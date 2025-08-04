<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class PasetoKeyGenerate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'paseto:key-generate';


    /**
     * The console command description.
     *
     * @var string
     */
        protected $description = 'Generate a PASETO key for the application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $key = base64_encode(random_bytes(32));

            // Output yang benar
            $this->info("PASETO_KEY={$key}");
            $this->comment('Salin kunci di atas dan letakkan di file .env Anda.');

        } catch (\Exception $e) {
            $this->error('Gagal membuat kunci PASETO: ' . $e->getMessage());
        }
    }
}