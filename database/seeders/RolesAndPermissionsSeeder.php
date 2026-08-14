<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use App\Models\Seller; // ou Vendeur selon le nom de ton modèle
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // ==========================================
        // 1. CRÉATION DES RÔLES
        // ==========================================
        $adminRole = Role::updateOrCreate(
            ['slug' => 'admin'],
            [
                'name' => 'Administrateur',
                'description' => 'Accès complet à la plateforme (Back-office)'
            ]
        );

        $vendeurRole = Role::updateOrCreate(
            ['slug' => 'vendeur'],
            [
                'name' => 'Vendeur',
                'description' => 'Commerçant ou vendeur en ligne gérant ses livraisons'
            ]
        );

        $livreurRole = Role::updateOrCreate(
            ['slug' => 'livreur'],
            [
                'name' => 'Livreur',
                'description' => 'Livreur indépendant acceptant et réalisant les missions'
            ]
        );

        // ==========================================
        // 2. CRÉATION DES PERMISSIONS
        // ==========================================
        $permissions = [
            // --- ADMINISTRATION ---
            'view-admin-dashboard'   => 'Voir le tableau de bord admin',
            'view-all-orders'        => 'Voir toutes les commandes de la plateforme',
            'manage-assignments'     => 'Gérer les attributions manuelles de livraisons',
            'manage-disputes'        => 'Gérer et résoudre les litiges',
            'view-sellers'           => 'Voir la liste des vendeurs',
            'approve-sellers'        => 'Valider les comptes et documents KYC des vendeurs',
            'edit-sellers'           => 'Modifier ou suspendre un compte vendeur',
            'view-couriers'          => 'Voir la liste des livreurs',
            'approve-couriers'       => 'Valider les comptes et documents KYC des livreurs',
            'edit-couriers'          => 'Modifier ou suspendre un compte livreur',
            'manage-zones-pricing'   => 'Gérer les zones de livraison et les grilles tarifaires',
            'view-statistics'        => 'Voir les statistiques et rapports avancés',
            'manage-finances'        => 'Gérer les transactions, commissions et retraits',
            'manage-system-settings' => 'Gérer les paramètres globaux du système',

            // --- VENDEUR ---
            'view-seller-dashboard'  => 'Voir le tableau de bord vendeur',
            'create-seller-order'    => 'Créer une demande de livraison',
            'view-my-orders'         => 'Voir l\'historique de mes commandes',
            'track-live-deliveries'  => 'Suivre les livraisons en temps réel (GPS)',
            'view-my-invoices'       => 'Consulter et télécharger mes factures',
            'manage-seller-api'      => 'Gérer les clés d\'intégration API',

            // --- LIVREUR ---
            'view-available-missions' => 'Voir les missions de livraison disponibles',
            'manage-mission-status'   => 'Accepter, refuser ou mettre à jour le statut d\'une mission',
            'view-my-earnings'        => 'Consulter mes gains et l\'historique de mes revenus',
        ];

        foreach ($permissions as $slug => $name) {
            Permission::updateOrCreate(
                ['slug' => $slug],
                [
                    'name'        => $name,
                    'description' => $name
                ]
            );
        }

        // ==========================================
        // 3. ASSIGNATION DES PERMISSIONS AUX RÔLES
        // ==========================================
        
        // L'administrateur a toutes les permissions (renvoie un tableau d'IDs pour sync)
        $adminRole->permissions()->sync(Permission::pluck('id'));

        // Le vendeur a des permissions limitées à son espace
        $vendeurPermissions = Permission::whereIn('slug', [
            'view-seller-dashboard',
            'create-seller-order',
            'view-my-orders',
            'track-live-deliveries',
            'view-my-invoices',
            'manage-seller-profile',
            'manage-seller-api'
        ])->pluck('id');
        $vendeurRole->permissions()->sync($vendeurPermissions);

        // Le livreur a des permissions limitées
        $livreurPermissions = Permission::whereIn('slug', [
            'view-available-missions',
            'manage-mission-status',
            'view-my-earnings'
        ])->pluck('id');
        $livreurRole->permissions()->sync($livreurPermissions);


        // ==========================================
        // 4. CRÉATION D'UTILISATEURS DE TEST
        // ==========================================
        
        // --- ADMIN DE TEST ---
        $admin = User::updateOrCreate(
            ['email' => 'admin@gabonlivraison.com'],
            [
                'name'     => 'Administrateur Principal',
                'password' => Hash::make('password123'),
            ]
        );
        $admin->roles()->sync([$adminRole->id]);

        // --- VENDEUR DE TEST ---
        // Step A: Création du User
        $vendeurUser = User::updateOrCreate(
            ['email' => 'vendeur@test.com'],
            [
                'name'     => 'Super Market Plus',
                'password' => Hash::make('password123'),
            ]
        );
        // Assignation du rôle vendeur sur le compte User
        $vendeurUser->roles()->sync([$vendeurRole->id]);

       

        $this->command->info('✅ Rôles, permissions et utilisateurs de test créés avec succès !');
    }
}