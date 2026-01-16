# 🚀 Deploy GoPubLi API - Configuração Específica

## Informações do Seu Servidor

- **Domínio**: `api.gopubli.com.br`
- **Caminho Base**: `/home/gopublicom`
- **Caminho da API**: `/home/gopublicom/api.gopubli.com.br`
- **Document Root**: `/home/gopublicom/api.gopubli.com.br/current/public`

## 🔐 Secrets do GitHub (Configurar)

Vá em: **Repositório → Settings → Secrets and variables → Actions → New repository secret**

⚠️ **IMPORTANTE**: Use **underscores (_)** nos nomes, NÃO use espaços!

Crie cada secret individualmente com estes nomes EXATOS:

| Nome do Secret | Valor |
|----------------|-------|
| `DEPLOY_HOST` | `gopubli.com.br` (ou o IP do servidor) |
| `DEPLOY_USER` | `gopublicom` |
| `DEPLOY_KEY` | [Sua chave privada SSH completa - todo o conteúdo de id_rsa] |
| `DEPLOY_PATH` | `/home/gopublicom/api.gopubli.com.br` |
| `DB_HOST` | `localhost` |
| `DB_DATABASE` | `gopublicom_gopubli` |
| `DB_USERNAME` | `gopublicom_gopubli` |
| `DB_PASSWORD` | [Senha do seu banco MySQL] |

## 📋 Passo a Passo Rápido

### 1. Conectar ao Servidor via SSH

```bash
ssh gopublicom@gopubli.com.br
# ou
ssh gopublicom@[IP_DO_SERVIDOR]
```

### 2. Criar Estrutura de Diretórios

```bash
cd ~/api.gopubli.com.br

# Criar diretórios
mkdir -p releases
mkdir -p shared/storage/{app,framework,logs}
mkdir -p shared/storage/framework/{cache,sessions,views}
mkdir -p shared/storage/app/public
mkdir -p shared/bootstrap/cache

# Configurar permissões
chmod -R 775 shared/storage
chmod -R 775 shared/bootstrap/cache
```

### 3. Criar Arquivo .env de Produção

```bash
nano ~/api.gopubli.com.br/shared/.env
```

**Cole este conteúdo e ajuste os valores:**

```env
APP_NAME="GoPubLi API"
APP_ENV=production
APP_KEY=base64:SERA_GERADO_AUTOMATICAMENTE
APP_DEBUG=false
APP_URL=https://api.gopubli.com.br

APP_LOCALE=pt_BR
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

# Configurações do Banco (ajuste com suas credenciais)
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=gopublicom_gopubli
DB_USERNAME=gopublicom_gopubli
DB_PASSWORD=SUA_SENHA_MYSQL_AQUI

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=.gopubli.com.br

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database

# Email (configure com suas credenciais)
MAIL_MAILER=smtp
MAIL_HOST=mail.gopubli.com.br
MAIL_PORT=587
MAIL_USERNAME=noreply@gopubli.com.br
MAIL_PASSWORD=SUA_SENHA_EMAIL
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@gopubli.com.br"
MAIL_FROM_NAME="${APP_NAME}"
```

Salve com: `Ctrl+O`, Enter, `Ctrl+X`

### 4. Configurar Document Root no cPanel

1. Acesse **Domínios** no cPanel
2. Encontre `api.gopubli.com.br`
3. Clique em **Gerenciar**
4. Altere **Document Root** para: `/home/gopublicom/api.gopubli.com.br/current/public`
5. Salve

### 5. Verificar/Criar Chave SSH

```bash
# No servidor
cd ~/.ssh

# Se não existir, criar
ssh-keygen -t rsa -b 4096 -C "deploy-gopubli"
# (Pressione Enter para todas as perguntas)

# Ver chave pública
cat ~/.ssh/id_rsa.pub

# Ver chave privada (copie TODA para o GitHub Secret DEPLOY_KEY)
cat ~/.ssh/id_rsa

# Adicionar à authorized_keys
cat ~/.ssh/id_rsa.pub >> ~/.ssh/authorized_keys
chmod 600 ~/.ssh/authorized_keys
```

### 6. Criar Banco de Dados (se ainda não criou)

No cPanel:
1. **MySQL Databases**
2. Criar banco: `gopublicom_gopubli`
3. Criar usuário: `gopublicom_gopubli`
4. Adicionar usuário ao banco com **ALL PRIVILEGES**

### 7. Configurar GitHub

#### 7.1 Criar Repositório
```bash
# No seu computador local (laragon)
cd c:\laragon\www\gopubli-back

# Inicializar git (se ainda não tiver)
git init
git add .
git commit -m "Initial commit - GoPubLi API"

# Adicionar remote (substitua com seu repositório)
git remote add origin https://github.com/seu-usuario/gopubli-back.git
git branch -M main
git push -u origin main
```

#### 7.2 Configurar Secrets (ver lista acima)

### 8. Fazer Deploy

```bash
# No seu computador
git add .
git commit -m "Configure deployment for api.gopubli.com.br"
git push origin main
```

GitHub Actions fará o deploy automaticamente! 🎉

### 9. Ativar SSL

1. No cPanel → **SSL/TLS Status**
2. Ativar **AutoSSL** para `api.gopubli.com.br`
3. Aguardar alguns minutos para gerar

### 10. Testar

```bash
# Via navegador ou curl
curl https://api.gopubli.com.br/api/health

# Ver logs
ssh gopublicom@gopubli.com.br
tail -f ~/api.gopubli.com.br/current/storage/logs/laravel.log
```

## 🔧 Comandos Úteis

```bash
# Conectar ao servidor
ssh gopublicom@gopubli.com.br

# Ver logs
tail -f ~/api.gopubli.com.br/current/storage/logs/laravel.log

# Limpar cache
cd ~/api.gopubli.com.br/current
php artisan cache:clear
php artisan config:clear

# Reotimizar
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ver status
php artisan about

# Rodar migrations manualmente
php artisan migrate --force

# Ver releases
ls -la ~/api.gopubli.com.br/releases/
```

## 🐛 Troubleshooting

### Erro de permissão
```bash
cd ~/api.gopubli.com.br
chmod -R 775 shared/storage
chmod -R 775 shared/bootstrap/cache
chown -R gopublicom:gopublicom shared/
```

### Deploy falha
1. Verifique os logs no GitHub Actions
2. Teste conexão SSH manualmente
3. Verifique se todos os secrets estão corretos

### Erro 500
```bash
# Ver erro específico
tail -50 ~/api.gopubli.com.br/current/storage/logs/laravel.log

# Verificar .env
cat ~/api.gopubli.com.br/shared/.env
```

### Banco não conecta
```bash
# Testar conexão
cd ~/api.gopubli.com.br/current
php artisan tinker
>>> DB::connection()->getPdo();
```

## ✅ Checklist Final

- [ ] Estrutura de diretórios criada
- [ ] Arquivo .env configurado no servidor
- [ ] Banco de dados MySQL criado
- [ ] Document Root configurado
- [ ] Secrets do GitHub configurados
- [ ] Chave SSH configurada
- [ ] Código no GitHub
- [ ] Deploy executado
- [ ] SSL ativado
- [ ] API testada e funcionando

## 🎯 Próximos Passos

Após configuração inicial, para fazer novos deploys:

```bash
git add .
git commit -m "Suas alterações"
git push origin main
```

Pronto! ✨ Deploy automático!
