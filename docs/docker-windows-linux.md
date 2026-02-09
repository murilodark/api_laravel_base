# 🐳 Ambiente Docker para Laravel (Windows)

Este repositório fornece uma **estrutura base de ambiente Docker** para projetos Laravel, focada em **desenvolvimento no Windows**, com **mapeamento completo de diretórios**, serviços essenciais já configurados e **processos críticos iniciando automaticamente**.

A ideia aqui é simples e tradicional:
👉 subir o projeto, codar e trabalhar — sem brigar com ambiente.

---

## 🎯 Objetivo do Ambiente

Este setup Docker tem como objetivo:

* Padronizar o ambiente de desenvolvimento Laravel
* Facilitar o uso no **Windows** com volumes mapeados
* Criar uma base reutilizável para novos projetos
* Separar responsabilidades (app, filas, scheduler, web, banco, cache)
* Garantir que **queues e schedules rodem automaticamente**
* Evitar dependência de serviços locais (MySQL, Redis, PHP)

Tudo isso usando containers, do jeito certo, como sempre foi feito.

---

## 🧱 Serviços Disponíveis

O ambiente é composto pelos seguintes containers:

### 🧩 app (PHP / Laravel)

* Container principal da aplicação
* Usa `Dockerfile` customizado
* Diretório `/var/www` mapeado com o projeto local
* Ideal para executar comandos Artisan, migrations, seeders etc.

---

### ⚙️ queue (Processador de Filas)

* Executa automaticamente:

  ```bash
  php artisan queue:work
  ```
* Responsável por:

  * Jobs
  * Filas assíncronas
  * Importações (ex: CSV, processamento pesado)

Sem gambiarras de cron — processo vivo e estável.

---

### ⏰ scheduler (Agendador de Tarefas)

* Executa automaticamente:

  ```bash
  php artisan schedule:work
  ```
* Responsável por:

  * Tarefas agendadas
  * Relatórios
  * Rotinas automáticas

Funciona como um **cron containerizado**, do jeito correto.

---

### 🌐 nginx (Servidor Web)

* Servidor HTTP do projeto
* Porta exposta:

  ```
  http://localhost:8989
  ```
* Configurações personalizadas via:

  ```
  ./docker/nginx/
  ```
* Aponta para o container `app`

Separação clássica: Nginx não roda PHP, só serve.

---

### 🛢️ db (MySQL 8)

* Banco de dados MySQL 8.0
* Porta exposta:

  ```
  3305 → 3306
  ```
* Dados persistidos em:

  ```
  ./docker/mysql
  ```
* Configuração via variáveis de ambiente (`.env`)

Nada de perder dados ao derrubar container.

---

### ⚡ redis

* Redis para:

  * Cache
  * Filas
  * Locks
* Integrado automaticamente ao Laravel

Simples, rápido e eficiente — como Redis sempre foi.

---

### ✉️ mailpit (E-mails em ambiente local)

* Captura e-mails enviados pela aplicação
* Interface web:

  ```
  http://localhost:8025
  ```
* SMTP:

  ```
  host: mailpit
  port: 1025
  ```

Ideal para testar notificações e e-mails sem risco.

---

## 🔗 Redes Docker

O ambiente utiliza duas redes:

* `laravel`
  Rede interna entre os serviços do projeto

* `rede_docker_olicode` (externa)
  Permite integração com outros containers/projetos já existentes
  Substitua-o pela a rede que deseja integração

Isso facilita ambientes compartilhados e arquiteturas maiores.

---

## 📁 Mapeamento de Diretórios

O projeto local é mapeado diretamente para dentro dos containers:

```
./ → /var/www
```

Benefícios:

* Código editado no Windows reflete instantaneamente no container
* Compatível com VS Code, PhpStorm, etc.
* Sem necessidade de rebuild a cada alteração

---

## ▶️ Como Subir o Ambiente

1. Configure o arquivo `.env` do Laravel
2. Certifique-se de que a rede externa existe:

   ```bash
   docker network create rede_docker_olicode
   ```
3. Suba os containers:

   ```bash
   docker-compose up -d --build
   ```

Pronto.
Laravel, filas, scheduler, banco, cache e e-mails já estarão rodando.

---

## 🧠 Observações Importantes

* Este ambiente é voltado para **desenvolvimento**
* Filas e schedules **não dependem de acesso manual**
* Estrutura pensada para crescer sem refatorar Docker
* Ideal como base padrão para múltiplos projetos Laravel

---

## ✅ Conclusão

Este setup entrega o que um ambiente Laravel precisa de verdade:

* previsibilidade
* separação de responsabilidades
* automação
* e zero dor de cabeça