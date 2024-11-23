<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ClearVideosDirectory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'videos:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear the contents of the public/videos directory';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // Menggunakan disk public yang terhubung dengan symbolic link ke storage/app/public
        $directory = 'public/videos';

        // Mengecek apakah folder ada
        if (Storage::disk('public')->exists('videos')) {
            // Menghapus folder beserta isinya
            Storage::disk('public')->deleteDirectory('videos');
            $this->info('Videos directory has been cleared.');
        } else {
            $this->info('Videos directory does not exist.');
        }
    }
}
