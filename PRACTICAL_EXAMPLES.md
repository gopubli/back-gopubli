# Guia de Exemplos Práticos - API GoPubli

## 📸 Upload de Avatar/Logo

### Exemplo com cURL - Upload de Avatar
```bash
curl -X POST http://localhost:8000/api/influencer/profile/avatar \
  -H "Authorization: Bearer SEU_TOKEN_AQUI" \
  -F "avatar=@/caminho/para/sua/imagem.jpg"
```

### Exemplo com cURL - Upload de Logo (Empresas)
```bash
curl -X POST http://localhost:8000/api/company/profile/logo \
  -H "Authorization: Bearer SEU_TOKEN_AQUI" \
  -F "logo=@/caminho/para/logo.png"
```

### Exemplo com JavaScript (Fetch API)
```javascript
const formData = new FormData();
formData.append('avatar', fileInput.files[0]);

fetch('http://localhost:8000/api/influencer/profile/avatar', {
  method: 'POST',
  headers: {
    'Authorization': 'Bearer ' + token,
  },
  body: formData
})
.then(response => response.json())
.then(data => {
  console.log('Avatar atualizado:', data.avatar_url);
});
```

### Response de Sucesso
```json
{
  "message": "Avatar atualizado com sucesso",
  "avatar_url": "http://localhost:8000/storage/avatars/abc123.jpg",
  "user": {
    "id": 1,
    "name": "João Silva",
    "avatar": "avatars/abc123.jpg",
    ...
  }
}
```

---

## 🔑 Recuperação de Senha - Fluxo Completo

### Passo 1: Usuário Esqueceu a Senha
```bash
curl -X POST http://localhost:8000/api/influencer/forgot-password \
  -H "Content-Type: application/json" \
  -d '{
    "email": "joao@gopubli.com"
  }'
```

**Response:**
```json
{
  "message": "Se o e-mail existir em nossa base, você receberá as instruções para redefinir sua senha.",
  "token": "abc123xyz..." // apenas em modo debug
}
```

### Passo 2: Usuário Recebe E-mail com Token

O usuário recebe um e-mail com um link ou token para resetar a senha.

### Passo 3: Resetar a Senha
```bash
curl -X POST http://localhost:8000/api/influencer/reset-password \
  -H "Content-Type: application/json" \
  -d '{
    "email": "joao@gopubli.com",
    "token": "abc123xyz...",
    "password": "novasenha123",
    "password_confirmation": "novasenha123"
  }'
```

**Response:**
```json
{
  "message": "Senha redefinida com sucesso! Por favor, faça login com sua nova senha."
}
```

### Passo 4: Fazer Login com Nova Senha
```bash
curl -X POST http://localhost:8000/api/influencer/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "joao@gopubli.com",
    "password": "novasenha123"
  }'
```

---

## ✉️ Verificação de E-mail - Fluxo Completo

### Fluxo Automático no Registro

Quando um usuário se registra, um e-mail de verificação é enviado automaticamente:

```bash
curl -X POST http://localhost:8000/api/company/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Empresa Teste",
    "email": "empresa@gopubli.com",
    "password": "senha12345",
    "password_confirmation": "senha12345"
  }'
```

**Response:**
```json
{
  "message": "Empresa registrada com sucesso. Verifique seu e-mail.",
  "user": {...},
  "token": "...",
  "type": "company"
}
```

### Reenviar E-mail de Verificação

Se o usuário não recebeu o e-mail ou o link expirou:

```bash
curl -X POST http://localhost:8000/api/company/email/send-verification \
  -H "Authorization: Bearer SEU_TOKEN_AQUI" \
  -H "Accept: application/json"
```

**Response:**
```json
{
  "message": "E-mail de verificação enviado com sucesso"
}
```

### Verificar Status de Verificação

```bash
curl -X GET http://localhost:8000/api/company/email/check-verification \
  -H "Authorization: Bearer SEU_TOKEN_AQUI" \
  -H "Accept: application/json"
```

**Response:**
```json
{
  "verified": false,
  "email": "empresa@gopubli.com"
}
```

### Verificar E-mail (Usuário Clica no Link do E-mail)

O usuário clica no link recebido por e-mail:
```
GET http://localhost:8000/api/email/verify/company/1/a94a8fe5ccb19ba61c4c0873d391e987982fbbd3?expires=1234567890&signature=abc123...
```

**Response:**
```json
{
  "message": "E-mail verificado com sucesso!",
  "user": {
    "id": 1,
    "name": "Empresa Teste",
    "email": "empresa@gopubli.com",
    "email_verified_at": "2024-12-16T10:30:00.000000Z",
    ...
  }
}
```

---

## 📝 Atualizar Perfil

### Atualizar Perfil de Influencer
```bash
curl -X PUT http://localhost:8000/api/influencer/profile \
  -H "Authorization: Bearer SEU_TOKEN_AQUI" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "João Silva Atualizado",
    "phone": "11999887766",
    "instagram": "@joaosilva_oficial",
    "bio": "Criador de conteúdo sobre tecnologia e inovação"
  }'
```

### Atualizar Perfil de Empresa
```bash
curl -X PUT http://localhost:8000/api/company/profile \
  -H "Authorization: Bearer SEU_TOKEN_AQUI" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Nova Empresa LTDA",
    "cnpj": "12345678000190",
    "phone": "1133334444",
    "address": "Av. Paulista, 1000 - São Paulo, SP"
  }'
```

**Response:**
```json
{
  "message": "Perfil atualizado com sucesso",
  "user": {
    "id": 1,
    "name": "Nova Empresa LTDA",
    ...
  }
}
```

---

## 🗑️ Deletar Avatar/Logo

```bash
curl -X DELETE http://localhost:8000/api/influencer/profile/avatar \
  -H "Authorization: Bearer SEU_TOKEN_AQUI" \
  -H "Accept: application/json"
```

**Response:**
```json
{
  "message": "Imagem removida com sucesso",
  "user": {
    "id": 1,
    "name": "João Silva",
    "avatar": null,
    ...
  }
}
```

---

## 🧪 Testando Fluxo Completo

### 1. Registrar Usuário
```bash
curl -X POST http://localhost:8000/api/influencer/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Teste User",
    "email": "teste@gopubli.com",
    "password": "senha12345",
    "password_confirmation": "senha12345"
  }'
```

Salve o `token` retornado.

### 2. Verificar Status do E-mail
```bash
curl -X GET http://localhost:8000/api/influencer/email/check-verification \
  -H "Authorization: Bearer SEU_TOKEN"
```

### 3. Upload de Avatar
```bash
curl -X POST http://localhost:8000/api/influencer/profile/avatar \
  -H "Authorization: Bearer SEU_TOKEN" \
  -F "avatar=@/caminho/para/imagem.jpg"
```

### 4. Atualizar Perfil
```bash
curl -X PUT http://localhost:8000/api/influencer/profile \
  -H "Authorization: Bearer SEU_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "bio": "Minha nova bio",
    "instagram": "@meuinsta"
  }'
```

### 5. Ver Perfil Atualizado
```bash
curl -X GET http://localhost:8000/api/influencer/me \
  -H "Authorization: Bearer SEU_TOKEN"
```

---

## 🔒 Configurando Rotas que Exigem E-mail Verificado

No arquivo `routes/api.php`, adicione o middleware `verified`:

```php
// Exemplo: Rota que exige e-mail verificado
Route::middleware(['auth:sanctum', 'type.company', 'verified'])
    ->post('/company/campaigns/create', [CampaignController::class, 'create']);
```

Se o e-mail não estiver verificado, retorna:
```json
{
  "message": "Seu endereço de e-mail não foi verificado. Por favor, verifique seu e-mail."
}
```

---

## 📧 Configuração de E-mail

Para testar o envio de e-mails em desenvolvimento, configure no arquivo `.env`:

### Usando Mailtrap (Recomendado para Dev)
```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=seu_username
MAIL_PASSWORD=sua_senha
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@gopubli.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### Usando Gmail
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=seu_email@gmail.com
MAIL_PASSWORD=sua_senha_app
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@gopubli.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### Para Produção
```env
# Configure com seu provedor de e-mail
# Ex: SendGrid, AWS SES, Mailgun, etc.
```

---

## ⚠️ Tratamento de Erros

### Erro: Token Expirado (Reset de Senha)
```json
{
  "message": "Token expirado"
}
```
**Solução:** Solicitar novo reset de senha.

### Erro: Link de Verificação Inválido
```json
{
  "message": "Link de verificação inválido ou expirado"
}
```
**Solução:** Solicitar novo e-mail de verificação.

### Erro: Arquivo Muito Grande
```json
{
  "message": "The avatar must not be greater than 2048 kilobytes."
}
```
**Solução:** Reduzir tamanho do arquivo para no máximo 2MB.

### Erro: Formato Inválido
```json
{
  "message": "The avatar must be a file of type: jpeg, png, jpg, gif."
}
```
**Solução:** Usar apenas formatos suportados.

---

## 🎯 Boas Práticas

1. **Sempre use HTTPS em produção** para proteger tokens e dados sensíveis
2. **Armazene tokens de forma segura** no cliente (não em localStorage se possível)
3. **Implemente rate limiting** para endpoints de recuperação de senha
4. **Configure corretamente o e-mail** antes de ir para produção
5. **Use o middleware `verified`** apenas em rotas críticas
6. **Faça backup das imagens** armazenadas no storage
7. **Configure CORS** corretamente para sua aplicação frontend

---

## 🔗 URLs Importantes

- Base URL API: `http://localhost:8000/api`
- Storage Público: `http://localhost:8000/storage`
- Health Check: `http://localhost:8000/up`

---

## 📞 Suporte

Para dúvidas ou problemas, consulte a documentação completa em `API_DOCUMENTATION.md`.
