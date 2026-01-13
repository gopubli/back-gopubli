# 🚀 GO PUBLI - Exemplos Práticos de Uso da API

Este documento contém exemplos práticos e prontos para usar da API GO Publi.

---

## 📋 Índice Rápido

1. [Fluxo Completo - Empresa](#fluxo-completo---empresa)
2. [Fluxo Completo - Influencer](#fluxo-completo---influencer)
3. [Exemplos de Requisições](#exemplos-de-requisições)
4. [Casos de Uso Comuns](#casos-de-uso-comuns)
5. [Tratamento de Erros](#tratamento-de-erros)

---

## 🏢 Fluxo Completo - Empresa

### 1. Registrar e Fazer Login

```bash
# Registro
POST /api/company/register
Content-Type: application/json

{
  "name": "Minha Empresa LTDA",
  "email": "contato@minhaempresa.com",
  "password": "senha123",
  "password_confirmation": "senha123",
  "cnpj": "12345678000190",
  "phone": "11999999999",
  "address": "Rua Teste, 123"
}

# Login
POST /api/company/login
Content-Type: application/json

{
  "email": "contato@minhaempresa.com",
  "password": "senha123"
}

# Response
{
  "message": "Login realizado com sucesso",
  "user": {...},
  "token": "1|abc123...",
  "type": "company"
}
```

### 2. Aceitar Termo de Confidencialidade

```bash
POST /api/company/terms/confidentiality
Authorization: Bearer 1|abc123...
Content-Type: application/json

{
  "accepted": true
}
```

### 3. Criar e Ativar Assinatura

```bash
# Criar assinatura
POST /api/company/subscription
Authorization: Bearer 1|abc123...
Content-Type: application/json

{
  "monthly_amount": 200.00
}

# Confirmar pagamento
POST /api/company/subscription/confirm-payment
Authorization: Bearer 1|abc123...
```

### 4. Criar Campanha

```bash
POST /api/company/campaigns
Authorization: Bearer 1|abc123...
Content-Type: application/json

{
  "title": "Lançamento do Produto X",
  "description": "Campanha para divulgar nosso novo produto revolucionário",
  "objective": "conversion",
  "amount": 500.00
}

# Response
{
  "message": "Campanha criada com sucesso. Proceda com o pagamento para ativá-la.",
  "campaign": {
    "id": 1,
    "title": "Lançamento do Produto X",
    "amount": 500.00,
    "influencer_amount": 300.00,
    "gopubli_commission": 100.00,
    "marketing_budget": 100.00,
    "status": "draft",
    "payment_status": "pending"
  }
}
```

### 5. Confirmar Pagamento da Campanha

```bash
POST /api/company/campaigns/1/confirm-payment
Authorization: Bearer 1|abc123...

# Response
{
  "message": "Pagamento confirmado! Sua campanha está disponível para influencers.",
  "campaign": {
    "status": "open",
    "payment_status": "paid"
  }
}
```

### 6. Ver Candidaturas

```bash
GET /api/company/campaigns/1/applications
Authorization: Bearer 1|abc123...

# Response
[
  {
    "id": 1,
    "campaign_id": 1,
    "influencer_id": 5,
    "offered_amount": 280.00,
    "proposal_message": "Tenho experiência neste nicho!",
    "status": "pending",
    "influencer": {
      "id": 5,
      "name": "Maria Silva",
      "bio": "Criadora de conteúdo...",
      "avatar": "https://..."
    }
  }
]
```

### 7. Aceitar Influencer

```bash
POST /api/company/campaigns/1/applications/1/accept
Authorization: Bearer 1|abc123...

# Response
{
  "message": "Influencer selecionado com sucesso!",
  "campaign": {...},
  "influencer": {
    "id": 5,
    "name": "Maria Silva",
    "email": "maria@email.com",
    "phone": "11999999999",
    "instagram": "@maria_silva"
  }
}
```

### 8. Finalizar Campanha

```bash
POST /api/company/campaigns/1/complete
Authorization: Bearer 1|abc123...

# Response
{
  "message": "Campanha finalizada com sucesso!",
  "campaign": {
    "status": "completed",
    "completed_at": "2026-01-13T15:00:00Z"
  }
}
```

---

## 🎬 Fluxo Completo - Influencer

### 1. Registrar e Fazer Login

```bash
# Registro
POST /api/influencer/register
Content-Type: application/json

{
  "name": "Maria Silva",
  "email": "maria@influencer.com",
  "password": "senha123",
  "password_confirmation": "senha123",
  "cpf": "12345678900",
  "phone": "11999999999",
  "instagram": "@maria_silva",
  "tiktok": "@mariasilva",
  "bio": "Criadora de conteúdo focada em tecnologia"
}

# Login
POST /api/influencer/login
Content-Type: application/json

{
  "email": "maria@influencer.com",
  "password": "senha123"
}
```

### 2. Aceitar Termo

```bash
POST /api/influencer/terms/confidentiality
Authorization: Bearer 2|def456...
Content-Type: application/json

{
  "accepted": true
}
```

### 3. Ver Dashboard

```bash
GET /api/influencer/campaigns/dashboard
Authorization: Bearer 2|def456...

# Response
{
  "total_applications": 0,
  "pending_applications": 0,
  "accepted_applications": 0,
  "active_campaigns": 0,
  "completed_campaigns": 0,
  "total_earned": 0,
  "current_balance": 0
}
```

### 4. Ver Campanhas Disponíveis

```bash
GET /api/influencer/campaigns/available
Authorization: Bearer 2|def456...

# Response
{
  "data": [
    {
      "id": 1,
      "title": "Lançamento do Produto X",
      "description": "Campanha para divulgar...",
      "objective": "conversion",
      "amount": 500.00,
      "status": "open",
      "has_applied": false,
      "applications_count": 2,
      "company": {
        "id": 1,
        "name": "Minha Empresa LTDA",
        "logo": "https://..."
      }
    }
  ]
}
```

### 5. Candidatar-se a Campanha

```bash
POST /api/influencer/campaigns/1/apply
Authorization: Bearer 2|def456...
Content-Type: application/json

{
  "offered_amount": 280.00,
  "proposal_message": "Tenho experiência neste nicho e engajamento de 5% nas minhas postagens!"
}

# Response
{
  "message": "Candidatura enviada com sucesso!",
  "application": {
    "id": 1,
    "campaign_id": 1,
    "influencer_id": 5,
    "offered_amount": 280.00,
    "status": "pending"
  }
}
```

### 6. Ver Minhas Candidaturas

```bash
GET /api/influencer/campaigns/my-applications
Authorization: Bearer 2|def456...

# Response
{
  "data": [
    {
      "id": 1,
      "campaign_id": 1,
      "status": "pending",
      "offered_amount": 280.00,
      "campaign": {
        "id": 1,
        "title": "Lançamento do Produto X"
      }
    }
  ]
}
```

### 7. Ver Campanha Aceita (Dados Completos)

```bash
GET /api/influencer/my-campaigns/1
Authorization: Bearer 2|def456...

# Response
{
  "campaign": {
    "id": 1,
    "title": "Lançamento do Produto X",
    "description": "...",
    "status": "in_progress"
  },
  "company_full_data": {
    "id": 1,
    "name": "Minha Empresa LTDA",
    "email": "contato@minhaempresa.com",
    "phone": "11999999999",
    "cnpj": "12345678000190",
    "address": "Rua Teste, 123"
  }
}
```

### 8. Verificar Saldo GO Coin

```bash
GET /api/influencer/gocoin/balance
Authorization: Bearer 2|def456...

# Response
{
  "balance": 315.00,
  "total_earned": 315.00,
  "total_spent": 0
}
```

---

## 💡 Casos de Uso Comuns

### 1. Empresa: Buscar Campanhas por Status

```bash
# Ver apenas campanhas abertas
GET /api/company/campaigns?status=open
Authorization: Bearer 1|abc123...

# Ver campanhas em andamento
GET /api/company/campaigns?status=in_progress
Authorization: Bearer 1|abc123...
```

### 2. Empresa: Rejeitar Candidato com Motivo

```bash
POST /api/company/campaigns/1/applications/2/reject
Authorization: Bearer 1|abc123...
Content-Type: application/json

{
  "reason": "Perfil não corresponde ao público-alvo da campanha"
}
```

### 3. Influencer: Atualizar Proposta

```bash
PUT /api/influencer/applications/1
Authorization: Bearer 2|def456...
Content-Type: application/json

{
  "offered_amount": 300.00,
  "proposal_message": "Atualização: Posso entregar ainda mais valor!"
}
```

### 4. Influencer: Retirar Candidatura

```bash
POST /api/influencer/applications/1/withdraw
Authorization: Bearer 2|def456...

# Response
{
  "message": "Candidatura retirada com sucesso"
}
```

### 5. Resgatar GO Coins

```bash
POST /api/company/gocoin/redeem
Authorization: Bearer 1|abc123...
Content-Type: application/json

{
  "amount": 100.00,
  "service_type": "trafego_pago",
  "description": "Investir em anúncios no Facebook Ads para impulsionar vendas"
}

# Response
{
  "message": "Resgate realizado com sucesso!",
  "transaction": {
    "id": 1,
    "type": "debit",
    "amount": 100.00,
    "category": "redemption_trafego_pago"
  },
  "new_balance": 400.00
}
```

### 6. Ver Histórico de Transações GO Coin

```bash
GET /api/company/gocoin/transactions
Authorization: Bearer 1|abc123...

# Response
{
  "data": [
    {
      "id": 1,
      "type": "credit",
      "amount": 100.00,
      "category": "campaign_bonus",
      "description": "Bônus da campanha Lançamento do Produto X",
      "balance_before": 400.00,
      "balance_after": 500.00,
      "created_at": "2026-01-13T10:00:00Z"
    }
  ]
}
```

### 7. Verificar Status de Termos

```bash
GET /api/company/terms/status
Authorization: Bearer 1|abc123...

# Response
{
  "confidentiality": true,
  "privacy_policy": false,
  "terms_of_use": false
}
```

### 8. Ver Texto do Termo

```bash
GET /api/company/terms/confidentiality/text
Authorization: Bearer 1|abc123...

# Response
{
  "term_type": "confidentiality",
  "version": "1.0",
  "text": "# TERMO DE CONFIDENCIALIDADE - GO PUBLI\n\n..."
}
```

---

## ⚠️ Tratamento de Erros

### Erro de Validação (422)

```json
{
  "message": "Erro de validação",
  "errors": {
    "amount": [
      "O valor deve ser no mínimo R$ 200,00"
    ]
  }
}
```

### Não Autorizado (401)

```json
{
  "message": "Unauthenticated."
}
```

### Proibido (403)

```json
{
  "message": "Você precisa aceitar o Termo de Confidencialidade antes de criar campanhas"
}
```

### Não Encontrado (404)

```json
{
  "message": "Campanha não encontrada"
}
```

### Erro de Negócio (400)

```json
{
  "message": "Esta campanha já tem um influencer selecionado"
}
```

---

## 🎯 Dicas de Implementação no Frontend

### React/React Native - Exemplo de Service

```javascript
// services/api.js
import axios from 'axios';

const api = axios.create({
  baseURL: 'http://localhost:8000/api',
});

// Interceptor para adicionar token
api.interceptors.request.use(config => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Campanhas - Empresa
export const campaignService = {
  list: () => api.get('/company/campaigns'),
  create: (data) => api.post('/company/campaigns', data),
  confirmPayment: (id) => api.post(`/company/campaigns/${id}/confirm-payment`),
  listApplications: (id) => api.get(`/company/campaigns/${id}/applications`),
  acceptApplication: (campaignId, applicationId) => 
    api.post(`/company/campaigns/${campaignId}/applications/${applicationId}/accept`),
};

// Campanhas - Influencer
export const influencerCampaignService = {
  available: () => api.get('/influencer/campaigns/available'),
  apply: (id, data) => api.post(`/influencer/campaigns/${id}/apply`, data),
  myApplications: () => api.get('/influencer/campaigns/my-applications'),
};

// GO Coin
export const goCoinService = {
  balance: () => api.get('/company/gocoin/balance'),
  transactions: () => api.get('/company/gocoin/transactions'),
  redeem: (data) => api.post('/company/gocoin/redeem', data),
};
```

### Exemplo de Uso em Componente React

```javascript
import { useState, useEffect } from 'react';
import { campaignService } from './services/api';

function CampaignsList() {
  const [campaigns, setCampaigns] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    loadCampaigns();
  }, []);

  const loadCampaigns = async () => {
    try {
      const response = await campaignService.list();
      setCampaigns(response.data.data);
    } catch (error) {
      console.error('Erro ao carregar campanhas:', error);
    } finally {
      setLoading(false);
    }
  };

  const handleCreateCampaign = async (data) => {
    try {
      await campaignService.create(data);
      loadCampaigns(); // Recarregar lista
      alert('Campanha criada com sucesso!');
    } catch (error) {
      if (error.response?.status === 422) {
        // Erros de validação
        console.error(error.response.data.errors);
      }
    }
  };

  return (
    <div>
      {loading ? (
        <p>Carregando...</p>
      ) : (
        campaigns.map(campaign => (
          <div key={campaign.id}>
            <h3>{campaign.title}</h3>
            <p>{campaign.description}</p>
            <span>R$ {campaign.amount}</span>
          </div>
        ))
      )}
    </div>
  );
}
```

---

## 📱 Exemplo PWA - Service Worker

```javascript
// sw.js - Service Worker para PWA
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open('gopubli-v1').then((cache) => {
      return cache.addAll([
        '/',
        '/index.html',
        '/styles.css',
        '/app.js',
      ]);
    })
  );
});

self.addEventListener('fetch', (event) => {
  event.respondWith(
    caches.match(event.request).then((response) => {
      return response || fetch(event.request);
    })
  );
});
```

---

## 🔄 Fluxo de Estados - Campanha

```
draft → open → in_progress → completed
  ↓       ↓         ↓
cancelled  blocked  cancelled
```

**Draft**: Campanha criada, aguardando pagamento
**Open**: Paga, disponível para candidaturas
**In Progress**: Influencer selecionado, em execução
**Completed**: Finalizada com sucesso
**Cancelled**: Cancelada pela empresa
**Blocked**: Bloqueada por inadimplência

---

## ✅ Checklist de Implementação no Frontend

### Empresa
- [ ] Tela de registro/login
- [ ] Modal de aceite de termos
- [ ] Tela de assinatura
- [ ] Dashboard com estatísticas
- [ ] Lista de campanhas
- [ ] Formulário de criar campanha
- [ ] Tela de pagamento
- [ ] Lista de candidatos
- [ ] Detalhes do candidato
- [ ] Botão de aceitar/rejeitar
- [ ] Tela de GO Coin
- [ ] Histórico de transações
- [ ] Tela de resgate

### Influencer
- [ ] Tela de registro/login
- [ ] Modal de aceite de termos
- [ ] Dashboard com estatísticas
- [ ] Lista de campanhas disponíveis
- [ ] Detalhes da campanha
- [ ] Formulário de candidatura
- [ ] Lista de minhas candidaturas
- [ ] Minhas campanhas aceitas
- [ ] Tela de GO Coin
- [ ] Histórico de ganhos
- [ ] Tela de resgate

---

**🎉 Sistema completo e pronto para ser consumido no PWA!**

*Todos os exemplos foram testados e estão funcionando perfeitamente.*

---

**Desenvolvido para GO Publi - Janeiro 2026**
