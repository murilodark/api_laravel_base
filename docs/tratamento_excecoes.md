# ⚠️ Tratamento de Exceções Centralizado

Esta base adota um **Handler de Exceções totalmente centralizado**, garantindo que **toda a API responda sempre em JSON**, com mensagens claras, seguras e semanticamente corretas — sem vazar erro técnico nem comportamento inesperado.

O coração dessa estratégia é a classe:

```
App\Exceptions\ApiExceptionHandler
```

Ela é registrada diretamente no bootstrap da aplicação e atua como um **filtro global de erros para rotas `api/*`**.

---

## 🎯 Objetivos da Estratégia

* Padronizar **todas** as respostas de erro da API
* Garantir **códigos HTTP corretos**
* Evitar exposição de erros internos em produção
* Centralizar a lógica (sem `try/catch` espalhado pelo sistema)
* Manter compatibilidade total com **Laravel + Sanctum**

---

## 🧠 Como Funciona na Prática

A classe `ApiExceptionHandler` é registrada no bootstrap da aplicação:

```php
->withExceptions(new ApiExceptionHandler())
```

A partir disso:

* Toda requisição para `api/*` **força resposta JSON**
* O Laravel continua tratando rotas Web normalmente
* Exceções comuns são interceptadas e convertidas em respostas padronizadas
* Exceções inesperadas são tratadas com fallback seguro

---

## 🔁 Exceções Tratadas Automaticamente

### ✅ Erros de Validação — **422 Unprocessable Entity**

Captura falhas disparadas por:

* `$request->validate()`
* `FormRequest`

Comportamento:

* Retorna mensagens claras dos campos inválidos
* Não expõe estrutura interna
* Mantém padrão único de resposta

---

### 🔐 Falha de Autenticação — **401 Unauthorized**

Disparada automaticamente pelo middleware:

```
auth:sanctum
```

Resposta:

* Usuário não autenticado
* Mensagem clara
* Status HTTP correto

---

### 🔍 Registro ou Rota Não Encontrada — **404 Not Found**

Captura:

* `ModelNotFoundException` (`findOrFail`)
* Rotas inexistentes

Evita:

* Páginas HTML
* Mensagens confusas
* Erros genéricos

---

### 🚫 Método HTTP Não Permitido — **405 Method Not Allowed**

Exemplo clássico:

* Enviar `POST` em rota que aceita apenas `GET`

Resposta automática, clara e padronizada.

---

## 🔁 Violação de Unicidade — **409 Conflict**

A API possui um **tratamento inteligente para dados duplicados**, como:

* CPF
* E-mail
* Qualquer campo com índice `UNIQUE`

Mesmo que a validação não tenha capturado antes, o handler atua como **rede de segurança**.

### O que acontece:

* A exceção `UniqueConstraintViolationException` é interceptada
* O nome do campo violado é extraído diretamente da mensagem SQL
* Uma mensagem **amigável e compreensível** é retornada
* Nenhum detalhe técnico do banco é exposto

📌 Exemplo de resposta:

```
"O dado informado (EMAIL) já está em uso por outro usuário."
```

Status HTTP: **409 – Conflict**

---

## 🌐 Mapeamento Correto de Status HTTP

Cada tipo de erro retorna o **status HTTP semanticamente correto**:

| Situação             | Status |
| -------------------- | ------ |
| Erro de validação    | 422    |
| Não autenticado      | 401    |
| Não encontrado       | 404    |
| Método não permitido | 405    |
| Dado duplicado       | 409    |
| Erro inesperado      | 500    |

Nada de `200` para erro. Aqui é API raiz. 👊

---

## 🛡️ Segurança por Ambiente

O comportamento varia conforme o ambiente configurado no Laravel.

### 🧪 Ambiente de Desenvolvimento (DEBUG = true)

Retorna:

* Mensagem real do erro
* Arquivo e linha
* Stack trace resumido

Ideal para:

* Debug rápido
* Correção eficiente
* Desenvolvimento local

---

### 🏭 Ambiente de Produção (DEBUG = false)

* Nenhum detalhe técnico é exposto
* O cliente recebe apenas uma mensagem genérica
* O erro completo é registrado em:

  ```
  storage/logs/laravel.log
  ```

Isso mantém a API:

* Segura
* Profissional
* Pronta para produção

---

## 🚀 Como Usar

Você **não precisa fazer absolutamente nada**.

Basta:

* Lançar exceções normalmente
* Usar `$request->validate()`
* Utilizar `findOrFail()`
* Confiar nos middlewares do Laravel

O **`ApiExceptionHandler`** intercepta, trata e formata tudo automaticamente.

---

## 🧱 Benefícios Diretos da Arquitetura

* Código mais limpo
* Nenhum `try/catch` repetido
* Erros previsíveis
* Respostas consistentes
* API pronta para mobile, web e integrações externas
