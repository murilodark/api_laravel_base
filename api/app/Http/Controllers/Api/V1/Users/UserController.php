<?php

namespace App\Http\Controllers\Api\V1\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\User\UserFilterRequest;
use App\Http\Requests\V1\User\StoreUserRequest;
use App\Http\Requests\V1\User\UpdateUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Services\AuthService;
use Illuminate\Http\Request;

class UserController extends Controller
{

    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    /**
     * Listar usuários
     */
    public function index(UserFilterRequest $request)
    {
        $filter = $request->input('filter');
        $perPage = $request->input('per_page', 30); // Número de itens por página, padrão é 10
        $currentPage = $request->input('page', 1); // Página atual, padrão é 1

        $query = User::query();

        if ($filter) {
            $query->where(function ($q) use ($filter) {
                $q->where('name', 'LIKE', "%{$filter}%")
                    ->orWhere('email', 'LIKE', "%{$filter}%");

                // Verifica se o filtro é um número para buscar pelo ID
                if (is_numeric($filter)) {
                    $q->orWhere('id', $filter);
                }
            });
        }

        // Aplica a ordenação e a paginação
        $list_users = $query->orderBy('id', 'DESC')->paginate($perPage, ['*'], 'page', $currentPage);
        return $this->ReturnJson($list_users, 'Listagem de usuários', true, 200);
    }

    /**
     * Criar usuário
     */
    public function store(StoreUserRequest $request)
    {

        // 1. Valida se o usuário logado tem permissão para criar o perfil desejado (ACL Hierárquica)

        $this->authService->canManageUser(
            auth()->user()->tipo,
            $request->tipo
        );

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'status'   => $request->status,
            'tipo'     => $request->tipo,
        ]);

        return $this->ReturnJson(
            $user,
            'Usuário criado com sucesso.',
            true,
            201
        );
    }



    /**
     * Atualizar usuário
     */
    public function update(UpdateUserRequest $request, $user)
    {

        $userAlvo = User::findOrFail($user);
        $usuarioLogado = auth()->user();

        // 1. Validação de Escopo Atual:
        // Verifica se o logado tem "patente" para editar quem este usuário É no momento.
        $this->authService->canManageUser(
            $usuarioLogado->tipo,
            $userAlvo->tipo,
            $usuarioLogado->id,
            $userAlvo->id
        );

        // 2. Validação de Escopo de Destino (O "Pulo do Gato"):
        // Se o request contiver um novo 'tipo', verificamos se o logado tem permissão
        // para atribuir esse novo perfil a alguém.
        if ($request->has('tipo') && $request->tipo !== $userAlvo->tipo) {
            $this->authService->canManageUser(
                $usuarioLogado->tipo,
                $request->tipo,      // Validamos o NOVO perfil desejado
                $usuarioLogado->id,
                null                 // Passamos null pois não é auto-edição de cargo
            );
        }

        $validated = $request->validated();
        $userAlvo->update($validated);
        return $this->returnJson($userAlvo, 'Atualização efetuada com sucesso!', true, 200);
    }


    /**
     * Exibir usuário
     */
    public function show(User $user)
    {
        return $this->ReturnJson(
            $user,
            'Usuário encontrado.'
        );
    }

    /**
     * Remover usuário
     */
    public function destroy(User $user)
    {
        $user->delete();
        return $this->ReturnJson(
            null,
            'Usuário removido com sucesso.'
        );
    }


    /**
     * Importação de Clientes via CSV
     * 
     * A permissão de acesso é validada automaticamente pelo middleware 'check.permission:users'
     */
    public function uploadClientes(Request $request)
    {
        // 1. Validação técnica do arquivo (Mime-type e Tamanho)
        $request->validate([
            'arquivo' => [
                'required',
                'file',
                'mimes:csv,txt',
                'max:10240' // Limite de 10MB
            ],
        ], [
            'arquivo.mimes' => 'O arquivo enviado não é um CSV válido.',
            'arquivo.max'   => 'O arquivo excede o limite de segurança de 10MB.'
        ]);

        /** @var \App\Models\User $usuarioLogado */
        $usuarioLogado = auth()->user();

        // 2. Armazenamento Seguro
        // O arquivo é persistido temporariamente para leitura pelo Job
        $path = $request->file('arquivo')->store('uploads/csv');

        // 3. Delegação para Fila (Queue)
        // Despacha o processamento para não travar a resposta da API
        \App\Jobs\ProcessarUploadClientes::dispatch($path, $usuarioLogado);

        // 4. Resposta Assíncrona Padronizada (Status 202)
        return $this->ReturnJson(
            null,
            'A importação foi agendada. O sistema processará os dados e enviará um relatório detalhado para seu e-mail.',
            true,
            202
        );
    }
}
