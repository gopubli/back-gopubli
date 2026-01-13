# 🚀 GO PUBLI - API Completa do PWA

Documentação completa da API do sistema GO Publi para o App PWA.

## 📋 Índice

1. [Empresas - Campanhas](#empresas---campanhas)
2. [Empresas - Assinatura](#empresas---assinatura)
3. [Empresas - GO Coin](#empresas---go-coin)
4. [Empresas - Termos](#empresas---termos)
5. [Influencers - Campanhas](#influencers---campanhas)
6. [Influencers - GO Coin](#influencers---go-coin)
7. [Influencers - Termos](#influencers---termos)

---

## 🏢 Empresas - Campanhas

### Dashboard de Campanhas
```http
GET /api/company/campaigns/dashboard
Authorization: Bearer {token}
```

**Response:**
```json
{
  "total_campaigns": 10,
  "active_campaigns": 3,
  "completed_campaigns": 5,
  "total_spent": 5000.00,
  "pending_applications": 8
}
```

---

### Listar Campanhas da Empresa
```http
GET /api/company/campaigns
Authorization: Bearer {token}
```

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "title": "Campanha Teste",
      "description": "Descrição da campanha",
      "objective": "branding",
      "amount": 500.00,
      "influencer_amount": 300.00,
      "gopubli_commission": 100.00,
      "marketing_budget": 100.00,
      "status": "open",
      "payment_status": "paid",
      "blocked": false,
      "applications_count": 5,
      "created_at": "2026-01-13T10:00:00.000000Z"
    }
  ],
  "links": {},
  "meta": {}
}
```

---

### Criar Nova Campanha
```http
POST /api/company/campaigns
Authorization: Bearer {token}
```

**Body:**
```json
{
  "title": "Minha Campanha",
  "description": "Descrição detalhada da campanha",
  "objective": "branding",
  "amount": 500.00
}
```

**Validações:**
- `title`: obrigatório, máximo 255 caracteres
- `description`: obrigatório
- `objective`: obrigatório, valores: branding, traffic, conversion
- `amount`: obrigatório, mínimo R$ 200,00

**Response:**
```json
{
  "message": "Campanha criada com sucesso. Proceda com o pagamento para ativá-la.",
  "campaign": {
    "id": 1,
    "title": "Minha Campanha",
    "status": "draft",
    "payment_status": "pending",
    "amount": 500.00,
    "influencer_amount": 300.00,
    "gopubli_commission": 100.00,
    "marketing_budget": 100.00
  }
}
```

---

### Ver Campanha Específica
```http
GET /api/company/campaigns/{id}
Authorization: Bearer {token}
```

---

### Atualizar Campanha
```http
PUT /api/company/campaigns/{id}
Authorization: Bearer {token}
```

**Body:**
```json
{
  "title": "Título Atualizado",
  "description": "Nova descrição",
  "objective": "conversion",
  "amount": 600.00
}
```

**Nota:** Não é possível editar campanhas em andamento ou concluídas.

---

### Confirmar Pagamento da Campanha
```http
POST /api/company/campaigns/{id}/confirm-payment
Authorization: Bearer {token}
```

**Response:**
```json
{
  "message": "Pagamento confirmado! Sua campanha está disponível para influencers.",
  "campaign": {}
}
```

---

### Listar Candidaturas da Campanha
```http
GET /api/company/campaigns/{id}/applications
Authorization: Bearer {token}
```

**Response:**
```json
[
  {
    "id": 1,
    "campaign_id": 1,
    "influencer_id": 5,
    "offered_amount": 280.00,
    "proposal_message": "Tenho experiência neste nicho",
    "status": "pending",
    "influencer": {
      "id": 5,
      "name": "Maria Silva",
      "bio": "Criadora de conteúdo...",
      "avatar": "url"
    }
  }
]
```

---

### Aceitar Candidatura
```http
POST /api/company/campaigns/{campaignId}/applications/{applicationId}/accept
Authorization: Bearer {token}
```

**Response:**
```json
{
  "message": "Influencer selecionado com sucesso!",
  "campaign": {},
  "influencer": {
    "id": 5,
    "name": "Maria Silva",
    "email": "maria@email.com",
    "phone": "11999999999",
    "instagram": "@maria_silva"
  }
}
```

**Nota:** Após aceitar, a empresa pode ver os dados completos do influencer.

---

### Rejeitar Candidatura
```http
POST /api/company/campaigns/{campaignId}/applications/{applicationId}/reject
Authorization: Bearer {token}
```

**Body:**
```json
{
  "reason": "Perfil não corresponde ao público-alvo"
}
```

---

### Finalizar Campanha
```http
POST /api/company/campaigns/{id}/complete
Authorization: Bearer {token}
```

**Response:**
```json
{
  "message": "Campanha finalizada com sucesso!",
  "campaign": {}
}
```

**Nota:** Ao finalizar, o influencer recebe o pagamento em GO Coins + bônus de 5%.

---

### Cancelar Campanha
```http
POST /api/company/campaigns/{id}/cancel
Authorization: Bearer {token}
```

---

## 💳 Empresas - Assinatura

### Ver Assinatura
```http
GET /api/company/subscription
Authorization: Bearer {token}
```

**Response:**
```json
{
  "subscription": {
    "id": 1,
    "company_id": 1,
    "monthly_amount": 200.00,
    "status": "active",
    "current_period_start": "2026-01-01",
    "current_period_end": "2026-02-01",
    "next_billing_date": "2026-02-01"
  },
  "is_overdue": false,
  "days_overdue": 0
}
```

---

### Criar Assinatura
```http
POST /api/company/subscription
Authorization: Bearer {token}
```

**Body:**
```json
{
  "monthly_amount": 200.00
}
```

**Validação:** Valor mínimo R$ 200,00

---

### Confirmar Pagamento da Assinatura
```http
POST /api/company/subscription/confirm-payment
Authorization: Bearer {token}
```

---

### Renovar Assinatura
```http
POST /api/company/subscription/renew
Authorization: Bearer {token}
```

---

### Verificar Status da Assinatura
```http
GET /api/company/subscription/status
Authorization: Bearer {token}
```

**Response:**
```json
{
  "has_subscription": true,
  "is_active": true,
  "is_overdue": false,
  "days_overdue": 0,
  "status": "active",
  "next_billing_date": "2026-02-01",
  "monthly_amount": 200.00
}
```

---

## 🪙 Empresas - GO Coin

### Ver Saldo
```http
GET /api/company/gocoin/balance
Authorization: Bearer {token}
```

**Response:**
```json
{
  "balance": 500.00,
  "total_earned": 1000.00,
  "total_spent": 500.00
}
```

---

### Histórico de Transações
```http
GET /api/company/gocoin/transactions
Authorization: Bearer {token}
```

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "type": "credit",
      "amount": 100.00,
      "category": "campaign_bonus",
      "description": "Bônus da campanha X",
      "balance_before": 400.00,
      "balance_after": 500.00,
      "created_at": "2026-01-13T10:00:00Z"
    }
  ]
}
```

---

### Filtrar por Tipo
```http
GET /api/company/gocoin/transactions/type/{type}
Authorization: Bearer {token}
```

Tipos disponíveis: `credit`, `debit`

---

### Resgatar GO Coins
```http
POST /api/company/gocoin/redeem
Authorization: Bearer {token}
```

**Body:**
```json
{
  "amount": 100.00,
  "service_type": "trafego_pago",
  "description": "Investir em anúncios Facebook"
}
```

---

### Categorias de Resgate
```http
GET /api/company/gocoin/redeem-categories
Authorization: Bearer {token}
```

**Response:**
```json
[
  {
    "id": "trafego_pago",
    "name": "Tráfego Pago",
    "description": "Invista em anúncios no Facebook, Instagram, Google Ads",
    "min_amount": 50
  },
  {
    "id": "design",
    "name": "Design Gráfico",
    "description": "Criação de artes, logos, banners",
    "min_amount": 30
  }
]
```

---

### Estatísticas GO Coin
```http
GET /api/company/gocoin/stats
Authorization: Bearer {token}
```

---

## 📄 Empresas - Termos

### Aceitar Termo de Confidencialidade
```http
POST /api/company/terms/confidentiality
Authorization: Bearer {token}
```

**Body:**
```json
{
  "accepted": true
}
```

**Nota:** Obrigatório para criar campanhas.

---

### Ver Texto do Termo
```http
GET /api/company/terms/confidentiality/text
Authorization: Bearer {token}
```

---

### Verificar Status dos Termos
```http
GET /api/company/terms/status
Authorization: Bearer {token}
```

**Response:**
```json
{
  "confidentiality": true,
  "privacy_policy": false,
  "terms_of_use": false
}
```

---

## 🎬 Influencers - Campanhas

### Dashboard
```http
GET /api/influencer/campaigns/dashboard
Authorization: Bearer {token}
```

**Response:**
```json
{
  "total_applications": 10,
  "pending_applications": 5,
  "accepted_applications": 3,
  "active_campaigns": 2,
  "completed_campaigns": 5,
  "total_earned": 2500.00,
  "current_balance": 800.00
}
```

---

### Listar Campanhas Disponíveis
```http
GET /api/influencer/campaigns/available
Authorization: Bearer {token}
```

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "title": "Campanha Teste",
      "description": "Descrição",
      "objective": "branding",
      "amount": 500.00,
      "status": "open",
      "has_applied": false,
      "applications_count": 3,
      "company": {
        "id": 1,
        "name": "Empresa Teste",
        "logo": "url"
      }
    }
  ]
}
```

**Nota:** Dados sensíveis da empresa ficam ocultos até aceite.

---

### Ver Campanha Disponível
```http
GET /api/influencer/campaigns/{id}
Authorization: Bearer {token}
```

---

### Candidatar-se a Campanha
```http
POST /api/influencer/campaigns/{id}/apply
Authorization: Bearer {token}
```

**Body:**
```json
{
  "offered_amount": 280.00,
  "proposal_message": "Tenho experiência neste nicho e engajamento alto!"
}
```

**Nota:** Modelo estilo InDriver - influencer pode ofertar valor.

---

### Minhas Candidaturas
```http
GET /api/influencer/campaigns/my-applications
Authorization: Bearer {token}
```

---

### Minhas Campanhas (Aceitas)
```http
GET /api/influencer/campaigns/my-campaigns
Authorization: Bearer {token}
```

---

### Ver Campanha Aceita (Dados Completos)
```http
GET /api/influencer/my-campaigns/{id}
Authorization: Bearer {token}
```

**Response:**
```json
{
  "campaign": {},
  "company_full_data": {
    "id": 1,
    "name": "Empresa Teste",
    "email": "contato@empresa.com",
    "phone": "11999999999",
    "cnpj": "12345678000190",
    "address": "Endereço completo"
  }
}
```

---

### Retirar Candidatura
```http
POST /api/influencer/applications/{id}/withdraw
Authorization: Bearer {token}
```

---

### Atualizar Proposta
```http
PUT /api/influencer/applications/{id}
Authorization: Bearer {token}
```

**Body:**
```json
{
  "offered_amount": 300.00,
  "proposal_message": "Nova proposta atualizada"
}
```

---

## 🪙 Influencers - GO Coin

Mesmas rotas das empresas:
- `/api/influencer/gocoin/balance`
- `/api/influencer/gocoin/transactions`
- `/api/influencer/gocoin/redeem`
- `/api/influencer/gocoin/stats`

---

## 📄 Influencers - Termos

Mesmas rotas das empresas:
- `/api/influencer/terms/confidentiality`
- `/api/influencer/terms/status`

---

## 🔒 Regras de Negócio Implementadas

### ✅ Segurança e Privacidade
- Dados do influencer ocultos até pagamento confirmado
- Dados da empresa ocultos até aceite da candidatura
- Termos de confidencialidade obrigatórios
- Bloqueio de contato externo

### ✅ Financeiro
- Valor mínimo de campanha: R$ 200,00
- Mensalidade mínima da empresa: R$ 200,00
- Distribuição automática: 60% influencer / 20% GO Publi / 20% marketing
- Sistema GO Coin com resgate para serviços de marketing
- Bônus de 5% para influencers ao concluir campanha

### ✅ Status de Campanhas
- **draft**: Rascunho (pagamento pendente)
- **open**: Aberta para candidaturas
- **in_progress**: Em andamento (influencer selecionado)
- **completed**: Finalizada
- **cancelled**: Cancelada
- **blocked**: Bloqueada por inadimplência

### ✅ Assinatura
- Bloqueio automático de campanhas em caso de atraso
- Cálculo de dias de atraso
- Sistema de renovação e reativação

---

## 🎯 Próximos Passos

### Integração de Pagamento
- Integrar com Asaas API
- Implementar webhooks de pagamento
- Sistema de reembolso automatizado

### Notificações
- E-mail ao receber candidatura
- Push notification no PWA
- Alertas de vencimento de assinatura

### Analytics
- Dashboard com gráficos de performance
- Métricas de ROI das campanhas
- Relatórios detalhados

---

## 🧪 Testes

Para popular o banco com dados de teste:

```bash
php artisan db:seed --class=GoPubliSeeder
```

### Credenciais de Teste:

**Empresa 1:**
- Email: contato@techcorp.com.br
- Senha: password123

**Influencer 1:**
- Email: maria@influencer.com
- Senha: password123

---

## 📝 Notas de Desenvolvimento

- Todas as migrations estão prontas
- Models com relacionamentos configurados
- Controllers com validações completas
- Rotas protegidas por middleware
- Sistema de permissões integrado (RBAC)
- Soft deletes habilitado em campanhas

---

**Desenvolvido para GO Publi - Versão Beta PWA**
*Janeiro 2026*
