# 🚀 GO PUBLI - Instalação e Configuração

## 📋 Novas Funcionalidades Implementadas

Todas as funcionalidades do escopo do PWA foram implementadas:

### ✅ Sistema de Campanhas
- CRUD completo de campanhas para empresas
- Valor mínimo de R$ 200,00
- Distribuição automática 60/20/20
- Sistema de candidaturas estilo InDriver
- Proteção de dados até pagamento confirmado

### ✅ Sistema GO Coin
- Carteira digital para empresas e influencers
- Histórico de transações
- Sistema de resgate para serviços de marketing
- Bonificação automática (5% ao concluir campanha)

### ✅ Sistema de Assinatura
- Mensalidade mínima de R$ 200,00
- Bloqueio automático em caso de inadimplência
- Controle de períodos e renovação

### ✅ Termos e Segurança
- Termo de Confidencialidade obrigatório
- Registro de aceites com IP e user agent
- Bloqueio de contato externo

## 🔧 Instalação

### 1. Executar as novas migrations

```bash
php artisan migrate
```

Isso criará as seguintes tabelas:
- `campaigns` - Campanhas das empresas
- `campaign_applications` - Candidaturas dos influencers
- `go_coin_wallets` - Carteiras GO Coin
- `go_coin_transactions` - Transações GO Coin
- `terms_acceptances` - Aceites de termos
- `subscriptions` - Assinaturas das empresas

### 2. Popular com dados de teste (Opcional)

```bash
php artisan db:seed --class=GoPubliSeeder
```

Isso criará:
- 2 Empresas (com assinatura ativa)
- 3 Influencers
- 3 Campanhas (diferentes status)
- 4 Candidaturas
- Carteiras GO Coin para todos

### 3. Credenciais de Teste

**Empresas:**
- Email: `contato@techcorp.com.br` / Senha: `password123`
- Email: `contato@fashionstore.com.br` / Senha: `password123`

**Influencers:**
- Email: `maria@influencer.com` / Senha: `password123`
- Email: `joao@influencer.com` / Senha: `password123`
- Email: `ana@influencer.com` / Senha: `password123`

## 📚 Documentação

A documentação completa da API está em:
- **[PWA_API_DOCUMENTATION.md](PWA_API_DOCUMENTATION.md)** - Documentação completa dos novos endpoints

## 🎯 Endpoints Principais

### Para Empresas

```bash
# Dashboard
GET /api/company/campaigns/dashboard

# Criar campanha
POST /api/company/campaigns

# Confirmar pagamento
POST /api/company/campaigns/{id}/confirm-payment

# Aceitar influencer
POST /api/company/campaigns/{campaignId}/applications/{applicationId}/accept

# Gerenciar assinatura
GET /api/company/subscription/status
POST /api/company/subscription/confirm-payment

# GO Coin
GET /api/company/gocoin/balance
POST /api/company/gocoin/redeem
```

### Para Influencers

```bash
# Campanhas disponíveis
GET /api/influencer/campaigns/available

# Candidatar-se
POST /api/influencer/campaigns/{id}/apply

# Minhas candidaturas
GET /api/influencer/campaigns/my-applications

# Campanhas aceitas
GET /api/influencer/campaigns/my-campaigns

# GO Coin
GET /api/influencer/gocoin/balance
GET /api/influencer/gocoin/transactions
```

## 🔄 Fluxo da Aplicação

### Para Empresas:
1. Registrar → Aceitar termos → Criar assinatura
2. Confirmar pagamento da assinatura
3. Criar campanha (mín R$ 200)
4. Confirmar pagamento da campanha
5. Aguardar candidaturas dos influencers
6. Analisar candidatos e aceitar
7. Ver dados completos do influencer
8. Finalizar campanha
9. Usar GO Coins acumulados

### Para Influencers:
1. Registrar → Aceitar termos
2. Ver campanhas disponíveis
3. Candidatar-se com oferta personalizada
4. Aguardar aceite da empresa
5. Ver dados completos da empresa (após aceite)
6. Receber pagamento em GO Coin
7. Resgatar GO Coins para serviços

## 🛡️ Regras de Segurança Implementadas

- ✅ Dados do influencer ocultos até pagamento da campanha
- ✅ Dados da empresa ocultos até aceite da candidatura
- ✅ Termos de confidencialidade obrigatórios
- ✅ Bloqueio automático por inadimplência
- ✅ Validação de valores mínimos
- ✅ Proteção contra múltiplas candidaturas

## 📊 Models Criados

### Novos Models:
- `Campaign` - Campanhas
- `CampaignApplication` - Candidaturas
- `GoCoinWallet` - Carteira digital
- `GoCoinTransaction` - Transações
- `TermsAcceptance` - Aceites de termos
- `Subscription` - Assinaturas

### Models Atualizados:
- `Company` - Adicionados relacionamentos
- `Influencer` - Adicionados relacionamentos e métodos de proteção de dados

## 🎨 Controllers Criados

- `CompanyCampaignController` - Gestão de campanhas (empresas)
- `InfluencerCampaignController` - Campanhas (influencers)
- `GoCoinController` - Sistema GO Coin
- `TermsController` - Aceite de termos
- `SubscriptionController` - Gestão de assinaturas

## 🧪 Testes Recomendados

### Fluxo Completo - Empresa:
```bash
# 1. Login
POST /api/company/login

# 2. Aceitar termo
POST /api/company/terms/confidentiality

# 3. Criar assinatura
POST /api/company/subscription
POST /api/company/subscription/confirm-payment

# 4. Criar campanha
POST /api/company/campaigns

# 5. Confirmar pagamento
POST /api/company/campaigns/1/confirm-payment

# 6. Ver candidaturas
GET /api/company/campaigns/1/applications

# 7. Aceitar candidato
POST /api/company/campaigns/1/applications/1/accept
```

### Fluxo Completo - Influencer:
```bash
# 1. Login
POST /api/influencer/login

# 2. Aceitar termo
POST /api/influencer/terms/confidentiality

# 3. Ver campanhas
GET /api/influencer/campaigns/available

# 4. Candidatar-se
POST /api/influencer/campaigns/1/apply

# 5. Ver status
GET /api/influencer/campaigns/my-applications
```

## ⚙️ Configurações Adicionais

### .env
Nenhuma configuração adicional necessária. O sistema usa as mesmas configurações de banco de dados existentes.

### Storage
As migrations já configuram todos os relacionamentos necessários.

### Middleware
Os middlewares existentes (`auth:sanctum`, `type.company`, `type.influencer`) já protegem as rotas.

## 🚀 Próximas Etapas Sugeridas

1. **Integração com Gateway de Pagamento**
   - Implementar Asaas API
   - Webhooks de confirmação
   - Sistema de reembolso

2. **Notificações**
   - E-mail ao receber candidatura
   - Push notifications no PWA
   - Alertas de vencimento

3. **Dashboard Analytics**
   - Gráficos de performance
   - Métricas de ROI
   - Relatórios exportáveis

4. **Chat Interno**
   - Mensagens entre empresa e influencer
   - Apenas após aceite da candidatura

5. **Sistema de Avaliações**
   - Empresas avaliam influencers
   - Influencers avaliam empresas
   - Badge de qualidade

## 📞 Suporte

Para dúvidas sobre a implementação, consulte:
- **PWA_API_DOCUMENTATION.md** - Documentação completa
- **API_DOCUMENTATION.md** - Documentação de autenticação
- **PERMISSIONS_SYSTEM.md** - Sistema de permissões

---

**✨ Todas as funcionalidades do escopo PWA foram implementadas com sucesso!**

O sistema está pronto para:
- ✅ Empresas criarem e gerenciarem campanhas
- ✅ Influencers se candidatarem e receberem pagamentos
- ✅ Sistema GO Coin funcionando
- ✅ Controle de assinaturas e termos
- ✅ Segurança e proteção de dados
- ✅ Distribuição automática de valores (60/20/20)

*Desenvolvido para GO Publi - Janeiro 2026*
