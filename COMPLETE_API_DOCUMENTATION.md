# 📚 Documentação Completa da API - GoPubli
## Sistema Completo para Frontend Vue.js

> **URL Base:** `http://localhost:8000/api`
> 
> **Autenticação:** Bearer Token via header `Authorization: Bearer {token}`

---

## 📋 Índice

1. [Autenticação](#autenticação)
2. [Perfil do Usuário](#perfil-do-usuário)
3. [Campanhas - Empresa](#campanhas-empresa)
4. [Campanhas - Influencer](#campanhas-influencer)
5. [GO Coins](#go-coins)
6. [Termos e Condições](#termos-e-condições)
7. [Assinaturas](#assinaturas)
8. [Administração](#administração)
9. [Códigos de Status](#códigos-de-status)
10. [Estrutura de Dados](#estrutura-de-dados)

---

## 🔐 Autenticação

### 1.1 Login - Administrador
```http
POST /api/admin/login
```

**Body:**
```json
{
  "email": "admin@email.com",
  "password": "senha123"
}
```

**Response (200):**
```json
{
  "message": "Login realizado com sucesso",
  "user": { /* dados do admin */ },
  "token": "1|token...",
  "type": "administrator"
}
```

---

### 1.2 Login - Empresa
```http
POST /api/company/login
```

**Body:**
```json
{
  "email": "empresa@email.com",
  "password": "senha123"
}
```

**Response (200):**
```json
{
  "message": "Login realizado com sucesso",
  "user": {
    "id": 1,
    "name": "Empresa Exemplo LTDA",
    "email": "empresa@email.com",
    "cnpj": "12.345.678/0001-90",
    "phone": "(11) 98765-4321",
    "address": "Rua X, 123",
    "logo_url": null,
    "active": true
  },
  "token": "2|token...",
  "type": "company"
}
```

---

### 1.3 Login - Influencer
```http
POST /api/influencer/login
```

**Body:**
```json
{
  "email": "influencer@email.com",
  "password": "senha123"
}
```

**Response (200):**
```json
{
  "message": "Login realizado com sucesso",
  "user": {
    "id": 1,
    "name": "Maria Silva",
    "email": "influencer@email.com",
    "cpf": "123.456.789-00",
    "phone": "(11) 91234-5678",
    "instagram": "@mariasilva",
    "tiktok": "@mariasilva",
    "youtube": "mariasilva",
    "bio": "Influencer de moda",
    "followers": 125000,
    "niche": "Moda e Beleza",
    "avatar_url": null,
    "active": true
  },
  "token": "3|token...",
  "type": "influencer"
}
```

---

### 1.4 Registro - Empresa
```http
POST /api/company/register
```

**Body:**
```json
{
  "name": "Empresa Exemplo LTDA",
  "email": "empresa@email.com",
  "password": "senha123",
  "password_confirmation": "senha123",
  "cnpj": "12.345.678/0001-90",
  "phone": "(11) 98765-4321",
  "address": "Rua X, 123"
}
```

**Campos Obrigatórios:**
- `name` (string, max 255)
- `email` (email, único)
- `password` (min 8 caracteres)
- `password_confirmation` (igual ao password)

**Campos Opcionais:**
- `cnpj`, `phone`, `address`

**Response (201):** Igual ao login

---

### 1.5 Registro - Influencer
```http
POST /api/influencer/register
```

**Body:**
```json
{
  "name": "Maria Silva",
  "email": "influencer@email.com",
  "password": "senha123",
  "password_confirmation": "senha123",
  "cpf": "123.456.789-00",
  "phone": "(11) 91234-5678",
  "instagram": "@mariasilva",
  "tiktok": "@mariasilva",
  "youtube": "mariasilva",
  "bio": "Influencer de moda e beleza",
  "followers": 125000,
  "niche": "Moda e Beleza"
}
```

**Campos Obrigatórios:**
- `name`, `email`, `password`, `password_confirmation`

**Campos Opcionais:**
- `cpf`, `phone`, `instagram`, `tiktok`, `youtube`, `bio`, `followers`, `niche`

**Response (201):** Igual ao login

---

### 1.6 Logout (Todas as roles)
```http
POST /api/{role}/logout
Authorization: Bearer {token}
```

**Roles:** `admin`, `company`, `influencer`

**Response (200):**
```json
{
  "message": "Logout realizado com sucesso"
}
```

---

### 1.7 Obter Dados do Usuário Logado
```http
GET /api/{role}/me
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "user": { /* dados completos do usuário */ },
  "type": "company|influencer|administrator"
}
```

---

### 1.8 Esqueci Minha Senha
```http
POST /api/{role}/forgot-password
```

**Body:**
```json
{
  "email": "usuario@email.com"
}
```

**Response (200):**
```json
{
  "message": "Link de redefinição enviado para seu e-mail"
}
```

---

### 1.9 Resetar Senha
```http
POST /api/{role}/reset-password
```

**Body:**
```json
{
  "email": "usuario@email.com",
  "token": "token_do_email",
  "password": "novaSenha123",
  "password_confirmation": "novaSenha123"
}
```

**Response (200):**
```json
{
  "message": "Senha redefinida com sucesso"
}
```

---

## 👤 Perfil do Usuário

### 2.1 Atualizar Perfil - Empresa
```http
PUT /api/company/profile
Authorization: Bearer {token}
```

**Body:**
```json
{
  "name": "Novo Nome da Empresa",
  "phone": "(11) 99999-9999",
  "address": "Novo endereço"
}
```

**Response (200):**
```json
{
  "message": "Perfil atualizado com sucesso",
  "user": { /* dados atualizados */ }
}
```

---

### 2.2 Atualizar Perfil - Influencer
```http
PUT /api/influencer/profile
Authorization: Bearer {token}
```

**Body:**
```json
{
  "name": "Novo Nome",
  "phone": "(11) 99999-9999",
  "instagram": "@novousuario",
  "tiktok": "@novousuario",
  "youtube": "novousuario",
  "bio": "Nova biografia",
  "followers": 150000,
  "niche": "Novo nicho"
}
```

---

### 2.3 Upload de Avatar (Empresa/Influencer/Admin)
```http
POST /api/{role}/profile/avatar
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Body (FormData):**
```javascript
const formData = new FormData();
formData.append('avatar', fileInput.files[0]);
```

**Response (200):**
```json
{
  "message": "Avatar atualizado com sucesso",
  "avatar_url": "http://localhost:8000/storage/avatars/hash.jpg"
}
```

---

### 2.4 Upload de Logo (Empresa apenas)
```http
POST /api/company/profile/logo
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Body (FormData):**
```javascript
const formData = new FormData();
formData.append('logo', fileInput.files[0]);
```

**Response (200):**
```json
{
  "message": "Logo atualizado com sucesso",
  "logo_url": "http://localhost:8000/storage/logos/hash.jpg"
}
```

---

### 2.5 Deletar Avatar
```http
DELETE /api/{role}/profile/avatar
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "Avatar removido com sucesso"
}
```

---

### 2.6 Enviar Email de Verificação
```http
POST /api/{role}/email/send-verification
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "E-mail de verificação enviado"
}
```

---

### 2.7 Verificar Status de Email
```http
GET /api/{role}/email/check-verification
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "email_verified": true,
  "email_verified_at": "2026-01-15T10:00:00.000000Z"
}
```

---

## 🏢 Campanhas - Empresa

### 3.1 Dashboard da Empresa
```http
GET /api/company/campaigns/dashboard
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "total_campaigns": 15,
  "active_campaigns": 5,
  "completed_campaigns": 8,
  "draft_campaigns": 2,
  "total_spent": 45000.00,
  "total_applications": 150,
  "pending_applications": 25
}
```

---

### 3.2 Listar Campanhas da Empresa
```http
GET /api/company/campaigns
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "company_id": 1,
      "title": "Campanha Verão 2026",
      "description": "Campanha para divulgação de produtos de verão",
      "objective": "branding",
      "category": "Moda",
      "platform": "Instagram",
      "start_date": "2026-02-01",
      "end_date": "2026-03-31",
      "requirements": "Mínimo 50k seguidores",
      "deliverables": "3 posts e 5 stories",
      "amount": 5000.00,
      "min_amount": 200.00,
      "influencer_amount": 3000.00,
      "gopubli_commission": 1000.00,
      "marketing_budget": 1000.00,
      "status": "open",
      "payment_status": "paid",
      "blocked": false,
      "selected_influencer_id": null,
      "applications_count": 12,
      "created_at": "2026-01-15T10:00:00.000000Z"
    }
  ],
  "per_page": 15,
  "total": 25
}
```

---

### 3.3 Criar Nova Campanha
```http
POST /api/company/campaigns
Authorization: Bearer {token}
```

**Body:**
```json
{
  "title": "Campanha Verão 2026",
  "description": "Campanha para divulgação de produtos de verão na plataforma Instagram",
  "objective": "branding",
  "category": "Moda",
  "platform": "Instagram",
  "start_date": "2026-02-01",
  "end_date": "2026-03-31",
  "requirements": "Mínimo 50k seguidores, público feminino, nicho de moda",
  "deliverables": "3 posts no feed e 5 stories",
  "total_value": 5000.00
}
```

**Campos Obrigatórios:**
- `title` (string, max 255)
- `description` (string)
- `total_value` (decimal, mín 200.00) ou `amount`

**Campos Opcionais:**
- `objective`: "branding", "traffic", "conversion"
- `category`, `platform`, `start_date`, `end_date`
- `requirements`, `deliverables`

**Response (201):**
```json
{
  "message": "Campanha criada com sucesso. Proceda com o pagamento para ativá-la.",
  "campaign": {
    "id": 1,
    "title": "Campanha Verão 2026",
    "amount": 5000.00,
    "influencer_amount": 3000.00,
    "gopubli_commission": 1000.00,
    "marketing_budget": 1000.00,
    "status": "draft",
    "payment_status": "pending"
  }
}
```

**Distribuição Automática (60/20/20):**
- **60%** → Influencer
- **20%** → GoPubli (comissão)
- **20%** → Marketing Budget (GO Coins)

---

### 3.4 Ver Detalhes de uma Campanha
```http
GET /api/company/campaigns/{id}
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "id": 1,
  "title": "Campanha Verão 2026",
  "description": "...",
  "amount": 5000.00,
  "influencer_amount": 3000.00,
  "status": "open",
  "payment_status": "paid",
  "applications_count": 12,
  "selected_influencer": null,
  "created_at": "2026-01-15T10:00:00.000000Z"
}
```

---

### 3.5 Editar Campanha
```http
PUT /api/company/campaigns/{id}
Authorization: Bearer {token}
```

**Body:** (mesmos campos do criar)

**Restrições:**
- Só pode editar campanhas em status `draft` ou `open`
- Se já tiver influencer selecionado, não pode editar

---

### 3.6 Confirmar Pagamento da Campanha
```http
POST /api/company/campaigns/{id}/confirm-payment
Authorization: Bearer {token}
```

**Body:**
```json
{
  "payment_method": "pix",
  "transaction_id": "PIX123456789"
}
```

**Response (200):**
```json
{
  "message": "Pagamento confirmado! Sua campanha está ativa.",
  "campaign": {
    "id": 1,
    "status": "open",
    "payment_status": "paid",
    "payment_confirmed_at": "2026-01-15T10:00:00.000000Z"
  }
}
```

---

### 3.7 Listar Candidaturas de uma Campanha
```http
GET /api/company/campaigns/{id}/applications
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "campaign": {
    "id": 1,
    "title": "Campanha Verão 2026"
  },
  "applications": [
    {
      "id": 1,
      "campaign_id": 1,
      "influencer_id": 5,
      "status": "pending",
      "offered_amount": 2800.00,
      "proposal_message": "Olá! Tenho experiência com campanhas de moda...",
      "created_at": "2026-01-15T10:00:00.000000Z",
      "influencer": {
        "id": 5,
        "name": "Maria Silva",
        "instagram": "@mariasilva",
        "followers": 125000,
        "niche": "Moda e Beleza",
        "avatar_url": "..."
      }
    }
  ],
  "total": 12,
  "pending": 8,
  "accepted": 1,
  "rejected": 3
}
```

---

### 3.8 Aceitar Candidatura (Fazer Match)
```http
POST /api/company/campaigns/{campaignId}/applications/{applicationId}/accept
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "🎉 MATCH! Influencer selecionado com sucesso",
  "application": {
    "id": 1,
    "status": "accepted",
    "accepted_at": "2026-01-15T10:00:00.000000Z"
  },
  "campaign": {
    "id": 1,
    "status": "in_progress",
    "selected_influencer_id": 5,
    "started_at": "2026-01-15T10:00:00.000000Z"
  }
}
```

---

### 3.9 Rejeitar Candidatura
```http
POST /api/company/campaigns/{campaignId}/applications/{applicationId}/reject
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "Candidatura rejeitada",
  "application": {
    "id": 1,
    "status": "rejected"
  }
}
```

---

### 3.10 Completar Campanha
```http
POST /api/company/campaigns/{id}/complete
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "Campanha concluída com sucesso! GO Coins distribuídos.",
  "campaign": {
    "id": 1,
    "status": "completed",
    "completed_at": "2026-01-15T10:00:00.000000Z"
  },
  "coins_distributed": {
    "company": 1000,
    "influencer": 1500
  }
}
```

**Sistema de GO Coins:**
- Empresa recebe **20%** do valor total em GO Coins
- Influencer recebe **25%** do valor recebido em GO Coins

---

### 3.11 Cancelar Campanha
```http
POST /api/company/campaigns/{id}/cancel
Authorization: Bearer {token}
```

**Body:**
```json
{
  "reason": "Motivo do cancelamento"
}
```

**Response (200):**
```json
{
  "message": "Campanha cancelada",
  "campaign": {
    "status": "cancelled"
  }
}
```

---

## 🎯 Campanhas - Influencer

### 4.1 Dashboard do Influencer
```http
GET /api/influencer/campaigns/dashboard
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "available_campaigns": 25,
  "my_applications": 10,
  "pending_applications": 6,
  "accepted_applications": 2,
  "active_campaigns": 1,
  "completed_campaigns": 3,
  "total_earned": 12000.00,
  "gocoin_balance": 2500
}
```

---

### 4.2 Listar Campanhas Disponíveis
```http
GET /api/influencer/campaigns/available
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "title": "Campanha Verão 2026",
      "description": "...",
      "category": "Moda",
      "platform": "Instagram",
      "start_date": "2026-02-01",
      "end_date": "2026-03-31",
      "requirements": "Mínimo 50k seguidores",
      "deliverables": "3 posts e 5 stories",
      "influencer_amount": 3000.00,
      "status": "open",
      "has_applied": false,
      "applications_count": 12,
      "company": {
        "id": 1,
        "name": "Empresa Exemplo LTDA",
        "logo": null
      }
    }
  ],
  "per_page": 15,
  "total": 42
}
```

**Filtros Aplicados Automaticamente:**
- Apenas campanhas pagas (`payment_status = 'paid'`)
- Apenas campanhas abertas (`status = 'open'`)
- Não bloqueadas (`blocked = false`)

---

### 4.3 Ver Detalhes de Campanha Disponível
```http
GET /api/influencer/campaigns/{id}
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "id": 1,
  "title": "Campanha Verão 2026",
  "description": "...",
  "objective": "branding",
  "category": "Moda",
  "platform": "Instagram",
  "requirements": "...",
  "deliverables": "...",
  "influencer_amount": 3000.00,
  "start_date": "2026-02-01",
  "end_date": "2026-03-31",
  "has_applied": false,
  "my_application": null,
  "company": {
    "id": 1,
    "name": "Empresa Exemplo LTDA",
    "logo": null
  }
}
```

**Nota:** Dados sensíveis da empresa ficam ocultos até o match.

---

### 4.4 Candidatar-se a uma Campanha
```http
POST /api/influencer/campaigns/{id}/apply
Authorization: Bearer {token}
```

**Body:**
```json
{
  "offered_amount": 2800.00,
  "proposal_message": "Olá! Tenho grande experiência com campanhas de moda e um público engajado de 125k seguidores. Posso entregar conteúdo de alta qualidade..."
}
```

**Campos Opcionais:**
- `offered_amount` (decimal) - Valor proposto pelo influencer
- `proposal_message` (string, max 1000) - Mensagem de proposta

**Response (201):**
```json
{
  "message": "Candidatura enviada com sucesso!",
  "application": {
    "id": 1,
    "campaign_id": 1,
    "influencer_id": 5,
    "status": "pending",
    "offered_amount": 2800.00,
    "proposal_message": "...",
    "created_at": "2026-01-15T10:00:00.000000Z"
  }
}
```

**Requisitos:**
- Deve ter aceito o Termo de Confidencialidade
- Não pode já ter se candidatado à mesma campanha

---

### 4.5 Minhas Candidaturas
```http
GET /api/influencer/campaigns/my-applications
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "total": 10,
  "pending_count": 6,
  "accepted_count": 2,
  "rejected_count": 2,
  "applications": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "campaign_id": 1,
        "influencer_id": 5,
        "status": "accepted",
        "status_label": "✅ MATCH! Você foi selecionado",
        "has_match": true,
        "offered_amount": 2800.00,
        "proposal_message": "...",
        "created_at": "2026-01-15T10:00:00.000000Z",
        "accepted_at": "2026-01-16T14:00:00.000000Z",
        "campaign": {
          "id": 1,
          "title": "Campanha Verão 2026",
          "company": {
            "id": 1,
            "name": "Empresa Exemplo LTDA",
            "email": "empresa@email.com",
            "phone": "(11) 98765-4321"
          }
        }
      }
    ]
  }
}
```

**Status Labels:**
- `pending` → "Aguardando resposta"
- `accepted` → "✅ MATCH! Você foi selecionado"
- `rejected` → "Não selecionado"
- `withdrawn` → "Cancelada"

---

### 4.6 Minhas Campanhas Ativas
```http
GET /api/influencer/campaigns/my-campaigns
Authorization: Bearer {token}
```

**Response (200):** Lista de campanhas onde foi selecionado

---

### 4.7 Ver Detalhes da Minha Campanha
```http
GET /api/influencer/my-campaigns/{id}
Authorization: Bearer {token}
```

**Response (200):** Detalhes completos incluindo dados da empresa

---

### 4.8 Retirar Candidatura
```http
POST /api/influencer/applications/{id}/withdraw
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "Candidatura retirada com sucesso",
  "application": {
    "id": 1,
    "status": "withdrawn"
  }
}
```

**Restrição:** Só pode retirar candidaturas com status `pending`

---

### 4.9 Atualizar Candidatura
```http
PUT /api/influencer/applications/{id}
Authorization: Bearer {token}
```

**Body:**
```json
{
  "offered_amount": 3000.00,
  "proposal_message": "Nova proposta..."
}
```

---

## 💰 GO Coins

### 5.1 Ver Saldo
```http
GET /api/{role}/gocoin/balance
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "balance": 2500,
  "total_earned": 5000,
  "total_redeemed": 2500
}
```

---

### 5.2 Listar Transações
```http
GET /api/{role}/gocoin/transactions
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "holder_type": "App\\Models\\Influencer",
      "holder_id": 5,
      "type": "credit",
      "amount": 1500,
      "category": "campaign_completion",
      "description": "GO Coins por campanha concluída: Campanha Verão 2026",
      "reference_type": "App\\Models\\Campaign",
      "reference_id": 1,
      "created_at": "2026-01-15T10:00:00.000000Z"
    },
    {
      "id": 2,
      "type": "debit",
      "amount": 500,
      "category": "product",
      "description": "Resgate: Produto X",
      "created_at": "2026-01-14T15:00:00.000000Z"
    }
  ],
  "total": 25
}
```

---

### 5.3 Transações por Tipo
```http
GET /api/{role}/gocoin/transactions/type/{type}
Authorization: Bearer {token}
```

**Tipos:** `credit`, `debit`

---

### 5.4 Transações por Categoria
```http
GET /api/{role}/gocoin/transactions/category/{category}
Authorization: Bearer {token}
```

**Categorias:**
- `campaign_completion` - Conclusão de campanha
- `bonus` - Bônus
- `product` - Resgate de produto
- `service` - Resgate de serviço
- `discount` - Desconto

---

### 5.5 Resgatar GO Coins
```http
POST /api/{role}/gocoin/redeem
Authorization: Bearer {token}
```

**Body:**
```json
{
  "amount": 500,
  "category": "product",
  "description": "Resgate de Produto X"
}
```

**Response (200):**
```json
{
  "message": "Resgate realizado com sucesso",
  "transaction": {
    "id": 10,
    "type": "debit",
    "amount": 500,
    "new_balance": 2000
  }
}
```

---

### 5.6 Estatísticas de GO Coins
```http
GET /api/{role}/gocoin/stats
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "current_balance": 2500,
  "total_credits": 5000,
  "total_debits": 2500,
  "transactions_count": 15,
  "last_transaction": {
    "id": 15,
    "type": "credit",
    "amount": 1000,
    "created_at": "2026-01-15T10:00:00.000000Z"
  }
}
```

---

### 5.7 Categorias de Resgate
```http
GET /api/{role}/gocoin/redeem-categories
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "categories": [
    {
      "key": "product",
      "label": "Produtos",
      "description": "Resgatar produtos da loja"
    },
    {
      "key": "service",
      "label": "Serviços",
      "description": "Resgatar serviços parceiros"
    },
    {
      "key": "discount",
      "label": "Descontos",
      "description": "Cupons de desconto"
    }
  ]
}
```

---

## 📜 Termos e Condições

### 6.1 Aceitar Termo de Confidencialidade
```http
POST /api/{role}/terms/confidentiality
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "Termo de Confidencialidade aceito com sucesso",
  "acceptance": {
    "id": 1,
    "term_type": "confidentiality",
    "accepted_at": "2026-01-15T10:00:00.000000Z"
  }
}
```

---

### 6.2 Aceitar Política de Privacidade
```http
POST /api/{role}/terms/privacy-policy
Authorization: Bearer {token}
```

---

### 6.3 Aceitar Termos de Uso
```http
POST /api/{role}/terms/terms-of-use
Authorization: Bearer {token}
```

---

### 6.4 Ver Status dos Termos
```http
GET /api/{role}/terms/status
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "confidentiality": {
    "accepted": true,
    "accepted_at": "2026-01-15T10:00:00.000000Z"
  },
  "privacy_policy": {
    "accepted": true,
    "accepted_at": "2026-01-15T10:00:00.000000Z"
  },
  "terms_of_use": {
    "accepted": false,
    "accepted_at": null
  }
}
```

---

### 6.5 Histórico de Aceites
```http
GET /api/{role}/terms/history
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "term_type": "confidentiality",
      "accepted_at": "2026-01-15T10:00:00.000000Z"
    },
    {
      "id": 2,
      "term_type": "privacy_policy",
      "accepted_at": "2026-01-15T10:00:00.000000Z"
    }
  ]
}
```

---

### 6.6 Obter Texto do Termo
```http
GET /api/{role}/terms/confidentiality/text
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "title": "Termo de Confidencialidade",
  "content": "Texto completo do termo...",
  "version": "1.0",
  "effective_date": "2026-01-01"
}
```

---

## 💳 Assinaturas (Empresa apenas)

### 7.1 Ver Assinatura Atual
```http
GET /api/company/subscription
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "id": 1,
  "company_id": 1,
  "plan_name": "Plano Pro",
  "amount": 299.90,
  "status": "active",
  "billing_cycle": "monthly",
  "start_date": "2026-01-01",
  "end_date": "2026-02-01",
  "auto_renew": true,
  "created_at": "2026-01-01T00:00:00.000000Z"
}
```

---

### 7.2 Criar Assinatura
```http
POST /api/company/subscription
Authorization: Bearer {token}
```

**Body:**
```json
{
  "plan_name": "Plano Pro",
  "billing_cycle": "monthly",
  "auto_renew": true
}
```

**Response (201):**
```json
{
  "message": "Assinatura criada. Proceda com o pagamento.",
  "subscription": {
    "id": 1,
    "amount": 299.90,
    "status": "pending",
    "payment_url": "..."
  }
}
```

---

### 7.3 Confirmar Pagamento da Assinatura
```http
POST /api/company/subscription/confirm-payment
Authorization: Bearer {token}
```

**Body:**
```json
{
  "payment_method": "credit_card",
  "transaction_id": "TXN123456"
}
```

**Response (200):**
```json
{
  "message": "Pagamento confirmado! Assinatura ativa.",
  "subscription": {
    "id": 1,
    "status": "active",
    "paid_at": "2026-01-15T10:00:00.000000Z"
  }
}
```

---

### 7.4 Renovar Assinatura
```http
POST /api/company/subscription/renew
Authorization: Bearer {token}
```

---

### 7.5 Cancelar Assinatura
```http
POST /api/company/subscription/cancel
Authorization: Bearer {token}
```

**Body:**
```json
{
  "reason": "Motivo do cancelamento"
}
```

---

### 7.6 Status da Assinatura
```http
GET /api/company/subscription/status
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "is_active": true,
  "status": "active",
  "expires_at": "2026-02-01T00:00:00.000000Z",
  "days_remaining": 17
}
```

---

### 7.7 Histórico de Pagamentos
```http
GET /api/company/subscription/payment-history
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "amount": 299.90,
      "payment_method": "credit_card",
      "status": "paid",
      "paid_at": "2026-01-15T10:00:00.000000Z"
    }
  ]
}
```

---

## 🔧 Administração

### 8.1 Listar Influencers
```http
GET /api/admin/influencers
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "name": "Maria Silva",
      "email": "maria@email.com",
      "followers": 125000,
      "niche": "Moda e Beleza",
      "active": true,
      "created_at": "2026-01-15T10:00:00.000000Z"
    }
  ],
  "total": 50
}
```

---

### 8.2 Ver Detalhes do Influencer
```http
GET /api/admin/influencers/{id}
Authorization: Bearer {token}
```

---

### 8.3 Atualizar Influencer
```http
PUT /api/admin/influencers/{id}
Authorization: Bearer {token}
```

**Body:**
```json
{
  "active": false,
  "verified": true
}
```

---

### 8.4 Deletar Influencer
```http
DELETE /api/admin/influencers/{id}
Authorization: Bearer {token}
```

---

### 8.5 Listar Empresas
```http
GET /api/admin/companies
Authorization: Bearer {token}
```

---

### 8.6 Ver Detalhes da Empresa
```http
GET /api/admin/companies/{id}
Authorization: Bearer {token}
```

---

### 8.7 Atualizar Empresa
```http
PUT /api/admin/companies/{id}
Authorization: Bearer {token}
```

---

### 8.8 Deletar Empresa
```http
DELETE /api/admin/companies/{id}
Authorization: Bearer {token}
```

---

## 📊 Códigos de Status HTTP

| Código | Significado |
|--------|-------------|
| 200 | Sucesso |
| 201 | Criado com sucesso |
| 400 | Requisição inválida |
| 401 | Não autenticado |
| 403 | Não autorizado (sem permissão) |
| 404 | Não encontrado |
| 422 | Erro de validação |
| 500 | Erro do servidor |

---

## 📦 Estrutura de Dados

### Status de Campanha
- `draft` - Rascunho (recém criada)
- `open` - Aberta (paga e aceitando candidaturas)
- `in_progress` - Em andamento (influencer selecionado)
- `completed` - Concluída
- `cancelled` - Cancelada

### Status de Pagamento
- `pending` - Aguardando pagamento
- `paid` - Pago

### Status de Candidatura
- `pending` - Aguardando resposta
- `accepted` - Aceita (Match!)
- `rejected` - Rejeitada
- `withdrawn` - Retirada

### Objetivos de Campanha
- `branding` - Awareness de marca
- `traffic` - Gerar tráfego
- `conversion` - Gerar conversões/vendas

### Plataformas
- Instagram
- TikTok
- YouTube
- Facebook
- Twitter/X
- LinkedIn

### Ciclos de Cobrança
- `monthly` - Mensal
- `quarterly` - Trimestral
- `yearly` - Anual

---

## 🔒 Autenticação e Segurança

### Headers Necessários

**Para Rotas Públicas:**
```javascript
{
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}
```

**Para Rotas Autenticadas:**
```javascript
{
  'Content-Type': 'application/json',
  'Accept': 'application/json',
  'Authorization': 'Bearer ' + token
}
```

### Upload de Arquivos

**Para upload de imagens:**
```javascript
{
  'Authorization': 'Bearer ' + token,
  'Accept': 'application/json'
  // NÃO incluir Content-Type, o browser define automaticamente
}
```

---

## 💡 Composable Vue.js Completo

```javascript
// composables/useApi.js
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const API_BASE_URL = 'http://localhost:8000/api'

export function useApi() {
  const router = useRouter()
  const loading = ref(false)
  const error = ref(null)

  // Obter token
  function getToken() {
    return localStorage.getItem('token')
  }

  // Obter tipo de usuário
  function getUserType() {
    return localStorage.getItem('userType')
  }

  // Requisição genérica
  async function request(endpoint, options = {}) {
    loading.value = true
    error.value = null

    try {
      const token = getToken()
      const defaultHeaders = {
        'Accept': 'application/json'
      }

      // Adicionar token se existir
      if (token) {
        defaultHeaders['Authorization'] = `Bearer ${token}`
      }

      // Adicionar Content-Type apenas se não for FormData
      if (!(options.body instanceof FormData)) {
        defaultHeaders['Content-Type'] = 'application/json'
      }

      const response = await fetch(`${API_BASE_URL}${endpoint}`, {
        ...options,
        headers: {
          ...defaultHeaders,
          ...options.headers
        }
      })

      const data = await response.json()

      if (!response.ok) {
        if (response.status === 401) {
          // Token inválido, fazer logout
          localStorage.clear()
          router.push('/login')
          throw new Error('Sessão expirada')
        }

        if (data.errors) {
          const errorMessages = Object.values(data.errors).flat()
          throw new Error(errorMessages.join('\n'))
        }

        throw new Error(data.message || 'Erro na requisição')
      }

      return data
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  // Métodos HTTP
  const get = (endpoint) => request(endpoint, { method: 'GET' })
  
  const post = (endpoint, body) => request(endpoint, {
    method: 'POST',
    body: body instanceof FormData ? body : JSON.stringify(body)
  })
  
  const put = (endpoint, body) => request(endpoint, {
    method: 'PUT',
    body: JSON.stringify(body)
  })
  
  const del = (endpoint) => request(endpoint, { method: 'DELETE' })

  // Upload de arquivo
  async function uploadFile(endpoint, file, fieldName = 'file') {
    const formData = new FormData()
    formData.append(fieldName, file)
    return post(endpoint, formData)
  }

  return {
    loading,
    error,
    get,
    post,
    put,
    del,
    uploadFile,
    getToken,
    getUserType
  }
}
```

---

## 🎯 Exemplos de Uso

### Login
```javascript
import { useApi } from '@/composables/useApi'

const { post, loading, error } = useApi()

async function login(email, password, type) {
  const data = await post(`/${type}/login`, { email, password })
  
  // Salvar dados
  localStorage.setItem('token', data.token)
  localStorage.setItem('user', JSON.stringify(data.user))
  localStorage.setItem('userType', data.type)
  
  return data
}
```

### Listar Campanhas
```javascript
const { get } = useApi()

async function getCampaigns() {
  const userType = localStorage.getItem('userType')
  return await get(`/${userType}/campaigns`)
}
```

### Criar Campanha
```javascript
const { post } = useApi()

async function createCampaign(formData) {
  return await post('/company/campaigns', {
    title: formData.title,
    description: formData.description,
    total_value: formData.totalValue,
    // ...outros campos
  })
}
```

### Upload de Avatar
```javascript
const { uploadFile } = useApi()

async function updateAvatar(file) {
  const userType = localStorage.getItem('userType')
  return await uploadFile(`/${userType}/profile/avatar`, file, 'avatar')
}
```

### Candidatar-se a Campanha
```javascript
const { post } = useApi()

async function applyToCampaign(campaignId, data) {
  return await post(`/influencer/campaigns/${campaignId}/apply`, {
    offered_amount: data.amount,
    proposal_message: data.message
  })
}
```

---

## ✅ Checklist de Implementação

### Autenticação
- [ ] Login (Admin, Empresa, Influencer)
- [ ] Registro (Empresa, Influencer)
- [ ] Logout
- [ ] Esqueci senha
- [ ] Reset de senha

### Perfil
- [ ] Ver perfil
- [ ] Atualizar perfil
- [ ] Upload de avatar
- [ ] Upload de logo (empresa)
- [ ] Verificação de email

### Campanhas (Empresa)
- [ ] Dashboard
- [ ] Listar campanhas
- [ ] Criar campanha
- [ ] Editar campanha
- [ ] Confirmar pagamento
- [ ] Ver candidaturas
- [ ] Aceitar/rejeitar candidaturas
- [ ] Completar campanha
- [ ] Cancelar campanha

### Campanhas (Influencer)
- [ ] Dashboard
- [ ] Listar campanhas disponíveis
- [ ] Ver detalhes da campanha
- [ ] Candidatar-se
- [ ] Ver minhas candidaturas
- [ ] Ver minhas campanhas ativas
- [ ] Retirar candidatura
- [ ] Atualizar candidatura

### GO Coins
- [ ] Ver saldo
- [ ] Listar transações
- [ ] Resgatar coins
- [ ] Ver estatísticas

### Termos
- [ ] Aceitar termos
- [ ] Ver status dos termos
- [ ] Ver histórico

### Assinaturas (Empresa)
- [ ] Ver assinatura
- [ ] Criar assinatura
- [ ] Confirmar pagamento
- [ ] Cancelar assinatura
- [ ] Ver histórico de pagamentos

---

## 🆘 Tratamento de Erros

### Erro 401 - Não Autenticado
```javascript
if (response.status === 401) {
  localStorage.clear()
  router.push('/login')
  throw new Error('Sessão expirada. Faça login novamente.')
}
```

### Erro 422 - Validação
```javascript
if (data.errors) {
  const errorMessages = Object.values(data.errors).flat()
  // Exibir cada erro
  errorMessages.forEach(msg => console.error(msg))
}
```

### Erro 403 - Sem Permissão
```javascript
if (response.status === 403) {
  // Mostrar mensagem ao usuário
  alert('Você não tem permissão para esta ação')
}
```

---

## 📝 Notas Finais

1. **CORS**: Certifique-se que o backend aceita requisições do domínio do frontend
2. **HTTPS**: Use sempre HTTPS em produção
3. **Tokens**: Tokens nunca expiram automaticamente, apenas no logout
4. **Paginação**: Todas as listagens são paginadas (15 itens por página)
5. **Uploads**: Tamanho máximo de arquivo: 2MB para avatars, 5MB para logos
6. **GO Coins**: Calculados automaticamente na conclusão de campanhas
7. **Termos**: Obrigatório aceitar Termo de Confidencialidade para criar/aplicar campanhas
8. **Assinatura**: Empresa precisa de assinatura ativa para criar campanhas

---

**🎉 Documentação Completa! Pronto para implementar todo o sistema no frontend Vue.js!**
