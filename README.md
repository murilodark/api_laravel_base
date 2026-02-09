
# 🚀 Base API Laravel

## Boilerplate Profissional para APIs REST

Este repositório fornece uma **base opinativa em Laravel**, projetada **exclusivamente para a construção de APIs REST de alta performance**, com foco em **padronização, previsibilidade e escalabilidade**.

Toda a sobrecarga típica de aplicações web tradicionais foi removida (Views, Blade, middlewares de sessão, etc.), mantendo apenas o que uma API moderna realmente precisa: **comunicação clara, contratos bem definidos e infraestrutura pronta para produção**.

A proposta é simples:
👉 eliminar o retrabalho de configuração
👉 garantir consistência arquitetural
👉 acelerar o início de novos projetos

---

## 🎯 Objetivo do Projeto

Fornecer uma **fundação reutilizável e bem definida** para APIs REST, garantindo boas práticas desde o primeiro commit, com foco em:

* Comunicação RESTful padronizada
* Tratamento centralizado de exceções
* Respostas JSON previsíveis
* Segurança e controle de acesso
* Processamento assíncrono
* Automação de tarefas e notificações
* Estrutura preparada para versionamento de API

Tudo isso já integrado a um **ambiente Docker completo**, pronto para desenvolvimento no Windows ou Linux.

---

## 🧱 Estrutura Geral do Projeto

```
.
├── api/                  # Aplicação Laravel (API)
├── docker/               # Configurações do Docker
│   ├── mysql/            # Volume e dados do MySQL
│   ├── nginx/            # Configurações do Nginx
│   └── php/              # Configurações do PHP
├── docs/                 # Documentações do projeto
│   ├── base_arquitetura.md
│   ├── base_laravel.md
│   └── docker-windows-linux.md
├── Dockerfile
├── docker-compose.yml
└── README.md
```

---

## 🐳 Ambiente Docker

O projeto utiliza **Docker Compose** para fornecer um ambiente completo e isolado, incluindo:

* PHP (Laravel)
* Nginx
* MySQL
* Redis
* Mailpit (captura de e-mails)
* Queue Worker (filas)
* Scheduler (tarefas agendadas)

Filas e agendamentos **sobem automaticamente**, sem necessidade de execução manual.

👉 A documentação completa do ambiente Docker está disponível em:
📄 **[Docker (Windows e Linux)](docs/docker-windows-linux.md)**

---

## 🛠️ Principais Características da Base

* **API-only**
  Estrutura enxuta, sem camadas web desnecessárias.

* **Tratamento de Exceções Centralizado**
  Todas as falhas são interceptadas por um handler global e convertidas para respostas JSON padronizadas.

* **Padronização Estrita de Respostas**
  100% das respostas seguem um contrato único e previsível.

* **Autenticação e Segurança**
  Pronta para uso com Laravel Sanctum ou Passport.

* **Filas e Jobs**
  Infraestrutura preparada para processamento assíncrono com Redis ou Database.

* **Agendamentos Automatizados**
  Task Scheduler ativo para relatórios, notificações e rotinas recorrentes.

* **Versionamento de API**
  Estrutura preparada para múltiplas versões (v1, v2, etc.).

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

## 📚 Documentação

A documentação detalhada do projeto está organizada no diretório `docs/`:

* 🐳 **Docker (Windows e Linux)**
  📄 [docker-windows-linux.md](docs/docker-windows-linux.md)  
  Guia para configuração e uso do ambiente Docker em diferentes sistemas operacionais.

* 🧱 **Arquitetura da Base**
  📄 [base_arquitetura.md](docs/base_arquitetura.md)  
  Visão geral da arquitetura, organização de camadas e decisões estruturais do projeto.

* 🐘 **Tratamento de Exceções Centralizado**
  📄 [tratamento_excecoes.md](docs/tratamento_excecoes.md)  
  Estrutura de tratamento global de exceções, padronização de respostas JSON e mapeamento de erros HTTP.

* 🔔 **Notificações, Filas e Agendamentos**
  📄 [notificacoes_filas_agendamentos.md](docs/notificacoes_filas_agendamentos.md)  
  Uso de filas, jobs, notificações e agendamentos para processamento assíncrono e automações da aplicação.

---

## 🚀 Para Quem Este Projeto é Indicado

* Desenvolvedores que constroem **APIs profissionais**
* Times que precisam de **padronização entre projetos**
* Projetos que exigem **escalabilidade e manutenção a longo prazo**
* Quem quer subir um ambiente completo sem reinventar a roda

---

## 🏁 Considerações Finais

Este projeto não tenta ser genérico nem mágico.
Ele entrega uma **base sólida, previsível e madura**, pronta para crescer sem refatorações traumáticas.

Menos improviso.
Mais estrutura.
Como boas APIs sempre deveriam nascer.

---

Projeto idealizado e mantido por Murilo Dark.
