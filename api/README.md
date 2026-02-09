# 🚀 Base API Laravel

## Projeto Base para APIs REST

Este repositório é um **boilerplate opinativo em Laravel**, projetado **exclusivamente para a construção de APIs REST de alta performance**.

Toda a sobrecarga típica de aplicações web tradicionais foi removida (Views, Blade, middlewares de sessão, etc.) para focar no que realmente importa: **comunicação eficiente, previsível e escalável entre sistemas**.

A proposta é simples e direta: entregar uma **fundação limpa, organizada e pronta para produção**, eliminando o retrabalho de configurar, a cada novo projeto, os padrões que toda API moderna exige.

---

## 🎯 Objetivo do Projeto

Fornecer uma **estrutura padrão reutilizável**, garantindo **consistência arquitetural** e boas práticas desde o primeiro commit, com foco em:

* **Comunicação RESTful**
  Interação via requisições HTTP com um handler de exceções centralizado (ApiExceptionHandler), que intercepta falhas e as converte automaticamente para o formato JSON padronizado.

* **Padronização Estrita de Respostas (JSON)**
  Garantia de que 100% das respostas da API (sucesso ou erro) sigam um contrato único e previsível através do TraitReturnJsonOlirum, facilitando o consumo por qualquer cliente.

* **Segurança e Controle**
  Autenticação robusta com Sanctum ou Passport, além de separação rigorosa entre rotas públicas e protegidas.

  * **Tratamento de Exceções Centralizado**
  Centraliza o tratamento de exceções da aplicação, garantindo respostas padronizadas, controle de erros consistente e maior previsibilidade na comunicação entre API e clientes.

* **Processamento Assíncrono (Queues)**
  Infraestrutura pronta para delegar tarefas pesadas para segundo plano, garantindo respostas rápidas e alta performance sob carga.

* **Automação de Notificações**
  Sistema de agendamento integrado (Task Scheduling) para envio automático de e-mails, alertas e relatórios.


* **Versionamento Nativo**
  Estrutura preparada para suportar múltiplas versões da API (v1, v2, etc.) desde o primeiro dia.

* **Escalabilidade e Manutenibilidade**
  Arquitetura que separa claramente controladores, serviços e regras de negócio, facilitando a evolução do projeto a longo prazo.

---

## 🛠️ Funcionalidades Core

* **API Resourceful**
  Uso de API Resources do Laravel para transformação de dados e respostas consistentes.

* **Background Jobs**
  Suporte nativo a Laravel Queues para processamento em segundo plano (Database, Redis, etc.).

* **Scheduled Notifications**
  Agendamentos via Laravel Task Scheduler para disparo automático de notificações e rotinas recorrentes.

* **Email System**
  Configuração simplificada para envio de e-mails via SMTP ou serviços de terceiros como Mailgun e Postmark.

* **Clean Code Foundation**
  Estrutura organizada, previsível e sem "mágica" desnecessária, priorizando legibilidade, padronização e facilidade de manutenção.

---

> Este projeto serve como base sólida para APIs profissionais, reduzindo o tempo de setup e aumentando a confiabilidade desde o início.


---

## 🧱 Filosofia do Projeto

Alguns princípios seguidos aqui (sem frescura):

* 🔹 API não é site → **sem Blade, sem controllers web**
* 🔹 Tudo retorna JSON, sempre no mesmo formato
* 🔹 Organização de pastas importa (e muito)
* 🔹 Versionar API desde o dia zero evita dor de cabeça
* 🔹 Código fácil de entender hoje e daqui 2 anos

Tradição + organização = manutenção barata.

---

## 🗂 Estrutura de Pastas

### Controllers

```
app/
└── Controllers/
    └── Api/
        └── V1/
            ├── BaseApiController.php
            ├── AuthController.php
            └── OutrosControllers...
```

* Cada versão da API tem sua própria pasta
* Controllers apenas de API
* Ideal ter um `BaseApiController` herdando o Trait de resposta

---

### Requests (Validações)

```
app/
└── Requests/
    └── V1/
        ├── LoginRequest.php
        ├── UserStoreRequest.php
        └── OutrosRequests...
```

* Validações organizadas por versão
* Nada de validação perdida dentro de controller

---

### Rotas

```
routes/
└── api/
    └── v1/
        ├── public/
        │   ├── auth.php
        │   └── health.php
        └── private/
            ├── users.php
            └── profile.php
```

E o arquivo principal:

```
routes/api.php
```

Esse arquivo **carrega automaticamente** todas as rotas da API com base na estrutura de pastas.

---

## 🔀 Versionamento de API

O versionamento é feito via URL:

```
/api/v1/public/...
/api/v1/private/...
```

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

```
/api/v1/public/login
```

---

## 🔐 Rotas Privadas

* Protegidas por middleware
* Exigem autenticação
* Exige permissão da rota perfis de usuarios

```
Route::middleware('auth:sanctum')
```
e 

```
Route::middleware('check.permission')
```

Exemplo:

```
/api/v1/private/users
```

O controle de acesso é simples e explícito — sem gambiarra.

---

Perfeito. Segue o texto **formatado no padrão README.md**, com hierarquia clara, **destaques em negrito**, blocos de código e explicação objetiva — pronto para **anexar direto** ao seu README em andamento.

---

## 📦 **Padronização de Respostas JSON**

Para garantir uma comunicação **previsível e consistente** entre o backend e qualquer cliente (**Frontend**, **Mobile** ou **Integrações externas**), este projeto elimina respostas genéricas e inconsistentes.

Toda a construção de respostas é centralizada através do **`TraitReturnJsonOlirum`**, garantindo um padrão único em toda a aplicação.

---

## 🧱 **Estrutura Padrão de Resposta**

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

## 🚀 **Por que isso é importante?**

### ✅ **Consistência**

O frontend não precisa adivinhar se um erro virá como string, array ou objeto.
A estrutura é **sempre a mesma**.

---

### 🛠️ **Tratamento de Erros Simplificado**

Facilita a criação de **interceptadores globais**, por exemplo:

* Axios
* Fetch
* Clients Mobile

Erros podem ser tratados de forma **genérica e previsível**.

---

### 🧠 **Debug Acelerado**

Respostas limpas e padronizadas permitem:

* Identificar falhas de lógica rapidamente
* Reduzir tempo de troubleshooting
* Melhorar a qualidade geral da API

---


## 🔐 **Autenticação**

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

## 🔑 **Controle de Acesso (ACL por Matriz)**

Ao invés de sistemas **complexos e custosos** de permissões armazenadas em banco de dados, o projeto adota uma abordagem **simples**, **previsível** e **altamente performática**.

O controle de acesso é centralizado no **`AuthService`**, que lê uma **matriz de configuração estática**.

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

### 🧩 **Service Layer Dedicada**

Toda a lógica de autorização fica isolada no **`AuthService`**, facilitando:

* **Reutilização da lógica**
* **Manutenção do código**
* **Evolução do sistema sem impacto direto nas rotas**

---

## 🛣️ **Estrutura de Rotas e Middleware**

O arquivo **`bootstrap/app.php`** foi customizado para permitir uma organização **modular** e **automática** das rotas da API.

---

### 📁 **Organização de Arquivos**

As rotas **não ficam concentradas em um único arquivo**.
O sistema varre automaticamente os diretórios abaixo para registrar os endpoints.

---

#### 🔓 **Rotas Públicas**

```text
routes/api/v1/public/
```

Endpoints abertos, como:

* **Login**
* **Cadastro**

---

#### 🔐 **Rotas Privadas**

```text
routes/api/v1/private/
```

Endpoints protegidos pelo middleware **`auth:sanctum`**.

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

## 🧪 **Exemplo de Fluxo de Segurança**

1. O cliente realiza uma requisição para:

  **`routes/api/v1/private/users.php`** 

2. O Laravel aplica automaticamente o middleware:

  **`auth:sanctum`**

3. Após a autenticação, o middleware **`check.permission`** verifica se o tipo do usuário possui acesso ao método **`index`** do módulo **`users`**.

4. Caso o acesso seja negado, a **`ApiExceptionHandler`** captura a exceção e retorna um **erro JSON padronizado**, garantindo **consistência em toda a API**.

---
Perfeito. Abaixo está o texto **formatado no padrão README.md**, com títulos consistentes, destaques em **negrito**, código bem isolado e tabela organizada — pronto pra **anexar direto** no README que você já está montando.

👉 É copiar e colar, sem retrabalho.

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

---

## 📋 **Resumo de Endpoints de Autenticação**

| Método | Endpoint      | Descrição                            | Proteção               |
| ------ | ------------- | ------------------------------------ | ---------------------- |
| POST   | `/api/login`  | Realiza login e retorna o token      | **Rate Limit (5 req)** |
| GET    | `/api/me`     | Retorna dados do usuário autenticado | **Sanctum**            |
| POST   | `/api/logout` | Revoga o token de acesso atual       | **Sanctum**            |

---

### 📌 **Observações**

* Todos os endpoints protegidos utilizam **tokens via Sanctum**
* O logout invalida **apenas o token atual**
* As respostas seguem um **padrão JSON unificado**


---

## ⚠️ **Tratamento de Exceções Centralizado**

A robustez desta base reside na classe **`ApiExceptionHandler`**, que atua como um **filtro global** para qualquer erro que ocorra na aplicação.

Em vez de retornar telas de erro padrão do Laravel, o handler **intercepta as exceções** e as converte para o **formato JSON padronizado pelo projeto**, garantindo previsibilidade e segurança na comunicação com qualquer cliente.

---

## ⭐ **Principais Diferenciais**

### 🔀 **Isolamento de Ambiente (API vs Web)**

O handler identifica automaticamente requisições com prefixo:

```text
/api/*
```

* Requisições **API** retornam **JSON padronizado**
* Rotas **Web (Blade)** mantêm o comportamento padrão do Laravel
* Evita conflitos entre APIs e interfaces web

---

### 🗄️ **Tratamento Inteligente de Banco de Dados**

Possui lógica específica para capturar:

* **`UniqueConstraintViolationException`**

Quando um dado duplicado (como **CPF** ou **E-mail**) chega ao banco de dados:

* O campo violado é extraído diretamente da mensagem SQL
* Uma resposta **amigável e compreensível** é retornada
* Status HTTP apropriado: **409 (Conflict)**
* Nenhum erro técnico do banco é exposto ao cliente

---

### 🌐 **Mapeamento Correto de Status HTTP**

Exceções comuns do framework são automaticamente convertidas em **códigos HTTP semanticamente corretos**:

* **401** – Falha de autenticação via **Sanctum**
* **404** – Rota inexistente ou registro não encontrado (`ModelNotFound`)
* **405** – Uso de método HTTP não permitido
* **422** – Falhas de validação de dados (`FormRequest` ou `validate`)

---

## 🔐 **Segurança em Produção**

O comportamento do handler varia conforme o ambiente:

### 🧪 **Modo Debug**

* Retorna:

  * Mensagem real do erro
  * Arquivo e linha
  * Stack trace
* Facilita correção rápida durante o desenvolvimento

### 🏭 **Modo Produção**

* Detalhes técnicos **não são expostos** ao usuário
* O erro é **registrado integralmente nos logs** do sistema
* Mantém a API segura e profissional


---

## 🚀 **Como Usar**

Você **não precisa fazer nada**.

Basta:

* Lançar uma exceção manualmente
* Ou deixar que o Laravel lance automaticamente (ex: `$request->validate()`)

A **`ApiExceptionHandler`** cuidará de **interceptar, tratar e formatar** a resposta de forma automática e padronizada.


---

## 🔔 **Sistema de Notificações e Engajamento**

Este projeto utiliza o **Laravel Notifications** para manter administradores e stakeholders informados sobre **processos assíncronos**, **tarefas em background** e **métricas de crescimento da aplicação**.

As notificações estão preparadas para os seguintes canais:

* 📧 **E-mail** (SMTP / Mailgun)
* 🗄️ **Banco de Dados** (histórico persistido via `notifications` table)

Essa abordagem garante rastreabilidade, feedback claro e tomada de decisão rápida — do jeito que sistemas robustos sempre funcionaram.

---

## 📧 **Relatórios de Importação (Assíncrono)**

Gerenciado pela classe:

* `ClientesImportadosNotification`

A notificação é disparada automaticamente após o processamento de planilhas **CSV** pelo Job:

* `ProcessarUploadClientes`

---

## 🛠️ **Estrutura de Comandos (CLI)**
Diferente de agendamentos comuns, nossas rotinas de notificação são isoladas em classes de comando dedicadas para garantir testabilidade e execução manual via terminal. Essas rotinas são identificadas automaticamente pelo laravel e armarzena em kernel

Localização: app/Console/Commands/

## 📅 **Agendamento de Tarefas (Crontab)**

O projeto utiliza o sistema de agendamento centralizado do Laravel definido em:

```
routes/console.php
```

### **Configuração no Servidor**
Os serviços de notificação já estão configurados no docker-compose.yml

## 🛠️ **Como Testar Notificações Localmente**

### 📬 **Mailtrap (Ambiente de Testes)**

* Configure as credenciais do **Mailtrap** no arquivo `.env`
* Ideal para capturar e-mails sem envio real

### ▶️ **Execução Manual**

É possível disparar o relatório semanal manualmente via terminal dentro do container diretorio api:
** docker exec -it laravel_api_pescala-app-1 bash
** cd api
```bash
php artisan relatorio:clientes-semanal
```
---

## 🌐 **Endereços de Acesso (2025)**
Serviço	URL	Descrição
API	http://localhost:8989/api	Api de requisição
Mailpit	http://localhost:8025	Verificação de e-mails enviados pela fila