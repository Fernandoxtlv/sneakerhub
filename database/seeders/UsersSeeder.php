<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        // Owner
        $owner = User::create([
            'name' => 'Dueño SneakerHub',
            'email' => 'owner@sneakerhub.com',
            'password' => Hash::make('password'),
            'phone' => '+51 999 111 111',
            'address' => 'Av. Principal 123',
            'city' => 'Lima',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $owner->assignRole('owner');

        // Admin
        $admin = User::create([
            'name' => 'Administrador Demo',
            'email' => 'admin@sneakerhub.com',
            'password' => Hash::make('password'),
            'phone' => '+51 999 222 222',
            'address' => 'Calle Admin 456',
            'city' => 'Lima',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        // Worker
        $worker = User::create([
            'name' => 'Trabajador Demo',
            'email' => 'worker@sneakerhub.com',
            'password' => Hash::make('password'),
            'phone' => '+51 999 333 333',
            'address' => 'Jr. Trabajo 789',
            'city' => 'Lima',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $worker->assignRole('worker');

        // Client
        $client = User::create([
            'name' => 'Cliente Demo',
            'email' => 'cliente@sneakerhub.com',
            'password' => Hash::make('password'),
            'phone' => '+51 999 444 444',
            'address' => 'Av. Cliente 321',
            'city' => 'Lima',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $client->assignRole('client');

        // Additional demo clients
        $clients = [
            ['name' => 'María García', 'email' => 'maria@example.com'],
            ['name' => 'José López', 'email' => 'jose@example.com'],
            ['name' => 'Ana Torres', 'email' => 'ana@example.com'],
        ];

        foreach ($clients as $clientData) {
            $user = User::create([
                'name' => $clientData['name'],
                'email' => $clientData['email'],
                'password' => Hash::make('password'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
            $user->assignRole('client');
        }
    }
}
