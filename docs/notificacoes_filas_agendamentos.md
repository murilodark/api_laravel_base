# 🔔 Sistema de Notificações, Filas e Agendamentos

Este projeto implementa um **sistema completo de notificações e engajamento**, apoiado em **Jobs, Filas, Commands e Scheduler do Laravel**, garantindo processamento assíncrono, escalabilidade e feedback claro ao usuário.

A arquitetura foi pensada como **base educacional e reutilizável**, servindo tanto para o fluxo atual quanto para **qualquer outro tipo de processamento pesado no futuro**.

---

## 🎯 Objetivos do Sistema

* Processar tarefas pesadas fora do request HTTP
* Evitar travamento da API
* Garantir feedback ao usuário após processamento
* Centralizar notificações e relatórios
* Servir como **modelo real de uso de filas no Laravel**

Tradição boa: request rápido, processamento em background e notificação no final. Sempre foi assim — e sempre funcionou.

---

## 🔄 Arquitetura Geral

O sistema é composto por:

* **Filas (Queue)** → Processamento assíncrono
* **Jobs** → Execução do trabalho pesado
* **Notifications** → Feedback ao usuário
* **Commands (CLI)** → Rotinas executáveis e agendáveis
* **Scheduler** → Automação via cron

---

## 📦 Sistema de Filas e Jobs

### 🧵 Fila de Processamento de Upload de Clientes

O upload de clientes via CSV **não é processado diretamente na requisição**.

Fluxo correto:

1. Usuário envia o arquivo
2. A API despacha um **Job**
3. O Job entra na **fila**
4. O Worker processa linha a linha
5. Ao final, uma **notificação é enviada**

Essa abordagem:

* Evita timeout
* Reduz uso de memória
* Escala facilmente
* Serve de base para outros tipos de processamento (importações, integrações, relatórios, etc.)

---

### ⚙️ Job de Processamento

Classe responsável:

```
App\Jobs\ProcessarUploadClientes
```

Responsabilidades do Job:

* Ler CSV com `LazyCollection` (baixo consumo de memória)
* Validar tipo de usuário
* Tratar duplicidades diretamente no banco
* Contabilizar sucessos e falhas
* Remover arquivo após processamento
* Notificar o usuário solicitante ao final

Este Job implementa:

```
ShouldQueue
```

---

## 📤 Upload de Arquivo para Processamento em Fila

O sistema disponibiliza um endpoint específico para **upload de clientes via arquivo CSV**, cujo processamento ocorre **exclusivamente de forma assíncrona**, utilizando **Jobs e Filas do Laravel**.


### 🌐 Endpoint de Upload

```
POST /api/users/uploadcliente
```

Ao receber o arquivo, o controller **despacha o Job `ProcessarUploadClientes`**, que será executado pela fila configurada no projeto.

---

## 📄 Formato do Arquivo CSV

O arquivo deve obrigatoriamente seguir o padrão abaixo:

###  Nome do campo 

```
arquivo
```

### 🧾 Cabeçalho

```
name,email,password,status,tipo
```

### 📌 Exemplo de Conteúdo

```csv
name,email,password,status,tipo
Jose Silva,jose.silva@email.com,password123,ativo,cliente
Maria Oliveira,maria.oliveira@email.com,password123,ativo,cliente
Admin Invasor,admin.tentativa@email.com,hacker123,ativo,admin
```

---

## ⚠️ Regras de Processamento

Durante a execução do Job, o sistema aplica as seguintes regras **de forma automática**:

### ✅ Registros Processados com Sucesso

* Apenas registros com:

  * `tipo = cliente`
  * `email` **não duplicado** no banco são inseridos normalmente

### 🚫 Registros Ignorados

* **Perfis diferentes de `cliente`**

  * Exemplo: `admin`, `gestor`, `root`

* **Registros duplicados**

  * E-mails já existentes no banco

Esses registros **não interrompem o processamento** — apenas são contabilizados.

---

## 📧 Notificação ao Final do Processamento

Ao término do Job, o sistema envia automaticamente uma **notificação por e-mail** ao usuário solicitante, contendo:

* ✅ Quantidade de clientes importados com sucesso
* ⚠️ Quantidade de registros duplicados
* 🚫 Quantidade de registros ignorados por perfil inválido

Essa notificação é enviada através da classe:

```
ClientesImportadosNotification
```

Garantindo feedback claro, auditável e profissional.

---

## 🧠 Observação Importante

Este fluxo de upload foi projetado como **modelo base** para:

* Importação de outros tipos de dados
* Integrações externas
* Processamentos pesados
* Migrações assistidas

Ou seja: **entendeu esse fluxo, entendeu filas no Laravel**.
Clássico, robusto e escalável — como manda o figurino.

---

## 🔔 Sistema de Notificações

O projeto utiliza o **Laravel Notifications**, com suporte aos seguintes canais:

* 📧 **E-mail** (SMTP / Mailpit / Mailtrap)
* 🗄️ **Banco de Dados** (`notifications` table)

Isso garante:

* Histórico persistido
* Auditoria
* Transparência para o usuário

---

### 📧 Relatório de Importação de Clientes

Notificação responsável:

```
ClientesImportadosNotification
```

Ela é disparada automaticamente ao final do Job `ProcessarUploadClientes`, informando:

* Total de clientes importados com sucesso
* Total de registros ignorados por perfil inválido
* Total de duplicidades detectadas

Feedback claro, objetivo e automático.

---

## 🕒 Pontos de Disparo das Notificações

As notificações podem ser disparadas de **três formas diferentes**, dependendo do cenário.

---

### 1️⃣ Ao Final do Processamento da Fila (Automático)

* Após o Job terminar
* Ideal para feedback de uploads e integrações
* Não depende de ação manual

Este é o fluxo mais comum e recomendado.

---

### 2️⃣ Por Agendamento (Scheduler)

O Laravel Scheduler é configurado em:

```
routes/console.php
```

Exemplo real utilizado no projeto:

```php
// Agenda o envio do relatório de crescimento da base de clientes
// Executa toda segunda-feira às 08:00 da manhã
Schedule::command('relatorio:clientes-semanal')->weeklyOn(1, '08:00');
```

Esse agendamento:

* Executa um Command
* O Command pode disparar notificações
* Não depende de requisição HTTP

---

### 3️⃣ Por Solicitação Manual (API)

Também é possível disparar notificações sob demanda via endpoint:

```
POST /solicitar-resumo
```

Rota:

```php
Route::post(
    '/solicitar-resumo',
    [NotificationController::class, 'solicitarResumoClientes']
);
```

Ideal para:

* Dashboards
* Botões de “Gerar relatório”
* Ações administrativas

---

## 🛠️ Estrutura de Comandos (CLI)

Os comandos são isolados em classes próprias, garantindo:

* Testabilidade
* Execução manual
* Uso via Scheduler

Localização padrão:

```
app/Console/Commands/
```

Esses comandos são automaticamente registrados no Kernel e podem ser executados via Artisan ou agendados.

---

## ▶️ Execução Manual de Comandos

Dentro do container da API:

```bash
docker exec -it laravel_api_pescala-app-1 bash
cd api
php artisan relatorio:clientes-semanal
```

Útil para:

* Testes
* Execução pontual
* Ambientes de homologação

---

## 🛠️ Como Testar Notificações Localmente

### 📬 Mailpit (Docker)

O projeto já vem configurado para uso do **Mailpit**, ideal para desenvolvimento local.

Nenhum e-mail real é enviado.

---

## 🌐 Endereços de Acesso (2025)

| Serviço | URL                                                    | Descrição                                  |
| ------- | ------------------------------------------------------ | ------------------------------------------ |
| API     | [http://localhost:8989/api](http://localhost:8989/api) | Endpoints da API                           |
| Mailpit | [http://localhost:8025](http://localhost:8025)         | Visualização de e-mails enviados pela fila |

---

## 🧱 Benefícios da Arquitetura

* Requests rápidos
* Processamento confiável
* Escalabilidade real
* Notificações automáticas
* Base sólida para novos fluxos assíncronos
