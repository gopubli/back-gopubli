# Laravel Forge Configuration

## Server Configuration
- PHP Version: 8.3
- Database: MySQL 8.0
- Node Version: 20.x

<!-- ## Site Configuration
- Root Domain: api.gopubli.com.br
- Web Directory: /public
- Project Type: Laravel -->

## Environment Variables
Configure no Forge em: Site → Environment

```env
APP_NAME="GoPubLi API"
APP_ENV=production
APP_KEY=base64:... (gerado automaticamente)
APP_DEBUG=false
APP_URL=https://api.gopubli.com.br

APP_LOCALE=pt_BR
APP_FALLBACK_LOCALE=en

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=gopublicom_gopubli
DB_USERNAME=gopublicom_gopubli
DB_PASSWORD=SUA_SENHA

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

SANCTUM_STATEFUL_DOMAINS=gopubli.com.br,www.gopubli.com.br
```

## Deploy Script
Use o script em: forge-deploy.sh

## SSL Certificate
Forge pode gerar certificado Let's Encrypt automaticamente:
1. Vá em Site → SSL
2. Clique em "Obtain Certificate"
3. Selecione Let's Encrypt
4. Forge instalará o certificado HTTPS

## Scheduler (se necessário)
O Forge configura automaticamente o cron:
```
* * * * * php /home/gopublicom/api.gopubli.com.br/artisan schedule:run >> /dev/null 2>&1
```

## Queue Workers (se necessário)
Configure em: Site → Queue
- Connection: database
- Queue: default
- Timeout: 60
- Sleep: 3
- Tries: 3
