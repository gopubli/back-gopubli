<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Administrator;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar se já existe um admin
        $existingAdmin = Administrator::where('email', 'admin@gopubli.com')->first();

        if ($existingAdmin) {
            $this->command->info('Administrador padrão já existe!');
            return;
        }

        // Criar administrador padrão
        $admin = Administrator::create([
            'name' => 'Administrador GoPubli',
            'email' => 'admin@gopubli.com',
            'password' => Hash::make('admin123456'),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $this->command->info('Administrador padrão criado com sucesso!');
        $this->command->line('');
        $this->command->line('==================================================');
        $this->command->line('Email: admin@gopubli.com');
        $this->command->line('Senha: admin123456');
        $this->command->line('==================================================');
        $this->command->warn('⚠️  IMPORTANTE: Altere a senha após o primeiro login!');
        $this->command->line('');
    }
}
