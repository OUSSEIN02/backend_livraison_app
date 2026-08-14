<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;


use App\Models\LivreurLocation;


class CleanupOldLocations extends Command
{
    protected $signature = 'locations:cleanup {--minutes=30}';
    protected $description = 'Nettoie les anciennes positions des livreurs';

    public function handle()
    {
        $minutes = (int) $this->option('minutes');
        $threshold = now()->subMinutes($minutes);
        
        $deleted = LivreurLocation::where('last_seen_at', '<', $threshold)->delete();

        $this->info("✅ {$deleted} anciennes positions supprimées (plus de {$minutes} min)");
        
        return 0;
    }
}