# 📋 Checklist de Deploy - GoPubLi API

## ✅ Antes de Começar

### No cPanel
- [ ] Acesso SSH habilitado
- [ ] PHP 8.1+ instalado
- [ ] Composer disponível
- [ ] Banco MySQL criado
- [ ] Usuário MySQL criado com permissões
- [ ] Domínio/subdomínio configurado (ex: api.seudominio.com)
- [ ] Document Root apontando para `/public`

### No GitHub
- [ ] Repositório criado
- [ ] Código commitado
- [ ] Acesso ao GitHub Actions habilitado

## 🔐 Configurar GitHub Secrets

Acesse: **Settings** → **Secrets and variables** → **Actions**
⚠️ **ATENÇÃO**: Digite os nomes com **underscore (_)**, NÃO use espaços!

Exemplo: `DEPLOY_HOST` ✅ (correto) | `DEPLOY HOST` ❌ (errado)
- [ ] `DEPLOY_HOST` - Seu domínio ou IP
- [ ] `DEPLOY_USER` - Usuário SSH do cPanel
- [ ] `DEPLOY_KEY` - Chave privada SSH (conteúdo completo)
- [ ] `DEPLOY_PATH` - Caminho completo (ex: `/home/usuario/api.seudominio.com`)
- [ ] `DB_HOST` - `localhost` (geralmente)
- [ ] `DB_DATABASE` - Nome do banco
- [ ] `DB_USERNAME` - Usuário do banco
- [ ] `DB_PASSWORD` - Senha do banco

## 🖥️ Configurar Servidor (SSH)

```bash
# 1. Conectar ao servidor
ssh usuario@seudominio.com

# 2. Criar estrutura
cd ~/api.seudominio.com
mkdir -p releases shared/storage/{app,framework,logs}
mkdir -p shared/storage/framework/{cache,sessions,views}
mkdir -p shared/storage/app/public
mkdir -p shared/bootstrap/cache

# 3. Configurar permissões
chmod -R 775 shared/storage
chmod -R 775 shared/bootstrap/cache

# 4. Criar .env de produção
nano ~/api.seudominio.com/shared/.env
```

**Cole o conteúdo de `.env.production.example` e ajuste os valores!**

## 🚀 Primeiro Deploy

### Opção 1: Deploy Automático (Recomendado)
```bash
# No seu computador local
git add .
git commit -m "Configure deployment"
git push origin main
```

GitHub Actions fará o resto automaticamente! 🎉

### Opção 2: Deploy Manual
```bash
# No servidor
cd ~/api.seudominio.com
git clone https://github.com/seu-usuario/gopubli-back.git current
cd current
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
```

## 🔒 Configurar SSL

- [ ] Ativar AutoSSL no cPanel (Let's Encrypt - gratuito)
- [ ] Ou instalar certificado SSL manualmente
- [ ] Forçar HTTPS no `.htaccess`

## ✅ Verificar Deploy

```bash
# Testar API
curl https://api.seudominio.com/api/health

# Ver logs
tail -f ~/api.seudominio.com/current/storage/logs/laravel.log
```

## 📱 Próximos Deploys

Após configuração inicial, basta:

```bash
git add .
git commit -m "Suas alterações"
git push origin main
```

GitHub Actions fará o deploy automaticamente! 🚀

## 🐛 Solução de Problemas

### Erro 500
```bash
# Ver logs
tail -50 ~/api.seudominio.com/current/storage/logs/laravel.log

# Ajustar permissões
chmod -R 775 ~/api.seudominio.com/shared/storage
```

### Deploy falha no GitHub Actions
1. Verifique se todos os secrets estão configurados
2. Teste conexão SSH manualmente
3. Verifique os logs do GitHub Actions

### Banco de dados não conecta
```bash
# Testar conexão
php artisan tinker
>>> DB::connection()->getPdo();
```

## 📚 Arquivos Criados

1. [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md) - Guia completo detalhado
2. [.github/workflows/deploy.yml](.github/workflows/deploy.yml) - Workflow GitHub Actions
3. [deploy.sh](deploy.sh) - Script de deploy no servidor
4. [.env.production.example](.env.production.example) - Template do .env

## 🎯 Comandos Úteis

```bash
# Ver status da aplicação
php artisan about

# Limpar todos os caches
php artisan cache:clear && php artisan config:clear && php artisan route:clear && php artisan view:clear

# Reotimizar
php artisan config:cache && php artisan route:cache && php artisan view:cache

# Ver logs em tempo real
tail -f storage/logs/laravel.log

# Executar migrations
php artisan migrate --force

# Rollback última migration
php artisan migrate:rollback --step=1 --force
```

## ⚠️ IMPORTANTE

- ✅ NUNCA commite o arquivo `.env` real
- ✅ Mantenha `APP_DEBUG=false` em produção
- ✅ Use HTTPS sempre
- ✅ Faça backup do banco regularmente
- ✅ Monitore os logs
- ✅ Mantenha dependências atualizadas
