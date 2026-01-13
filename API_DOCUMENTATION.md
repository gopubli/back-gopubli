# API GoPubli - Documentação de Autenticação

Sistema de autenticação multi-tipo com Laravel Sanctum. A API possui 3 tipos de usuários separados:

- **Administradores** - Acesso ao painel administrativo
- **Empresas** - Acesso mobile para empresas
- **Influencers** - Acesso mobile para influenciadores

## Base URL
```
http://localhost:8000/api
```

---

## 🔐 Autenticação Administrador

### Registrar Administrador
```http
POST /api/admin/register
```

**Body:**
```json
{
  "name": "Admin Teste",
  "email": "admin@gopubli.com",
  "password": "senha12345",
  "password_confirmation": "senha12345"
}
```

**Response:**
```json
{
  "message": "Administrador registrado com sucesso",
  "user": {
    "id": 1,
    "name": "Admin Teste",
    "email": "admin@gopubli.com",
    "active": true,
    "created_at": "2024-12-16T10:00:00.000000Z"
  },
  "token": "1|laravel_sanctum_...",
  "type": "administrator"
}
```

### Login Administrador
```http
POST /api/admin/login
```

**Body:**
```json
{
  "email": "admin@gopubli.com",
  "password": "senha12345"
}
```

### Perfil do Administrador (Protegido)
```http
GET /api/admin/me
Authorization: Bearer {token}
```

### Logout Administrador (Protegido)
```http
POST /api/admin/logout
Authorization: Bearer {token}
```

---

## 🏢 Autenticação Empresa

### Registrar Empresa
```http
POST /api/company/register
```

**Body:**
```json
{
  "name": "Empresa Teste LTDA",
  "email": "empresa@gopubli.com",
  "password": "senha12345",
  "password_confirmation": "senha12345",
  "cnpj": "12345678000190",
  "phone": "11999999999",
  "address": "Rua Teste, 123"
}
```

**Response:**
```json
{
  "message": "Empresa registrada com sucesso",
  "user": {
    "id": 1,
    "name": "Empresa Teste LTDA",
    "email": "empresa@gopubli.com",
    "cnpj": "12345678000190",
    "phone": "11999999999",
    "address": "Rua Teste, 123",
    "logo": null,
    "active": true,
    "created_at": "2024-12-16T10:00:00.000000Z"
  },
  "token": "2|laravel_sanctum_...",
  "type": "company"
}
```

### Login Empresa
```http
POST /api/company/login
```

**Body:**
```json
{
  "email": "empresa@gopubli.com",
  "password": "senha12345"
}
```

### Perfil da Empresa (Protegido)
```http
GET /api/company/me
Authorization: Bearer {token}
```

### Logout Empresa (Protegido)
```http
POST /api/company/logout
Authorization: Bearer {token}
```

---

## 🎬 Autenticação Influencer

### Registrar Influencer
```http
POST /api/influencer/register
```

**Body:**
```json
{
  "name": "João Silva",
  "email": "joao@gopubli.com",
  "password": "senha12345",
  "password_confirmation": "senha12345",
  "cpf": "12345678900",
  "phone": "11999999999",
  "instagram": "@joaosilva",
  "tiktok": "@joaosilva",
  "youtube": "@joaosilva",
  "bio": "Influenciador digital focado em tecnologia"
}
```

**Response:**
```json
{
  "message": "Influencer registrado com sucesso",
  "user": {
    "id": 1,
    "name": "João Silva",
    "email": "joao@gopubli.com",
    "cpf": "12345678900",
    "phone": "11999999999",
    "instagram": "@joaosilva",
    "tiktok": "@joaosilva",
    "youtube": "@joaosilva",
    "avatar": null,
    "bio": "Influenciador digital focado em tecnologia",
    "active": true,
    "created_at": "2024-12-16T10:00:00.000000Z"
  },
  "token": "3|laravel_sanctum_...",
  "type": "influencer"
}
```

### Login Influencer
```http
POST /api/influencer/login
```

**Body:**
```json
{
  "email": "joao@gopubli.com",
  "password": "senha12345"
}
```

### Perfil do Influencer (Protegido)
```http
GET /api/influencer/me
Authorization: Bearer {token}
```

### Logout Influencer (Protegido)
```http
POST /api/influencer/logout
Authorization: Bearer {token}
```

---

## 🔒 Segurança

### Headers Obrigatórios para Rotas Protegidas
```http
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

### Regras de Validação

**Todos os usuários:**
- E-mail único por tipo de usuário
- Senha mínima de 8 caracteres
- Nome obrigatório

**Empresas:**
- CNPJ único (opcional)

**Influencers:**
- CPF único (opcional)

---

## 🚨 Códigos de Erro

- `200` - Sucesso
- `201` - Criado com sucesso
- `401` - Não autenticado
- `403` - Acesso negado (tipo de usuário incorreto)
- `422` - Erro de validação
- `500` - Erro interno do servidor

---

## 📝 Notas Importantes

1. **Tokens separados**: Cada tipo de usuário recebe seu próprio token de acesso
2. **Middleware de tipo**: As rotas protegidas verificam se o tipo de usuário está correto
3. **Logout**: Remove apenas o token atual, não todos os tokens do usuário
4. **Tabelas separadas**: Administradores, Empresas e Influencers possuem tabelas próprias

---

## 🧪 Testando com cURL

### Registrar Administrador
```bash
curl -X POST http://localhost:8000/api/admin/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Admin Teste",
    "email": "admin@gopubli.com",
    "password": "senha12345",
    "password_confirmation": "senha12345"
  }'
```

### Login e obter token
```bash
curl -X POST http://localhost:8000/api/admin/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@gopubli.com",
    "password": "senha12345"
  }'
```

### Acessar rota protegida
```bash
curl -X GET http://localhost:8000/api/admin/me \
  -H "Authorization: Bearer SEU_TOKEN_AQUI" \
  -H "Accept: application/json"
```

---

## 🆕 Novos Recursos

### Upload de Avatar/Logo
```http
POST /api/{type}/profile/avatar
POST /api/company/profile/logo (apenas empresas)
Authorization: Bearer {token}
Content-Type: multipart/form-data

Body: FormData
- avatar: arquivo de imagem (jpeg, png, jpg, gif - max 2MB)
- logo: arquivo de imagem (jpeg, png, jpg, gif, svg - max 2MB)
```

### Atualizar Perfil
```http
PUT /api/{type}/profile
Authorization: Bearer {token}

Body para Administrador:
{
  "name": "Nome Atualizado",
  "phone": "11999999999"
}

Body para Empresa:
{
  "name": "Nova Empresa LTDA",
  "cnpj": "12345678000190",
  "phone": "11999999999",
  "address": "Nova Rua, 456"
}

Body para Influencer:
{
  "name": "João Silva Atualizado",
  "cpf": "12345678900",
  "phone": "11999999999",
  "instagram": "@novoinstagram",
  "tiktok": "@novotiktok",
  "youtube": "@novoyoutube",
  "bio": "Nova bio do influenciador"
}
```

### Deletar Avatar/Logo
```http
DELETE /api/{type}/profile/avatar
Authorization: Bearer {token}
```

---

## 🔑 Recuperação de Senha

### Solicitar Reset de Senha
```http
POST /api/{type}/forgot-password

Body:
{
  "email": "usuario@gopubli.com"
}

Response:
{
  "message": "Se o e-mail existir em nossa base, você receberá as instruções para redefinir sua senha.",
  "token": "token_para_testes_em_dev" // apenas em modo debug
}
```

### Resetar Senha
```http
POST /api/{type}/reset-password

Body:
{
  "email": "usuario@gopubli.com",
  "token": "token_recebido_por_email",
  "password": "novasenha123",
  "password_confirmation": "novasenha123"
}

Response:
{
  "message": "Senha redefinida com sucesso! Por favor, faça login com sua nova senha."
}
```

**Observações:**
- Token expira em 60 minutos
- Ao resetar a senha, todos os tokens de acesso são revogados
- Usuário precisa fazer login novamente após resetar a senha

---

## ✉️ Verificação de E-mail

### Enviar E-mail de Verificação
```http
POST /api/{type}/email/send-verification
Authorization: Bearer {token}

Response:
{
  "message": "E-mail de verificação enviado com sucesso"
}
```

### Verificar E-mail (via link no e-mail)
```http
GET /api/email/verify/{type}/{id}/{hash}?expires={timestamp}&signature={signature}

Response:
{
  "message": "E-mail verificado com sucesso!",
  "user": {...}
}
```

### Verificar Status de Verificação
```http
GET /api/{type}/email/check-verification
Authorization: Bearer {token}

Response:
{
  "verified": true,
  "email": "usuario@gopubli.com"
}
```

**Observações:**
- E-mail de verificação é enviado automaticamente ao registrar
- Link de verificação expira em 60 minutos
- Use o middleware `verified` para proteger rotas que exigem email verificado

---

## 🔒 Middleware de Verificação (Opcional)

Para proteger rotas que exigem e-mail verificado, adicione o middleware `verified`:

```php
Route::middleware(['auth:sanctum', 'type.company', 'verified'])->group(function () {
    // Rotas que exigem e-mail verificado
});
```

---

## 📦 Estrutura do Projeto

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       ├── AdministratorAuthController.php
│   │       ├── CompanyAuthController.php
│   │       ├── InfluencerAuthController.php
│   │       ├── ProfileController.php
│   │       ├── PasswordResetController.php
│   │       └── EmailVerificationController.php
│   └── Middleware/
│       ├── EnsureUserIsAdministrator.php
│       ├── EnsureUserIsCompany.php
│       ├── EnsureUserIsInfluencer.php
│       └── EnsureEmailIsVerified.php
├── Models/
│   ├── Administrator.php
│   ├── Company.php
│   └── Influencer.php
└── Notifications/
    ├── ResetPasswordNotification.php
    └── VerifyEmailNotification.php

database/
└── migrations/
    ├── 2024_12_16_000001_create_administrators_table.php
    ├── 2024_12_16_000002_create_companies_table.php
    ├── 2024_12_16_000003_create_influencers_table.php
    └── 2024_12_16_000004_create_password_reset_tokens_tables.php

routes/
└── api.php

storage/
└── app/
    └── public/
        ├── avatars/
        └── logos/
```
