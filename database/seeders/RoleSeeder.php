<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Menjalankan Database Seeder untuk akun pengguna User dan Admin.
     */
    public function run(): void
    {
        DB::table('roles')->insertOrIgnore([
            ['name' => 'admin', 'description' => 'Administator semua Akses', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'user', 'description' => 'Regular user', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}




?>
