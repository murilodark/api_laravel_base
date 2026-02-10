# 🐳 Ambiente Docker para Laravel 

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
  php api/artisan queue:work
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
  php api/artisan schedule:work
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

---

## 🚀 Clone e Permissões

Clone o projeto e ajuste as permissões para o usuário do container (`www-data`):

```bash
git clone https://github.com/murilodark/api_laravel_base.git
cd api_laravel_base

sudo chown -R $USER:www-data .
sudo chmod -R 775 api/storage api/bootstrap/cache
```

---

## ⚙️ Configuração de Ambiente

Crie o arquivo `.env` dentro da pasta da API:

```bash
cp api/.env.example api/.env
```

> **Nota:**
> Certifique-se de que `DB_HOST=db` esteja configurado corretamente no seu `.env`.

---

## 🐳 Subindo os Containers

Inicie os containers Docker:

```bash
docker compose up -d
```

---

## 📦 Dependências e Banco de Dados

Aguarde o MySQL iniciar (aprox. **30 segundos**) e execute os comandos abaixo dentro do container:

```bash
# Instalar dependências PHP
docker compose exec app composer install --working-dir=/var/www/api

# Gerar chave da aplicação
docker compose exec app php api/artisan key:generate

# Executar migrations e seeders
docker compose exec app php api/artisan migrate --seed
```

Clássico Laravel raiz, sem gambiarra 👌

---

## 🧱 Serviços Disponíveis

| Serviço   | Porta (Windows) | Descrição                                                                 |
| --------- | --------------- | ------------------------------------------------------------------------- |
| Nginx     | 8989            | Servidor Web — [http://localhost:8989](http://localhost:8989)             |
| App       | —               | Container PHP-FPM (execução da API)                                       |
| Queue     | —               | Processador de filas (`queue:work`) automático                            |
| Scheduler | —               | Agendador de tarefas (`schedule:work`) automático                         |
| MySQL     | 3305            | MySQL 8.0 (persistido em `./docker/mysql`)                                |
| Redis     | 6379            | Cache e Session Driver                                                    |
| Mailpit   | 8025            | Interface Web de E-mails — [http://localhost:8025](http://localhost:8025) |

---

👉 **Opinião sincera:**
Esse setup está **bem tradicional e correto**, do jeito que Docker + Laravel sempre deveriam ser. Fácil de subir, fácil de manter e fácil de explicar pra qualquer dev que entrar no projeto. Se todo README fosse assim, o mundo seria um lugar melhor 😄

Se quiser, posso:

* Padronizar isso com badges (Docker, Laravel, MySQL)
* Criar uma seção de **Troubleshooting**
* Ou alinhar esse README com o padrão da sua **Base API Laravel** 👌

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