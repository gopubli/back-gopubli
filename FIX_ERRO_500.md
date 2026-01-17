# 🚀 Guia Rápido - Corrigindo Erro 500

## ✅ Correções Aplicadas

Foram identificados e corrigidos os seguintes problemas que causavam o erro 500:

1. **Estrutura de diretórios incompleta** - O deploy não estava criando os diretórios necessários do storage/
2. **Falta do arquivo .htaccess** - Servidor Apache precisa deste arquivo
3. **Configurações PHP inadequadas** - Criado .user.ini para hosting compartilhado
4. **Setup sem verificações** - Melhorado para detectar problemas

## 📋 Próximos Passos

### 1. Commit e Push das Correções

```bash
git add .
git commit -m "fix: corrige erro 500 no deploy - adiciona estrutura completa"
git push origin main
```

O GitHub Actions vai iniciar o deploy automaticamente.

### 2. Após o Deploy, Criar o Arquivo .env no Servidor

Conecte via FTP ou SSH e crie o arquivo `.env` em:
```
/home/gopublicom/api.gopubli.com.br/.env
```

**Conteúdo do .env:**
```env
APP_NAME="GoPubLi API"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://api.gopubli.com.br

APP_LOCALE=pt_BR
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=pt_BR

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

# Ajuste com suas credenciais do banco
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

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@gopubli.com.br"
MAIL_FROM_NAME="${APP_NAME}"

# Sanctum
SANCTUM_STATEFUL_DOMAINS=gopubli.com.br,www.gopubli.com.br
```

### 3. Verificar o Ambiente

Acesse: **https://api.gopubli.com.br/check.php**

Este arquivo vai verificar:
- ✅ Versão do PHP e extensões
- ✅ Permissões dos diretórios
- ✅ Arquivos de configuração
- ✅ Se o Laravel carrega corretamente

### 4. Executar a Configuração Inicial

Se o check.php mostrar que tudo está OK, acesse:

**https://api.gopubli.com.br/setup-server.php?secret=gopubli2026**

Este script vai:
- ✅ Gerar a APP_KEY
- ✅ Executar migrations
- ✅ Executar seeders
- ✅ Criar storage link
- ✅ Otimizar cache

### 5. Deletar Arquivos de Setup (IMPORTANTE!)

Após configurar com sucesso, DELETE estes arquivos do servidor por segurança:
- `/public/check.php`
- `/public/setup-server.php`

### 6. Testar a API

Acesse: **https://api.gopubli.com.br**

Você deve ver a resposta da API (não mais erro 500!).

## 🔍 Diagnóstico de Problemas

### Ainda está dando erro 500?

1. **Verifique os logs do PHP**:
   - Acesse via FTP: `/storage/logs/laravel.log`
   - Ou logs do servidor se disponíveis

2. **Verifique permissões**:
   ```bash
   chmod -R 775 storage
   chmod -R 775 bootstrap/cache
   ```

3. **Verifique se o .env existe**:
   - Deve estar na raiz: `/home/gopublicom/api.gopubli.com.br/.env`

4. **Limpe o cache manualmente via SSH** (se tiver acesso):
   ```bash
   cd /home/gopublicom/api.gopubli.com.br
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```

## 📝 Arquivos Criados/Modificados

1. **[.github/workflows/deploy.yml](.github/workflows/deploy.yml)** - Deploy corrigido
2. **[public/.htaccess](public/.htaccess)** - Configuração Apache
3. **[.user.ini](.user.ini)** - Configurações PHP para hosting
4. **[public/check.php](public/check.php)** - Verificador de ambiente
5. **setup-server.php** - Melhorado com verificações

## 🆘 Precisa de Ajuda?

Se ainda tiver problemas:
1. Execute o check.php e me mostre o resultado
2. Envie o conteúdo de `/storage/logs/laravel.log`
3. Informe qual mensagem de erro aparece

## ✨ O que foi corrigido tecnicamente:

- ✅ Deploy agora cria toda estrutura de `storage/` e `bootstrap/cache/`
- ✅ Adicionado `.htaccess` com rewrite rules para Apache
- ✅ Adicionado `.user.ini` com configurações PHP seguras
- ✅ Setup agora verifica ambiente antes de executar comandos
- ✅ Criado `check.php` para diagnosticar problemas rapidamente
- ✅ Garantido que diretórios necessários são criados com permissões corretas
