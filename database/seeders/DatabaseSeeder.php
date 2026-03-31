<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\DocumentCategory;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Superadmin
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@sinergipas.test',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
        ]);

        // Create Default Categories
        $categories = ['Slip Gaji', 'SKP', 'SK Kenaikan Pangkat', 'Dokumen Pribadi'];
        foreach ($categories as $category) {
            DocumentCategory::create([
                'name' => $category,
                'slug' => Str::slug($category),
            ]);
        }
    }
}
