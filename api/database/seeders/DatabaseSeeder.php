<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder: DatabaseSeeder
 * 
 * Responsável por popular o banco de dados com usuários iniciais 
 * para teste das regras de ACL (Access Control List) e Hierarquia.
 * 
 * @author Murilo Dark
 * @date 2024-06-27 | Atualizado em 2026-02-07
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 👑 [ROOT] - Acesso Total e Superior a Todos
        // Este perfil ignora restrições de hierarquia no AuthService.
        User::factory()->create([
            'name'     => 'Desenvolvedor Root',
            'email'    => 'root@olirum.com',
            'password' => Hash::make('root123'),
            'tipo'     => 'root',
            'status'   => 'ativo',
        ]);

        // 🛡️ [ADMIN] - Gestor do Sistema
        // Pode gerenciar todos, exceto outros Admins e o Root.
        User::factory()->create([
            'name'     => 'Administrador Sistema',
            'email'    => 'admin@olirum.com',
            'password' => Hash::make('admin123'),
            'tipo'     => 'admin',
            'status'   => 'ativo',
        ]);

        // 👔 [GERENTE] - Gestor Operacional
        // Pode gerenciar Vendedores e Clientes.
        User::factory()->create([
            'name'     => 'Gerente Operacional',
            'email'    => 'gerente@olirum.com',
            'password' => Hash::make('password'),
            'tipo'     => 'gerente',
            'status'   => 'ativo',
        ]);

        // 💼 [VENDEDOR] - Operador Comercial
        // Pode gerenciar apenas Clientes e seu próprio perfil.
        User::factory()->create([
            'name'     => 'Vendedor Comercial',
            'email'    => 'vendedor@olirum.com',
            'password' => Hash::make('password'),
            'tipo'     => 'vendedor',
            'status'   => 'ativo',
        ]);

        // 👤 [CLIENTE] - Usuário Final
        // Não possui permissões de gestão de outros usuários.
        User::factory()->create([
            'name'     => 'Cliente Final',
            'email'    => 'cliente@olirum.com',
            'password' => Hash::make('password'),
            'tipo'     => 'cliente',
            'status'   => 'ativo',
        ]);

        // 🚫 [INATIVO] - Cenário de Teste de Bloqueio
        User::factory()->create([
            'name'     => 'Usuário Bloqueado',
            'email'    => 'inativo@olirum.com',
            'password' => Hash::make('password'),
            'tipo'     => 'cliente',
            'status'   => 'inativo',
        ]);
    }
}
