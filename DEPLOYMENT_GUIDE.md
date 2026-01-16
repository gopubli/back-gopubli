# Guia de Deploy - GoPubLi API

## 📋 Pré-requisitos

### No cPanel
- Acesso SSH habilitado
- PHP 8.1 ou superior
- Composer instalado
- MySQL/MariaDB
- Acesso ao cPanel
- Domínio configurado (ex: api.seudominio.com)

### No GitHub
- Repositório criado
- Acesso ao GitHub Actions

## 🚀 Passo a Passo

### 1. Configurar o Servidor cPanel

#### 1.1 Criar Banco de Dados
1. Acesse **MySQL Databases** no cPanel
2. Crie um novo banco de dados (ex: `usuario_gopubli`)
3. Crie um usuário MySQL com senha forte
4. Adicione o usuário ao banco com **ALL PRIVILEGES**
5. Anote as credenciais:
   - Nome do banco
   - Usuário
   - Senha
   - Host (geralmente `localhost`)

#### 1.2 Configurar Domínio/Subdomínio
1. Acesse **Domains** ou **Subdomains** no cPanel
2. Crie um novo subdomínio: `api.seudominio.com`
3. Configure o **Document Root** para: `/home/usuario/api.seudominio.com/public`
4. Salve as configurações

#### 1.3 Gerar Chave SSH (se não tiver)
```bash
ssh-keygen -t rsa -b 4096 -C "deploy@gopubli"
# Pressione Enter para todas as perguntas (sem senha)
cat ~/.ssh/id_rsa.pub
# Copie a chave pública
```

Adicione a chave pública em `~/.ssh/authorized_keys` do servidor.

### 2. Configurar GitHub Secrets

Vá em: **Settings** → **Secrets and variables** → **Actions** → **New repository secret**

Adicione os seguintes secrets:

| Nome | Descrição | Exemplo |
|------|-----------|---------|
| `DEPLOY_HOST` | Host do servidor | `seudominio.com` ou IP |
| `DEPLOY_USER` | Usuário SSH | `usuario` |
| `DEPLOY_KEY` | Chave privada SSH | Conteúdo de `~/.ssh/id_rsa` |
| `DEPLOY_PATH` | Caminho no servidor | `/home/usuario/api.seudominio.com` |
| `DB_HOST` | Host do banco | `localhost` |
| `DB_DATABASE` | Nome do banco | `usuario_gopubli` |
| `DB_USERNAME` | Usuário do banco | `usuario_gopubli` |
| `DB_PASSWORD` | Senha do banco | Sua senha MySQL |
| `APP_KEY` | Chave da aplicação | Será gerada no deploy |
| `JWT_SECRET` | Secret do JWT | String aleatória |

### 3. Configurar Estrutura no Servidor

Conecte via SSH e execute:

```bash
# Conectar ao servidor
ssh usuario@seudominio.com

# Criar estrutura de diretórios
cd ~/api.seudominio.com
mkdir -p releases shared/storage/{app,framework,logs}
mkdir -p shared/storage/framework/{cache,sessions,views}
mkdir -p shared/storage/app/public

# Configurar permissões
chmod -R 775 shared/storage
chmod -R 775 bootstrap/cache

# Criar link simbólico para storage
ln -s ~/api.seudominio.com/shared/storage ~/api.seudominio.com/storage

# Criar arquivo .env
nano ~/api.seudominio.com/shared/.env
```

### 4. Configurar .env de Produção

Cole no arquivo `.env` do servidor:

```env
APP_NAME="GoPubLi API"
APP_ENV=production
APP_KEY=base64:SUA_CHAVE_SERA_GERADA
APP_DEBUG=false
APP_URL=https://api.seudominio.com

APP_LOCALE=pt_BR
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=usuario_gopubli
DB_USERNAME=usuario_gopubli
DB_PASSWORD=SUA_SENHA_MYSQL

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=.seudominio.com

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database

MAIL_MAILER=smtp
MAIL_HOST=mail.seudominio.com
MAIL_PORT=587
MAIL_USERNAME=noreply@seudominio.com
MAIL_PASSWORD=sua_senha_email
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@seudominio.com"
MAIL_FROM_NAME="${APP_NAME}"

# Adicione outros secrets necessários
```

### 5. Configurar .htaccess (se necessário)

No diretório `public/`, crie ou edite `.htaccess`:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Redirecionar para HTTPS
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
    
    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
    
    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]
    
    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

### 6. Fazer o Primeiro Deploy Manual

```bash
ssh usuario@seudominio.com

cd ~/api.seudominio.com

# Clone o repositório
git clone https://github.com/seu-usuario/gopubli-back.git current
cd current

# Instalar dependências
composer install --no-dev --optimize-autoloader

# Gerar chave da aplicação
php artisan key:generate

# Criar link do storage
php artisan storage:link

# Executar migrations
php artisan migrate --force

# Executar seeders (se necessário)
php artisan db:seed --force

# Otimizar aplicação
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ajustar permissões
chmod -R 775 storage bootstrap/cache
```

### 7. Configurar SSL/HTTPS

1. No cPanel, acesse **SSL/TLS Status**
2. Ative o AutoSSL para seu domínio (Let's Encrypt gratuito)
3. Ou instale um certificado SSL manualmente

### 8. Testar a API

```bash
curl https://api.seudominio.com/api/health
```

## 🔄 Deploy Automático via GitHub Actions

Após configurar o workflow (arquivo `.github/workflows/deploy.yml`), o deploy será automático:

1. Faça commit das suas alterações
2. Push para a branch `main` (ou `production`)
3. GitHub Actions executará automaticamente
4. Acompanhe o processo em **Actions** no GitHub

## 🐛 Troubleshooting

### Erro 500
```bash
# Ver logs
tail -f storage/logs/laravel.log

# Verificar permissões
chmod -R 775 storage bootstrap/cache
chown -R usuario:usuario storage bootstrap/cache
```

### Composer out of memory
```bash
# Aumentar memória temporariamente
php -d memory_limit=-1 /usr/local/bin/composer install
```

### Migrations não executam
```bash
# Verificar conexão com banco
php artisan tinker
>>> DB::connection()->getPdo();
```

### Permissões negadas
```bash
# Ajustar proprietário (substitua 'usuario' pelo seu usuário cPanel)
chown -R usuario:usuario ~/api.seudominio.com
chmod -R 775 storage bootstrap/cache
```

## 📊 Monitoramento

### Logs
```bash
# Laravel logs
tail -f ~/api.seudominio.com/current/storage/logs/laravel.log

# PHP errors
tail -f ~/public_html/error_log
```

### Performance
- Configure cache (Redis recomendado)
- Use Queue para tarefas pesadas
- Configure CDN para assets estáticos

## 🔐 Segurança

- ✅ `APP_DEBUG=false` em produção
- ✅ Use HTTPS sempre
- ✅ Configure CORS adequadamente
- ✅ Mantenha secrets seguros no GitHub
- ✅ Atualize dependências regularmente
- ✅ Configure rate limiting
- ✅ Use senhas fortes para DB

## 📚 Recursos Adicionais

- [Laravel Deployment Documentation](https://laravel.com/docs/deployment)
- [GitHub Actions Documentation](https://docs.github.com/en/actions)
- [cPanel Documentation](https://docs.cpanel.net/)

## 🆘 Suporte

Se encontrar problemas:
1. Verifique os logs: `storage/logs/laravel.log`
2. Teste a conexão SSH
3. Verifique as permissões dos arquivos
4. Confirme as credenciais do banco de dados
