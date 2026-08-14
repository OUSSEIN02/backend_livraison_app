<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission; // Assurez-vous que ce modèle existe

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // ==========================================
            // 1. PERMISSIONS ADMIN (Back-office)
            // ==========================================
            [
                'name' => 'Voir le tableau de bord admin',
                'slug' => 'view-admin-dashboard',
                'description' => 'Accès à la vue d\'ensemble et aux statistiques globales de la plateforme.',
            ],
            
            // Gestion des Commandes & Exploitation
            [
                'name' => 'Voir toutes les commandes',
                'slug' => 'view-all-orders',
                'description' => 'Consulter la liste de toutes les commandes de la plateforme.',
            ],
            [
                'name' => 'Créer une commande (Admin)',
                'slug' => 'create-order',
                'description' => 'Créer manuellement une commande pour le compte d\'un client ou vendeur.',
            ],
            [
                'name' => 'Gérer les attributions manuelles',
                'slug' => 'manage-assignments',
                'description' => 'Attribuer manuellement une commande à un livreur spécifique.',
            ],
            [
                'name' => 'Gérer les litiges',
                'slug' => 'manage-disputes',
                'description' => 'Consulter, médier et résoudre les litiges entre vendeurs, livreurs et clients.',
            ],

            // Gestion des Utilisateurs (Vendeurs & Livreurs)
            [
                'name' => 'Voir les vendeurs',
                'slug' => 'view-sellers',
                'description' => 'Consulter la liste des vendeurs inscrits.',
            ],
            [
                'name' => 'Valider les comptes vendeurs (KYC)',
                'slug' => 'approve-sellers',
                'description' => 'Approuver ou rejeter les documents d\'identité et d\'entreprise des vendeurs.',
            ],
            [
                'name' => 'Modifier/Suspendre les vendeurs',
                'slug' => 'edit-sellers',
                'description' => 'Modifier les informations ou suspendre un compte vendeur.',
            ],
            [
                'name' => 'Voir les livreurs',
                'slug' => 'view-couriers',
                'description' => 'Consulter la liste des livreurs inscrits.',
            ],
            [
                'name' => 'Valider les comptes livreurs (KYC)',
                'slug' => 'approve-couriers',
                'description' => 'Approuver ou rejeter les documents d\'identité et de véhicule des livreurs.',
            ],
            [
                'name' => 'Modifier/Suspendre les livreurs',
                'slug' => 'edit-couriers',
                'description' => 'Modifier les informations ou suspendre un compte livreur.',
            ],

            // Configuration & Système
            [
                'name' => 'Gérer les zones et tarifs',
                'slug' => 'manage-zones-pricing',
                'description' => 'Créer, modifier ou supprimer des zones de livraison et leurs grilles tarifaires.',
            ],
            [
                'name' => 'Voir les statistiques avancées',
                'slug' => 'view-statistics',
                'description' => 'Accéder aux rapports détaillés de performance de la plateforme.',
            ],
            [
                'name' => 'Gérer les finances et paiements',
                'slug' => 'manage-finances',
                'description' => 'Consulter les transactions, commissions et valider les retraits des vendeurs/livreurs.',
            ],
            [
                'name' => 'Gérer les paramètres système',
                'slug' => 'manage-system-settings',
                'description' => 'Modifier les configurations globales de l\'application.',
            ],

            // ==========================================
            // 2. PERMISSIONS VENDEUR (Dashboard Vendeur)
            // ==========================================
            [
                'name' => 'Voir le tableau de bord vendeur',
                'slug' => 'view-seller-dashboard',
                'description' => 'Accès au résumé de l\'activité du vendeur.',
            ],
            [
                'name' => 'Créer une commande de livraison',
                'slug' => 'create-seller-order',
                'description' => 'Soumettre une nouvelle demande de livraison.',
            ],
            [
                'name' => 'Voir mes commandes',
                'slug' => 'view-my-orders',
                'description' => 'Consulter l\'historique et l\'état de ses propres commandes.',
            ],
            [
                'name' => 'Suivre les livraisons en temps réel',
                'slug' => 'track-live-deliveries',
                'description' => 'Accéder à la géolocalisation en direct des livraisons en cours.',
            ],
            [
                'name' => 'Voir mes factures',
                'slug' => 'view-my-invoices',
                'description' => 'Consulter et télécharger les factures des services de livraison.',
            ],
            [
                'name' => 'Gérer mon profil vendeur',
                'slug' => 'manage-seller-profile',
                'description' => 'Modifier ses informations d\'entreprise, de contact et de mot de passe.',
            ],
            [
                'name' => 'Gérer les intégrations API',
                'slug' => 'manage-seller-api',
                'description' => 'Générer et révoquer des clés API pour connecter son propre site e-commerce.',
            ],

            // ==========================================
            // 3. PERMISSIONS LIVREUR (Pour futur usage)
            // ==========================================
            [
                'name' => 'Voir les missions disponibles',
                'slug' => 'view-available-missions',
                'description' => 'Consulter la liste des commandes à livrer.',
            ],
            [
                'name' => 'Accepter/Refuser une mission',
                'slug' => 'manage-mission-status',
                'description' => 'Changer le statut d\'une mission (acceptée, en cours, livrée).',
            ],
            [
                'name' => 'Voir mes gains',
                'slug' => 'view-my-earnings',
                'description' => 'Consulter le solde et l\'historique des revenus.',
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['slug' => $permission['slug']], // Évite les doublons en cherchant par slug
                [
                    'name' => $permission['name'],
                    'description' => $permission['description'],
                ]
            );
        }

        $this->command->info('Permissions insérées avec succès !');
    }
}