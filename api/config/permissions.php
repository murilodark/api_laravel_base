<?php

/**
 * Matriz de Permissões de Acesso (ACL)
 * 
 * Este arquivo centraliza todas as regras de acesso da plataforma,
 * mapeando os perfis de usuário aos seus respectivos módulos e métodos.
 * 
 * @date 2024-06-27
 */

return [

    'roles' => [
        'root' => [
            '*' => ['*'], // Root tem acesso irrestrito a todos os módulos e métodos
        ],
        'admin' => [
            'users'        => ['index', 'store', 'show', 'update',   'destroy', 'usersUploadCsv'],
            'notifications' => ['solicitarResumoClientes'],
            'fornecedores' => ['index', 'store', 'show', 'update', 'destroy', 'produtosUploadCsv'],
            'produtos'     => ['index', 'store', 'show', 'update', 'destroy'],
            'pedidos'      => ['index', 'store', 'show', 'update', 'destroy', 'listarPorFornecedor', 'dispararReportDiario', 'obterEstatisticas'],
        ],
        'gerente' => [
            'users'        => ['index', 'store', 'show', 'update', 'usersUploadCsv'],
            'notifications' => ['solicitarResumoClientes'],
            'fornecedores' => ['index', 'store', 'show', 'update', 'destroy', 'produtosUploadCsv'],
            'produtos'     => ['index', 'store', 'show', 'update', 'destroy'],
            'pedidos'      => ['index', 'store', 'show', 'update', 'destroy', 'listarPorFornecedor', 'dispararReportDiario', 'obterEstatisticas'],
        ],
        'vendedor' => [
            'users'        => ['index', 'store', 'show', 'update'],
            'produtos'     => ['index', 'show'],
            'pedidos'      => ['store', 'show', 'listarPorFornecedor', 'meusPedidos'],
            'fornecedores' => ['index', 'listaProdutos', 'listarPorFornecedor'],
        ],
        'cliente' => [
            'produtos'     => ['index', 'show'],
            'pedidos'      => ['meusPedidos'],
            'fornecedores' => ['index', 'listaProdutos', 'listarPorFornecedor'],
        ]
    ]

];
