<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Seller;
use App\Models\Livreur;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatistiquesController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->input('period', 'semaine');
        
        // 1. Définir la plage de dates
        $now = Carbon::now();
        if ($period === 'semaine') {
            $start = $now->copy()->startOfWeek();
            $prevStart = $now->copy()->subWeek()->startOfWeek();
            $groupFormat = '%w'; // Jour de la semaine (0-6)
            $labels = ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'];
        } elseif ($period === 'mois') {
            $start = $now->copy()->startOfMonth();
            $prevStart = $now->copy()->subMonth()->startOfMonth();
            $groupFormat = '%U'; // Semaine du mois
            $labels = ['Sem 1', 'Sem 2', 'Sem 3', 'Sem 4', 'Sem 5'];
        } else { // annee
            $start = $now->copy()->startOfYear();
            $prevStart = $now->copy()->subYear()->startOfYear();
            $groupFormat = '%m'; // Mois
            $labels = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
        }

        // 2. Statistiques de performance (avec comparaison période précédente)
        $currentOrders = Order::where('created_at', '>=', $start)->count();
        $prevOrders = Order::whereBetween('created_at', [$prevStart, $start])->count();
        $orderChange = $prevOrders > 0 ? round((($currentOrders - $prevOrders) / $prevOrders) * 100) : 0;

        $currentRevenue = Order::where('created_at', '>=', $start)->where('status', 'livree')->sum('total_amount');
        $prevRevenue = Order::whereBetween('created_at', [$prevStart, $start])->where('status', 'livree')->sum('total_amount');
        $revenueChange = $prevRevenue > 0 ? round((($currentRevenue - $prevRevenue) / $prevRevenue) * 100) : 0;

        $activeSellers = Seller::whereHas('orders', function($q) use ($start) {
            $q->where('created_at', '>=', $start);
        })->count();
        
        $activeDrivers = Livreur::whereHas('orders', function($q) use ($start) {
            $q->where('created_at', '>=', $start);
        })->count();

        $performanceStats = [
            [
                'icon' => 'ShoppingCart', // Le frontend mappe cela sur l'icône Lucide
                'label' => 'Commandes totales',
                'value' => number_format($currentOrders),
                'change' => ($orderChange >= 0 ? '+' : '') . $orderChange . '%',
                'up' => $orderChange >= 0,
                'detail' => abs($currentOrders - $prevOrders) . ' vs période précédente'
            ],
            [
                'icon' => 'DollarSign',
                'label' => 'Chiffre d\'affaires',
                'value' => number_format($currentRevenue, 0, ',', ' ') . ' FCFA',
                'change' => ($revenueChange >= 0 ? '+' : '') . $revenueChange . '%',
                'up' => $revenueChange >= 0,
                'detail' => number_format(abs($currentRevenue - $prevRevenue), 0, ',', ' ') . ' FCFA'
            ],
            [
                'icon' => 'Users',
                'label' => 'Vendeurs actifs',
                'value' => number_format($activeSellers),
                'change' => '+N/A', // Simplifié pour l'exemple
                'up' => true,
                'detail' => 'Ayant généré des commandes'
            ],
            [
                'icon' => 'Truck',
                'label' => 'Livreurs actifs',
                'value' => number_format($activeDrivers),
                'change' => '+N/A',
                'up' => true,
                'detail' => 'Ayant effectué des livraisons'
            ]
        ];

        // 3. Données du graphique en barres (Commandes par jour/semaine/mois)
        $chartDataRaw = Order::selectRaw("DATE_FORMAT(created_at, '{$groupFormat}') as period, COUNT(*) as total")
            ->where('created_at', '>=', $start)
            ->groupBy('period')
            ->orderBy('period', 'asc')
            ->pluck('total', 'period')
            ->toArray();

        // Remplir les trous pour avoir un tableau complet correspondant aux labels
        $commandesData = [];
        foreach ($labels as $index => $label) {
            // Adaptation simple de l'index pour correspondre au format MySQL (0=Dimanche, 1=Lundi, etc.)
            $key = $period === 'semaine' ? (string)$index : (string)($index + 1);
            $commandesData[] = $chartDataRaw[$key] ?? 0;
        }

        // 4. Données du graphique circulaire (Répartition par statut)
        $statusCounts = Order::where('created_at', '>=', $start)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $totalForPie = array_sum($statusCounts) ?: 1;
        $pieData = [
            ['label' => 'En attente', 'value' => round((($statusCounts['en_attente'] ?? 0) / $totalForPie) * 100), 'color' => '#f59e0b'],
            ['label' => 'En cours', 'value' => round((($statusCounts['en_cours'] ?? 0) + ($statusCounts['assignee'] ?? 0)) / $totalForPie * 100), 'color' => '#3b82f6'],
            ['label' => 'Terminées', 'value' => round((($statusCounts['livree'] ?? 0) / $totalForPie) * 100), 'color' => '#22c55e'],
            ['label' => 'Annulées', 'value' => round((($statusCounts['annulee'] ?? 0) + ($statusCounts['litige'] ?? 0)) / $totalForPie * 100), 'color' => '#ef4444']
        ];

        // 5. Top Vendeurs
        $topVendeurs = Seller::select('sellers.company_name as nom', DB::raw('COUNT(orders.id) as commandes'), DB::raw('SUM(orders.total_amount) as revenu'))
            ->join('orders', 'sellers.id', '=', 'orders.user_id') // Adaptez si votre foreign key est différente
            ->where('orders.created_at', '>=', $start)
            ->groupBy('sellers.id', 'sellers.company_name')
            ->orderByDesc('commandes')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return [
                    'nom' => $item->nom,
                    'commandes' => $item->commandes,
                    'revenu' => number_format($item->revenu, 0, ',', ' ') . ' FCFA',
                    'note' => '4.5', // À remplacer par une vraie moyenne si vous avez une table d'avis
                    'evolution' => '+N/A'
                ];
            });

        // 6. Top Livreurs
        $topLivreurs = Livreur::select('users.name as nom', DB::raw('COUNT(orders.id) as livraisons'))
            ->join('users', 'livreurs.user_id', '=', 'users.id')
            ->join('orders', 'livreurs.id', '=', 'orders.livreur_id')
            ->where('orders.created_at', '>=', $start)
            ->where('orders.status', 'livree')
            ->groupBy('livreurs.id', 'users.name')
            ->orderByDesc('livraisons')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return [
                    'nom' => $item->nom,
                    'livraisons' => $item->livraisons,
                    'note' => '4.8', // À remplacer par une vraie moyenne
                    'temps' => '30 min', // À calculer si vous avez les timestamps de début/fin
                    'taux' => '95%'
                ];
            });

        return response()->json([
            'message' => 'Statistiques récupérées avec succès.',
            'data' => [
                'performance_stats' => $performanceStats,
                'chart_data' => [
                    'labels' => $labels,
                    'commandes' => $commandesData,
                    'revenus' => [] // Vous pouvez ajouter la logique de revenus par période ici si nécessaire
                ],
                'pie_data' => $pieData,
                'top_vendeurs' => $topVendeurs,
                'top_livreurs' => $topLivreurs,
                'extra_stats' => [
                    ['label' => 'Taux de livraison', 'value' => '92%', 'change' => '+4%', 'up' => true, 'icon' => 'Target'],
                    ['label' => 'Litiges résolus', 'value' => '95%', 'change' => '+2%', 'up' => true, 'icon' => 'Award']
                ]
            ]
        ]);
    }
}