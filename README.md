# 🚀 GoPubli API

API REST completa para o sistema GoPubli - Plataforma de conexão entre Empresas e Influenciadores.

## 📋 Sobre o Projeto

O GoPubli é uma API backend desenvolvida em Laravel que gerencia o sistema de autenticação e operações para três tipos de usuários:

- **👨‍💼 Administradores** - Acesso ao painel administrativo
- **🏢 Empresas** - Acesso mobile para criar campanhas
- **🎬 Influencers** - Acesso mobile para participar de campanhas

## ✨ Funcionalidades Implementadas

### 🔐 Autenticação Multi-Tipo
- Login e registro separado para cada tipo de usuário
- Sistema de tokens com Laravel Sanctum
- Middleware específico para cada tipo de usuário
- Logout e revogação de tokens

### 📧 Verificação de E-mail
- Envio automático ao registrar
- Reenvio de e-mail de verificação
- Links de verificação com assinatura temporária
- Middleware para proteger rotas que exigem e-mail verificado

### 🔑 Recuperação de Senha
- Solicitação de reset de senha por e-mail
- Tokens temporários com expiração de 60 minutos
- Reset de senha com confirmação
- Revogação de todos os tokens ao resetar senha

### 📸 Upload de Avatar/Logo
- Upload de imagens para perfis
- Suporte a múltiplos formatos (JPEG, PNG, GIF, SVG)
- Validação de tamanho (max 2MB)
- Storage público com links diretos
- Remoção de imagens antigas ao atualizar

### 👤 Gerenciamento de Perfil
- Atualização de dados do perfil
- Campos específicos para cada tipo de usuário
- Validação de dados únicos (CPF, CNPJ)

### 🎯 Sistema de Campanhas (NOVO!)
- Criar campanhas com valor mínimo R$ 200,00
- Objetivos: branding, tráfego ou conversão
- Distribuição automática 60/20/20
- Sistema de candidaturas estilo InDriver
- Proteção de dados até pagamento confirmado

### 🪙 Sistema GO Coin (NOVO!)
- Carteira digital para empresas e influencers
- Histórico completo de transações
- Sistema de resgate para serviços de marketing
- Bonificação automática de 5%

### 💳 Sistema de Assinatura (NOVO!)
- Mensalidade mínima de R$ 200,00
- Controle de períodos e renovação
- Bloqueio automático por inadimplência
- Gerenciamento completo de status

### 📄 Termos e Segurança (NOVO!)
- Termo de Confidencialidade obrigatório
- Registro de aceites com IP
- Proteção de dados pessoais
- Bloqueio de contato externo

### 🔒 Sistema de Permissões (RBAC)
- Roles e permissões dinâmicas
- Menus baseados em permissões
- Trait HasRoles para administradores e empresas

## 🛠️ Tecnologias Utilizadas

- **Laravel 11.x** - Framework PHP
- **Laravel Sanctum** - Autenticação por tokens
- **MySQL** - Banco de dados
- **Mailtrap/SendGrid** - Envio de e-mails

## 📦 Instalação

### Pré-requisitos

- PHP 8.2+
- Composer
- MySQL 8.0+
- Node.js (opcional)

### Passos

1. Clone o repositório:
```bash
git clone <seu-repositorio>
cd gopubli-back
```

2. Instale as dependências:
```bash
composer install
```

3. Configure o arquivo `.env`:
```bash
cp .env.example .env
php artisan key:generate
```

4. Configure o banco de dados no `.env`:
```env
DB_DATABASE=gopubli_back
DB_USERNAME=root
DB_PASSWORD=
```

5. Configure o e-mail no `.env` (veja `.env.example.complete` para exemplos)

6. Execute as migrations:
```bash
php artisan migrate
```

7. Crie o link simbólico do storage:
```bash
php artisan storage:link
```

8. **NOVO:** Popular banco com dados de teste (opcional):
```bash
php artisan db:seed --class=GoPubliSeeder
```

9. Inicie o servidor:
```bash
php artisan serve
```

A API estará disponível em `http://localhost:8000`

## 📚 Documentação Completa

- **[PWA_API_DOCUMENTATION.md](PWA_API_DOCUMENTATION.md)** - Documentação completa de todos os endpoints do PWA
- **[API_DOCUMENTATION.md](API_DOCUMENTATION.md)** - Documentação de autenticação e perfis
- **[PERMISSIONS_SYSTEM.md](PERMISSIONS_SYSTEM.md)** - Sistema de permissões (RBAC)
- **[PRACTICAL_EXAMPLES.md](PRACTICAL_EXAMPLES.md)** - Exemplos práticos e casos de uso
- **[INSTALLATION_GUIDE.md](INSTALLATION_GUIDE.md)** - Guia completo de instalação
- **[IMPLEMENTATION_SUMMARY.md](IMPLEMENTATION_SUMMARY.md)** - Resumo de toda implementação
- **[VISUAL_OVERVIEW.md](VISUAL_OVERVIEW.md)** - Visão geral visual do sistema
- **[PRACTICAL_USE_EXAMPLES.md](PRACTICAL_USE_EXAMPLES.md)** - Exemplos práticos de código

## 🧪 Dados de Teste

### Após executar o seeder, você terá:

**Empresas:**
```
Email: contato@techcorp.com.br
Senha: password123

Email: contato@fashionstore.com.br
Senha: password123
```

**Influencers:**
```
Email: maria@influencer.com
Senha: password123

Email: joao@influencer.com
Senha: password123

Email: ana@influencer.com
Senha: password123
```

## 🔗 Endpoints Principais

### Empresas - Campanhas
```
GET    /api/company/campaigns/dashboard
GET    /api/company/campaigns
POST   /api/company/campaigns
POST   /api/company/campaigns/{id}/confirm-payment
GET    /api/company/campaigns/{id}/applications
POST   /api/company/campaigns/{campaignId}/applications/{applicationId}/accept
POST   /api/company/campaigns/{id}/complete
```

### Influencers - Campanhas
```
GET    /api/influencer/campaigns/dashboard
GET    /api/influencer/campaigns/available
POST   /api/influencer/campaigns/{id}/apply
GET    /api/influencer/campaigns/my-applications
GET    /api/influencer/campaigns/my-campaigns
```

### GO Coin (ambos)
```
GET    /api/{type}/gocoin/balance
GET    /api/{type}/gocoin/transactions
POST   /api/{type}/gocoin/redeem
GET    /api/{type}/gocoin/stats
```

### Assinatura (empresas)
```
GET    /api/company/subscription
POST   /api/company/subscription
POST   /api/company/subscription/confirm-payment
GET    /api/company/subscription/status
```

### Autenticação
```
POST /api/{type}/register          # Registrar usuário
POST /api/{type}/login             # Login
POST /api/{type}/logout            # Logout (protegido)
GET  /api/{type}/me                # Dados do usuário (protegido)
```

### Recuperação de Senha
```
POST /api/{type}/forgot-password   # Solicitar reset
POST /api/{type}/reset-password    # Resetar senha
```

### Verificação de E-mail
```
POST /api/{type}/email/send-verification      # Enviar e-mail
GET  /api/email/verify/{type}/{id}/{hash}     # Verificar e-mail
GET  /api/{type}/email/check-verification     # Status (protegido)
```

### Perfil
```
PUT    /api/{type}/profile              # Atualizar perfil (protegido)
POST   /api/{type}/profile/avatar       # Upload avatar (protegido)
POST   /api/company/profile/logo        # Upload logo (protegido)
DELETE /api/{type}/profile/avatar       # Remover imagem (protegido)
```

**Nota:** `{type}` pode ser `admin`, `company` ou `influencer`

## 🧪 Testando a API

### Com cURL

```bash
# Registrar usuário
curl -X POST http://localhost:8000/api/influencer/register \
  -H "Content-Type: application/json" \
  -d '{"name":"João Silva","email":"joao@test.com","password":"senha123","password_confirmation":"senha123"}'

# Login
curl -X POST http://localhost:8000/api/influencer/login \
  -H "Content-Type: application/json" \
  -d '{"email":"joao@test.com","password":"senha123"}'

# Acessar perfil (use o token recebido no login)
curl -X GET http://localhost:8000/api/influencer/me \
  -H "Authorization: Bearer SEU_TOKEN_AQUI"
```

### Com Postman/Insomnia

Importe a coleção ou configure manualmente os endpoints listados acima.

## 📁 Estrutura do Projeto

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AdministratorAuthController.php
│   │   ├── CompanyAuthController.php
│   │   ├── InfluencerAuthController.php
│   │   ├── CompanyCampaignController.php (NOVO)
│   │   ├── InfluencerCampaignController.php (NOVO)
│   │   ├── GoCoinController.php (NOVO)
│   │   ├── SubscriptionController.php (NOVO)
│   │   ├── TermsController.php (NOVO)
│   │   ├── ProfileController.php
│   │   ├── PasswordResetController.php
│   │   └── EmailVerificationController.php
│   └── Middleware/
│       ├── EnsureUserIsAdministrator.php
│       ├── EnsureUserIsCompany.php
│       ├── EnsureUserIsInfluencer.php
│       └── EnsureEmailIsVerified.php
├── Models/
│   ├── Administrator.php
│   ├── Company.php (atualizado)
│   ├── Influencer.php (atualizado)
│   ├── Campaign.php (NOVO)
│   ├── CampaignApplication.php (NOVO)
│   ├── GoCoinWallet.php (NOVO)
│   ├── GoCoinTransaction.php (NOVO)
│   ├── Subscription.php (NOVO)
│   └── TermsAcceptance.php (NOVO) por tipo de usuário
- Validação de dados em todas as requisições
- **Proteção de dados sensíveis até pagamento confirmado**
- **Termo de confidencialidade obrigatório antes de ver campanhas**
- **Bloqueio automático de campanhas por inadimplência**

## 🎯 Status do Projeto

### ✅ Implementado (100% do Escopo PWA)
- [x] Sistema de autenticação completo (Admin, Empresa, Influencer)
- [x] Verificação de e-mail e reset de senha
- [x] Sistema de perfis com upload de imagens
- [x] **Sistema de campanhas com workflow completo**
- [x] **Sistema de candidaturas para influencers**
- [x] **GO Coin - Moeda digital do sistema**
- [x] **Sistema de assinaturas mensais**
- [x] **Termos de confidencialidade**
- [x] **Proteção de dados sensíveis**
- [x] **Distribuição automática de valores (60/20/20)**
- [x] **Seeders com dados de teste**
- [x] **Documentação completa**

## 🚧 Próximas Melhorias Sugeridas

- [ ] **Integração com Asaas** (gateway de pagamentos)
- [ ] Sistema de notificações push
- [ ] Chat interno entre empresa e influencer
- [ ] Dashboard administrativo com gráficos
- [ ] Sistema de avaliações mútuas
- [ ] Relatórios exportáveis (PDF/Excel)
- [ ] Sistema de disputas/reclamações
- [ ] Upload de comprovantes de pagamento
- [ ] Histórico detalhado de ações

## 📱 Consumindo a API (React/React Native)

### Exemplo de Integração

```javascript
import axios from 'axios';

const api = axios.create({
  baseURL: 'http://localhost:8000/api',
});

// Interceptor para adicionar token
api.interceptors.request.use(config => { da GO Publi.

## 👥 Equipe

Desenvolvido para GO Publi - Janeiro 2025

---

## 🎉 Sistema Completo e Pronto para Produção!

✨ **Todas as funcionalidades do escopo PWA Beta foram implementadas com sucesso!**

### O que está pronto:
- ✅ **API REST completa** com 50+ endpoints
- ✅ **Autenticação robusta** com Sanctum
- ✅ **Sistema de campanhas** com workflow completo
- ✅ **GO Coin operacional** com carteiras e transações
- ✅ **Assinaturas** com bloqueio automático
- ✅ **Segurança** com proteção de dados sensíveis
- ✅ **Documentação detalhada** com exemplos práticos
- ✅ **Dados de teste** prontos para desenvolvimento
- ✅ **Regras de negócio** implementadas e validadas

### Próximo passo:
**Desenvolver o frontend PWA (React/React Native)** para consumir esta API! 🚀

**Documentação recomendada para iniciar:**
1. [PWA_API_DOCUMENTATION.md](PWA_API_DOCUMENTATION.md) - Todos os endpoints
2. [PRACTICAL_USE_EXAMPLES.md](PRACTICAL_USE_EXAMPLES.md) - Exemplos de código
3. [VISUAL_OVERVIEW.md](VISUAL_OVERVIEW.md) - Fluxos visuais do sistemaesa
const loginCompany = async (email, password) => {
  const response = await api.post('/company/login', { email, password });
  localStorage.setItem('token', response.data.token);
  return response.data;
};

// Criar campanha
const createCampaign = async (campaignData) => {
  const response = await api.post('/company/campaigns', campaignData);
  return response.data;
};

// Buscar campanhas disponíveis (influencer)
const getAvailableCampaigns = async (filters = {}) => {
  const response = await api.get('/influencer/campaigns/available', { params: filters });
  return response.data;
};

// Candidatar-se a campanha
const applyToCampaign = async (campaignId, data) => {
  const response = await api.post(`/influencer/campaigns/${campaignId}/apply`, data);
  return response.data;
};

// Ver saldo GO Coin
const getGoCoinBalance = async (userType) => {
  const response = await api.get(`/${userType}/gocoin/balance`);
  return response.data;
};
```

### Veja mais exemplos em:
- [PRACTICAL_USE_EXAMPLES.md](PRACTICAL_USE_EXAMPLES.md) - Exemplos completos de integraçãoCandidaturas dos influencers
- `go_coin_wallets` - Carteiras GO Coin (polimórfica)
- `go_coin_transactions` - Histórico de transações
- `terms_acceptances` - Aceites de termos (polimórfica)
- `subscriptions` - Assinaturas mensais das empresas

## 🔐 Regras de Negócio Implementadas

- ✅ Valor mínimo de campanha: **R$ 200,00**
- ✅ Mensalidade mínima: **R$ 200,00**
- ✅ Distribuição automática: **60% influencer / 20% GO Publi / 20% marketing**
- ✅ Bônus de **5%** ao finalizar campanha
- ✅ Dados sensíveis ocultos até pagamento/aceite
- ✅ Termo de confidencialidade obrigatório
- ✅ Bloqueio automático por inadimplência de assinatura
- ✅ Validação de assinatura ativa para criar campanhas

## 🔒 Segurança

- Todas as senhas são criptografadas com bcrypt
- Tokens são gerados com Laravel Sanctum
- Links de verificação possuem assinatura temporária
- Middleware para proteção de rotas
- Validação de dados em todas as requisições

## 🚧 Próximos Passos

- [ ] Sistema de campanhas para empresas
- [ ] Sistema de propostas para influencers
- [ ] Dashboard administrativo
- [ ] Notificações em tempo real
- [ ] Sistema de pagamentos
- [ ] Relatórios e analytics

## 📄 Licença

Este projeto é proprietário e confidencial.

## 👥 Equipe

Desenvolvido para GoPubli

---

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
