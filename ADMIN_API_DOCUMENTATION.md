# API Admin - Documentação

## Estrutura do Módulo Admin

Este módulo foi desenvolvido seguindo princípios **SOLID** e **Design Patterns**, com uma arquitetura em camadas:

### Camadas

```
┌─────────────────────────────────────┐
│         Controllers                 │  ← Apenas retornos HTTP
├─────────────────────────────────────┤
│         Services                    │  ← Lógica de negócio
├─────────────────────────────────────┤
│         Repositories                │  ← Acesso a dados
├─────────────────────────────────────┤
│         Models                      │  ← Eloquent Models
└─────────────────────────────────────┘
```

### Princípios SOLID Aplicados

1. **Single Responsibility Principle (SRP)**
   - Cada classe tem uma única responsabilidade
   - Controllers: apenas retornam respostas HTTP
   - Services: contém lógica de negócio
   - Repositories: gerenciam acesso aos dados

2. **Open/Closed Principle (OCP)**
   - BaseRepository permite extensão sem modificação
   - Filtros aplicados via método protegido applyFilters()

3. **Liskov Substitution Principle (LSP)**
   - Todos os repositories estendem BaseRepository
   - Podem ser substituídos sem quebrar a aplicação

4. **Interface Segregation Principle (ISP)**
   - Métodos específicos em cada repository
   - Nenhum repositório implementa métodos desnecessários

5. **Dependency Inversion Principle (DIP)**
   - Controllers dependem de Services (abstrações)
   - Services dependem de Repositories (abstrações)
   - Injeção de dependências via construtor

### Design Patterns Utilizados

1. **Repository Pattern**
   - Abstração do acesso aos dados
   - Facilita testes e manutenção

2. **Service Layer Pattern**
   - Centraliza lógica de negócio
   - Reutilização de código

3. **Dependency Injection**
   - Inversão de controle
   - Facilita testes unitários

## Endpoints da API

### Base URL
```
/api/v1/admin
```

### Autenticação
Todas as rotas requerem:
- Header: `Authorization: Bearer {token}`
- Middleware: `type.administrator`

---

## 1. Gerenciamento de Usuários

### 1.1 Influenciadores

#### Listar Influenciadores
```http
GET /admin/influencers
```

**Query Parameters:**
- `search` (string): Busca por nome ou email
- `status` (string): active, inactive, pending, blocked
- `per_page` (int): Itens por página (padrão: 10)
- `page` (int): Página atual

**Resposta:**
```json
{
  "data": [...],
  "current_page": 1,
  "last_page": 5,
  "per_page": 10,
  "total": 50
}
```

#### Ver Detalhes do Influenciador
```http
GET /admin/influencers/{id}
```

#### Atualizar Influenciador
```http
PUT /admin/influencers/{id}
```

**Body:**
```json
{
  "name": "Nome",
  "email": "email@example.com",
  "status": "active"
}
```

#### Deletar Influenciador
```http
DELETE /admin/influencers/{id}
```

#### Bloquear Influenciador
```http
POST /admin/influencers/{id}/block
```

#### Desbloquear Influenciador
```http
POST /admin/influencers/{id}/unblock
```

### 1.2 Empresas

#### Listar Empresas
```http
GET /admin/companies
```

**Query Parameters:**
- `search` (string)
- `status` (string)
- `subscription_status` (string): active, expired, cancelled, pending
- `per_page` (int)
- `page` (int)

#### Ver Detalhes da Empresa
```http
GET /admin/companies/{id}
```

#### Atualizar Empresa
```http
PUT /admin/companies/{id}
```

#### Deletar Empresa
```http
DELETE /admin/companies/{id}
```

#### Bloquear Empresa
```http
POST /admin/companies/{id}/block
```

#### Desbloquear Empresa
```http
POST /admin/companies/{id}/unblock
```

---

## 2. Gerenciamento de Campanhas

#### Listar Campanhas
```http
GET /admin/campaigns
```

**Query Parameters:**
- `search` (string)
- `status` (string): draft, pending_payment, active, in_progress, completed, cancelled
- `company_id` (int)
- `start_date` (date)
- `end_date` (date)
- `per_page` (int)

#### Ver Detalhes da Campanha
```http
GET /admin/campaigns/{id}
```

#### Atualizar Campanha
```http
PUT /admin/campaigns/{id}
```

**Body:**
```json
{
  "title": "Título",
  "description": "Descrição",
  "status": "active",
  "budget": 5000
}
```

#### Cancelar Campanha
```http
POST /admin/campaigns/{id}/cancel
```

**Body:**
```json
{
  "reason": "Motivo do cancelamento"
}
```

#### Deletar Campanha
```http
DELETE /admin/campaigns/{id}
```

#### Estatísticas de Campanhas
```http
GET /admin/campaigns/statistics
```

**Resposta:**
```json
{
  "data": {
    "total": 100,
    "by_status": {
      "active": 30,
      "completed": 50,
      "cancelled": 10
    },
    "active": 30,
    "success_rate": 83.33,
    "average_budget": 3500,
    "total_budget": 350000
  }
}
```

#### Top Campanhas
```http
GET /admin/campaigns/top?limit=10
```

---

## 3. Gerenciamento de Candidaturas

#### Listar Candidaturas
```http
GET /admin/applications
```

**Query Parameters:**
- `search` (string)
- `status` (string): pending, accepted, rejected, withdrawn
- `campaign_id` (int)
- `influencer_id` (int)
- `per_page` (int)

#### Ver Detalhes da Candidatura
```http
GET /admin/applications/{id}
```

#### Atualizar Status da Candidatura
```http
PUT /admin/applications/{id}/status
```

**Body:**
```json
{
  "status": "accepted",
  "reason": "Motivo (opcional)"
}
```

#### Estatísticas de Candidaturas
```http
GET /admin/applications/statistics
```

---

## 4. Gerenciamento de GO Coins

#### Listar Transações
```http
GET /admin/gocoins/transactions
```

**Query Parameters:**
- `type` (string): earn, redeem
- `category` (string)
- `wallet_id` (int)
- `start_date` (date)
- `end_date` (date)
- `per_page` (int)

#### Estatísticas GO Coins
```http
GET /admin/gocoins/statistics
```

**Resposta:**
```json
{
  "data": {
    "total_transactions": 5000,
    "total_coins_in_circulation": 150000,
    "total_coins_earned": 200000,
    "total_coins_redeemed": 50000,
    "by_type": {
      "earn": {"count": 3000, "total_amount": 200000},
      "redeem": {"count": 2000, "total_amount": 50000}
    },
    "by_category": {...}
  }
}
```

#### Transações Recentes
```http
GET /admin/gocoins/recent?limit=20
```

#### Detalhes da Wallet
```http
GET /admin/gocoins/wallet/{walletId}
```

#### Ajustar Saldo da Wallet
```http
POST /admin/gocoins/wallet/{walletId}/adjust
```

**Body:**
```json
{
  "amount": 100,
  "reason": "Ajuste manual por [motivo]"
}
```

---

## 5. Relatórios

#### Dashboard
```http
GET /admin/reports/dashboard
```

**Resposta:**
```json
{
  "data": {
    "total_companies": 100,
    "active_companies": 85,
    "total_influencers": 500,
    "active_influencers": 450,
    "total_campaigns": 200,
    "active_campaigns": 50,
    "total_applications": 1000,
    "total_revenue": 500000,
    "platform_fee_revenue": 50000,
    "monthly_revenue": 45000,
    "growth_rate": 15.5
  }
}
```

#### Relatório de Receitas
```http
GET /admin/reports/revenue?start_date=2024-01-01&end_date=2024-12-31
```

**Resposta:**
```json
{
  "data": [
    {
      "period": "2024-01",
      "total_revenue": 45000,
      "platform_fees": 35000,
      "subscription_revenue": 10000,
      "campaign_revenue": 350000,
      "transactions_count": 50
    }
  ]
}
```

#### Relatório de Campanhas
```http
GET /admin/reports/campaigns?start_date=2024-01-01&end_date=2024-12-31
```

#### Crescimento de Usuários
```http
GET /admin/reports/users/growth?period=month
```

**Período:** week, month, year

#### Estatísticas de Usuários
```http
GET /admin/statistics/users
```

---

## Códigos de Status HTTP

- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error

## Exemplos de Uso

### Usando cURL

```bash
# Listar influenciadores
curl -X GET "http://localhost/api/v1/admin/influencers?per_page=10" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"

# Cancelar campanha
curl -X POST "http://localhost/api/v1/admin/campaigns/1/cancel" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"reason": "Solicitação da empresa"}'

# Ajustar GO Coins
curl -X POST "http://localhost/api/v1/admin/gocoins/wallet/1/adjust" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"amount": 100, "reason": "Bônus especial"}'
```

### Usando JavaScript (Axios)

```javascript
// Dashboard
const dashboard = await axios.get('/api/v1/admin/reports/dashboard', {
  headers: { Authorization: `Bearer ${token}` }
})

// Atualizar status de candidatura
await axios.put('/api/v1/admin/applications/1/status', {
  status: 'accepted',
  reason: 'Perfil adequado'
}, {
  headers: { Authorization: `Bearer ${token}` }
})
```

## Estrutura de Arquivos

```
app/
├── Http/Controllers/Api/Admin/
│   ├── CampaignManagementController.php
│   ├── ApplicationManagementController.php
│   ├── UserManagementController.php
│   ├── GoCoinManagementController.php
│   └── ReportController.php
├── Services/Admin/
│   ├── CampaignManagementService.php
│   ├── ApplicationManagementService.php
│   ├── UserManagementService.php
│   ├── GoCoinManagementService.php
│   └── ReportService.php
├── Repositories/
│   ├── BaseRepository.php
│   └── Admin/
│       ├── CampaignRepository.php
│       ├── ApplicationRepository.php
│       ├── InfluencerRepository.php
│       ├── CompanyRepository.php
│       └── GoCoinRepository.php
└── Providers/
    └── AdminServiceProvider.php
```

## Testes

Para testar as APIs, você pode usar:
- Postman/Insomnia (collections incluídas)
- PHPUnit para testes automatizados
- Pest para testes mais expressivos

## Próximos Passos

1. Implementar cache para relatórios pesados
2. Adicionar jobs para processamento assíncrono
3. Implementar notificações para ações importantes
4. Adicionar logs de auditoria
5. Criar exportação de relatórios em Excel/PDF
6. Implementar WebSockets para atualizações em tempo real
