# 🧪 Exemplos de Teste - Login API

## 📋 Dados de Teste Disponíveis

Depois de executar o seeder, você tem os seguintes usuários:

### 🏢 Empresas
```
Email: contato@techcorp.com.br
Senha: password123

Email: contato@fashionstore.com.br
Senha: password123
```

### 📱 Influencers
```
Email: maria@influencer.com
Senha: password123

Email: joao@influencer.com
Senha: password123

Email: ana@influencer.com
Senha: password123
```

---

## 🔧 Testando com cURL (Terminal)

### Login de Empresa
```bash
curl -X POST http://localhost:8000/api/company/login \
  -H "Content-Type: application/json" \
  -d "{\"email\":\"contato@techcorp.com.br\",\"password\":\"password123\"}"
```

### Login de Influencer
```bash
curl -X POST http://localhost:8000/api/influencer/login \
  -H "Content-Type: application/json" \
  -d "{\"email\":\"maria@influencer.com\",\"password\":\"password123\"}"
```

### Obter Dados do Usuário Logado
```bash
# Substitua SEU_TOKEN_AQUI pelo token recebido no login
curl -X GET http://localhost:8000/api/company/me \
  -H "Authorization: Bearer SEU_TOKEN_AQUI"
```

---

## 📬 Testando com Postman/Insomnia

### 1. Login de Empresa

**Método:** `POST`  
**URL:** `http://localhost:8000/api/company/login`  
**Headers:**
```
Content-Type: application/json
```
**Body (JSON):**
```json
{
  "email": "contato@techcorp.com.br",
  "password": "password123"
}
```

**Resposta Esperada:**
```json
{
  "message": "Login realizado com sucesso",
  "token": "1|abc123def456...",
  "user": {
    "id": 1,
    "name": "Tech Corp",
    "email": "contato@techcorp.com.br",
    "type": "company",
    "email_verified_at": "2025-01-13T10:00:00.000000Z",
    "created_at": "2025-01-13T10:00:00.000000Z"
  }
}
```

### 2. Login de Influencer

**Método:** `POST`  
**URL:** `http://localhost:8000/api/influencer/login`  
**Headers:**
```
Content-Type: application/json
```
**Body (JSON):**
```json
{
  "email": "maria@influencer.com",
  "password": "password123"
}
```

**Resposta Esperada:**
```json
{
  "message": "Login realizado com sucesso",
  "token": "2|xyz789ghi012...",
  "user": {
    "id": 1,
    "name": "Maria Silva",
    "email": "maria@influencer.com",
    "type": "influencer",
    "email_verified_at": "2025-01-13T10:00:00.000000Z",
    "created_at": "2025-01-13T10:00:00.000000Z"
  }
}
```

### 3. Obter Perfil do Usuário (Autenticado)

**Método:** `GET`  
**URL:** `http://localhost:8000/api/company/me`  
**Headers:**
```
Authorization: Bearer {seu_token_aqui}
Content-Type: application/json
```

**Resposta Esperada:**
```json
{
  "id": 1,
  "name": "Tech Corp",
  "email": "contato@techcorp.com.br",
  "phone": "(11) 98765-4321",
  "cnpj": "12345678000190",
  "logo": null,
  "email_verified_at": "2025-01-13T10:00:00.000000Z",
  "created_at": "2025-01-13T10:00:00.000000Z",
  "subscription": {
    "id": 1,
    "status": "active",
    "monthly_value": "200.00",
    "billing_day": 15,
    "next_billing_date": "2025-02-15"
  },
  "gocoin_wallet": {
    "id": 1,
    "balance": "100.00"
  }
}
```

---

## 🌐 Testando com JavaScript (Fetch API)

### Login de Empresa
```javascript
fetch('http://localhost:8000/api/company/login', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    email: 'contato@techcorp.com.br',
    password: 'password123'
  })
})
  .then(response => response.json())
  .then(data => {
    console.log('Login sucesso:', data);
    localStorage.setItem('token', data.token);
    localStorage.setItem('user', JSON.stringify(data.user));
  })
  .catch(error => console.error('Erro:', error));
```

### Login de Influencer
```javascript
fetch('http://localhost:8000/api/influencer/login', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    email: 'maria@influencer.com',
    password: 'password123'
  })
})
  .then(response => response.json())
  .then(data => {
    console.log('Login sucesso:', data);
    localStorage.setItem('token', data.token);
    localStorage.setItem('user', JSON.stringify(data.user));
  })
  .catch(error => console.error('Erro:', error));
```

### Obter Perfil (com token)
```javascript
const token = localStorage.getItem('token');

fetch('http://localhost:8000/api/company/me', {
  method: 'GET',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json',
  }
})
  .then(response => response.json())
  .then(data => {
    console.log('Perfil:', data);
  })
  .catch(error => console.error('Erro:', error));
```

---

## ⚛️ Testando com Axios (React/React Native)

### Configuração Inicial
```javascript
import axios from 'axios';

const api = axios.create({
  baseURL: 'http://localhost:8000/api',
});

// Interceptor para adicionar token automaticamente
api.interceptors.request.use(config => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

export default api;
```

### Login de Empresa
```javascript
import api from './api';

const loginCompany = async () => {
  try {
    const response = await api.post('/company/login', {
      email: 'contato@techcorp.com.br',
      password: 'password123'
    });
    
    console.log('Login sucesso:', response.data);
    localStorage.setItem('token', response.data.token);
    localStorage.setItem('user', JSON.stringify(response.data.user));
    
    return response.data;
  } catch (error) {
    console.error('Erro no login:', error.response?.data);
    throw error;
  }
};

loginCompany();
```

### Login de Influencer
```javascript
import api from './api';

const loginInfluencer = async () => {
  try {
    const response = await api.post('/influencer/login', {
      email: 'maria@influencer.com',
      password: 'password123'
    });
    
    console.log('Login sucesso:', response.data);
    localStorage.setItem('token', response.data.token);
    localStorage.setItem('user', JSON.stringify(response.data.user));
    
    return response.data;
  } catch (error) {
    console.error('Erro no login:', error.response?.data);
    throw error;
  }
};

loginInfluencer();
```

### Obter Perfil (já com token configurado)
```javascript
import api from './api';

const getProfile = async (userType) => {
  try {
    const response = await api.get(`/${userType}/me`);
    console.log('Perfil:', response.data);
    return response.data;
  } catch (error) {
    console.error('Erro ao obter perfil:', error.response?.data);
    throw error;
  }
};

// Usar
getProfile('company'); // ou 'influencer'
```

---

## ❌ Erros Comuns e Respostas

### Login com Credenciais Inválidas
**Request:**
```json
{
  "email": "contato@techcorp.com.br",
  "password": "senha_errada"
}
```

**Resposta (401):**
```json
{
  "message": "Credenciais inválidas"
}
```

### Token Inválido ou Expirado
**Resposta (401):**
```json
{
  "message": "Unauthenticated."
}
```

### Tentar acessar endpoint errado
**Request:** Influencer tentando acessar rota de empresa
```
GET /api/company/campaigns
Authorization: Bearer {token_de_influencer}
```

**Resposta (403):**
```json
{
  "message": "Você não tem permissão para acessar este recurso"
}
```

---

## 🔄 Fluxo Completo de Autenticação

### 1. Login
```javascript
const login = async (userType, email, password) => {
  const response = await fetch(`http://localhost:8000/api/${userType}/login`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ email, password })
  });
  
  const data = await response.json();
  
  if (response.ok) {
    localStorage.setItem('token', data.token);
    localStorage.setItem('userType', userType);
    return data;
  }
  
  throw new Error(data.message);
};
```

### 2. Fazer Requisições Autenticadas
```javascript
const makeAuthRequest = async (endpoint) => {
  const token = localStorage.getItem('token');
  const userType = localStorage.getItem('userType');
  
  const response = await fetch(`http://localhost:8000/api/${userType}${endpoint}`, {
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    }
  });
  
  return response.json();
};

// Usar
const profile = await makeAuthRequest('/me');
const campaigns = await makeAuthRequest('/campaigns');
```

### 3. Logout
```javascript
const logout = async () => {
  const token = localStorage.getItem('token');
  const userType = localStorage.getItem('userType');
  
  await fetch(`http://localhost:8000/api/${userType}/logout`, {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`
    }
  });
  
  localStorage.removeItem('token');
  localStorage.removeItem('userType');
  localStorage.removeItem('user');
};
```

---

## 🎯 Testando os 3 Tipos de Usuário

### Todos os Endpoints de Login
```bash
# Administrador
curl -X POST http://localhost:8000/api/admin/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@gopubli.com","password":"admin123"}'

# Empresa
curl -X POST http://localhost:8000/api/company/login \
  -H "Content-Type: application/json" \
  -d '{"email":"contato@techcorp.com.br","password":"password123"}'

# Influencer
curl -X POST http://localhost:8000/api/influencer/login \
  -H "Content-Type: application/json" \
  -d '{"email":"maria@influencer.com","password":"password123"}'
```

---

## 📊 Testando Endpoints Protegidos

### Dashboard de Empresa
```bash
curl -X GET http://localhost:8000/api/company/campaigns/dashboard \
  -H "Authorization: Bearer SEU_TOKEN"
```

### Dashboard de Influencer
```bash
curl -X GET http://localhost:8000/api/influencer/campaigns/dashboard \
  -H "Authorization: Bearer SEU_TOKEN"
```

### Saldo GO Coin
```bash
curl -X GET http://localhost:8000/api/company/gocoin/balance \
  -H "Authorization: Bearer SEU_TOKEN"
```

---

## 🔗 Próximos Passos

Após testar o login com sucesso:

1. **Teste as Campanhas** - [PWA_API_DOCUMENTATION.md](PWA_API_DOCUMENTATION.md#campanhas)
2. **Teste GO Coin** - [PWA_API_DOCUMENTATION.md](PWA_API_DOCUMENTATION.md#go-coin)
3. **Veja Exemplos Completos** - [PRACTICAL_USE_EXAMPLES.md](PRACTICAL_USE_EXAMPLES.md)

---

## 💡 Dicas

- ✅ Sempre salve o token retornado no login
- ✅ Inclua o token em todas as requisições protegidas
- ✅ Use o tipo correto de usuário nas rotas (`admin`, `company`, `influencer`)
- ✅ Verifique se o servidor Laravel está rodando (`php artisan serve`)
- ✅ Certifique-se de que os dados de teste foram populados (`php artisan db:seed`)
