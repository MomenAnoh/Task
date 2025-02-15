<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء 10 مستخدمين عاديين
        User::factory(10)->create([
            'user_type' => 'user', // مستخدم عادي
        ]);

        // إنشاء 5 مندوبين توصيل
        User::factory(5)->create([
            'user_type' => 'delivery', // مندوب توصيل
        ]);

        // إنشاء مستخدم إداري (Admin)
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'phone' => '01012345678',
            'latitude' => '30.0444',
            'longitude' => '31.2357',
            'profile_image' => 'admin.jpg',
            'password' => Hash::make('password'),
            'user_type' => 'admin',
            'remember_token' => Str::random(10),
        ]);
    }
}
