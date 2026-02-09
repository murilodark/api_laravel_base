## 🔀 Versionamento de API

O versionamento é feito via URL:

/api/v1/public/...
/api/v1/private/...

Benefícios:

* Evoluir API sem quebrar clientes antigos
* Manter múltiplas versões em paralelo
* Organização clara do código

---

## 🔓 Rotas Públicas

* Não exigem autenticação
* Usadas para:
  * Login
  * Registro
  * Health check
  * Webhooks

Exemplo:

/api/v1/public/login

---

## 🔐 Rotas Privadas

* Protegidas por middleware
* Exigem autenticação
* Exigem permissão conforme perfil do usuário

O controle de acesso é simples e explícito — sem gambiarra.

--- 

## 🗂 Estrutura de Pastas

### Controllers

app/
└── Controllers/
    └── Api/
        └── V1/
            ├── BaseApiController.php
            ├── AuthController.php
            └── OutrosControllers...

* Cada versão da API tem sua própria pasta
* Controllers apenas de API
* Ideal ter um BaseApiController herdando o Trait de resposta

---

### Requests (Validações)

app/
└── Requests/
    └── V1/
        ├── LoginRequest.php
        ├── UserStoreRequest.php
        └── OutrosRequests...

* Validações organizadas por versão
* Nada de validação perdida dentro de controller

---

### Rotas

routes/
└── api/
    └── v1/
        ├── public/
        │   ├── auth.php
        │   └── health.php
        └── private/
            ├── users.php
            └── profile.php

---

## 📦 Padronização de Respostas JSON


Toda a construção de respostas é centralizada através do TraitReturnJsonOlirum.

Independentemente de a resposta representar **sucesso**, **erro de validação** ou **falha de permissão**, o cliente sempre receberá um objeto JSON com a **mesma anatomia**:

```json
{
  "data": {},        
  "message": "...",  
  "status": true,    
  "code": 200        
}
```

### 🔎 **Descrição dos Campos**

* **`data`**
  Payload da resposta (objeto, array ou `null`)

* **`message`**
  Mensagem explicativa destinada ao usuário ou ao desenvolvedor

* **`status`**
  Valor **booleano** indicando sucesso (`true`) ou falha (`false`) da operação

* **`code`**
  Código de status HTTP **redundante**, incluído no corpo para facilitar o parse no cliente

> ⚠️ **Atenção:** o campo `code` deve sempre refletir corretamente o status HTTP da resposta.

---

## 🧩 **Trait `TraitReturnJsonOlirum`**

O coração da padronização reside em:

```text
App\Traits\TraitReturnJsonOlirum
```

Este trait é responsável por garantir que **todas as respostas da API sigam exatamente o mesmo padrão**.

### 🔧 **Responsabilidades**

* **Integridade HTTP**
  Garante que o código de status presente no JSON seja **idêntico** ao código enviado no cabeçalho HTTP.

* **Sanitização**

  * Força codificação **UTF-8**
  * Garante que o campo **`status` seja estritamente booleano**

* **Versatilidade**
  Pode ser utilizado em:

  * **Controllers**
  * **Services**
  * **Actions**

Isso permite que a lógica de resposta permaneça **consistente em todas as camadas da aplicação**.

---

## 🔐 Autenticação

Autenticação baseada em tokens utilizando Laravel Sanctum.
Este projeto utiliza uma estratégia de autenticação **baseada em tokens**, **desacoplada** e **resiliente a ataques de força bruta**, garantindo **segurança**, **performance** e **previsibilidade** — sem complexidade desnecessária.

---

## 🛡️ **Autenticação com Laravel Sanctum**

O **`AuthController`** é responsável por gerenciar todo o **ciclo de vida da sessão do usuário**, aplicando múltiplas camadas de segurança.

---

### 🔒 **Rate Limiting Nativo**

Proteção integrada contra **ataques de força bruta**.

* **Bloqueio automático** de tentativas excessivas de login
* Controle por **IP e/ou e-mail**
* Limite padrão: **5 tentativas**

---

### ✅ **Verificação de Status do Usuário**

Durante o processo de login, o sistema valida se o usuário possui:

* **`status == 'ativo'`**

Usuários **inativos** têm o acesso **imediatamente negado**, mesmo após a validação correta das credenciais.

---

### 🔑 **Gestão de Tokens**

A autenticação utiliza **Plain Text Tokens** via **Sanctum**, permitindo:

* **Controle de sessões ativas**
* **Logout seguro**
* **Invalidação imediata de tokens comprometidos**

---

## 📋 **Resumo de Endpoints de Autenticação**

| Método | Endpoint      | Descrição                            | Proteção               |
| ------ | ------------- | ------------------------------------ | ---------------------- |
| POST   | `/api/login`  | Realiza login e retorna o token      | **Rate Limit (5 req)** |
| GET    | `/api/me`     | Retorna dados do usuário autenticado | **Sanctum**            |
| POST   | `/api/logout` | Revoga o token de acesso atual       | **Sanctum**            |

---


## 🔑 **Controle de Acesso (ACL por Matriz)**

Ao invés de sistemas **complexos e custosos** de permissões armazenadas em banco de dados, o projeto adota uma abordagem **simples**, **previsível** e **altamente performática**.

Controle de acesso centralizado no AuthService, baseado em matriz estática definida em config/permissions.php

---

### 📌 **Configuração Centralizada**

Todas as permissões são definidas em:

**`config/permissions.php`**

---

### 🔍 **Validação por Módulo e Método**

O acesso é validado comparando:

* **Tipo do usuário**
* **Módulo acessado** (ex: `users`)
* **Método da ação** (ex: `index`, `store`, `update`)

---

## 🛠️ **Middlewares Customizados**

---

### 🔄 **ForceJsonResponse**

Garante que **todas as respostas** — inclusive erros internos do framework — sejam retornadas em **formato JSON padronizado**.

---

### 🛡️ **check.permission**

Alias para o middleware responsável por:

* **Interceptar a requisição**
* **Consultar o `AuthService`**
* **Validar se o perfil do usuário possui permissão** para executar a ação solicitada no módulo

---

## 🚀 **Como Aplicar Permissões nas Rotas**

Graças à automação configurada no **`bootstrap/app.php`**, a aplicação de permissões nas rotas é **simples, explícita e padronizada**.

Basta utilizar o middleware **`check.permission`**, informando o **módulo** desejado.
O **método da ação** é identificado automaticamente pelo **`AuthService`**.

---

### 🧩 **Exemplo Prático**

Arquivo:

```text
routes/api/v1/private/users.php
```

```php
Route::prefix('users')->group(function () {

    // Apenas perfis com permissão 'index' no módulo 'users' acessam
    Route::get('/', [UserController::class, 'index'])
        ->middleware('check.permission:users');

    // Apenas perfis com permissão 'store' no módulo 'users' acessam
    Route::post('/', [UserController::class, 'store'])
        ->middleware('check.permission:users');

});
```

### 🔎 **Como funciona**

* O **módulo** (`users`) é informado no middleware
* O **método** (`index`, `store`, etc.) é inferido automaticamente
* O **tipo do usuário** é validado com base na matriz definida em `config/permissions.php`



