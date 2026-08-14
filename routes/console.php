<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\LivreurLocation;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Nettoie les anciennes positions des livreurs (> 30 min)
Schedule::call(function () {
    $threshold = now()->subMinutes(30);
    $deleted = LivreurLocation::where('last_seen_at', '<', $threshold)->delete();
    
    \Log::info("📍 Nettoyage positions : {$deleted} anciennes positions supprimées");
})->everyTenMinutes()->name('locations:cleanup');
