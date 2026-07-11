<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanupOfflinePackages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup-offline-packages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $files = glob(
            storage_path('app/offline/*.zip')
        );

        foreach ($files as $file) {

            if (
                filemtime($file)
                <
                now()->subDays(7)->timestamp
            ) {
                unlink($file);
            }
        }
    }
}
