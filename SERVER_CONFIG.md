# Configuração do Servidor - GoPubLi API

## Problema Atual
O servidor está mostrando a tela padrão do Laravel porque o DocumentRoot não está apontando para o diretório `/public`.

## Solução

### Opção 1: Configurar DocumentRoot (Recomendado)

O DocumentRoot do servidor DEVE apontar para `/home/gopublicom/api.gopubli.com.br/public`

#### Apache VirtualHost
```apache
<VirtualHost *:80>
    ServerName api.gopubli.com.br
    DocumentRoot /home/gopublicom/api.gopubli.com.br/public

    <Directory /home/gopublicom/api.gopubli.com.br/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/gopubli-error.log
    CustomLog ${APACHE_LOG_DIR}/gopubli-access.log combined
</VirtualHost>
```

#### Nginx
```nginx
server {
    listen 80;
    server_name api.gopubli.com.br;
    root /home/gopublicom/api.gopubli.com.br/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Opção 2: Usar .htaccess na raiz (Temporário)

Já foram criados arquivos `.htaccess` e `index.php` na raiz para redirecionar para `/public`.
Esta é uma solução temporária e não ideal para produção.

## Verificação

Após configurar, teste:
```bash
curl -I https://api.gopubli.com.br/api/v1/health
```

Deve retornar `200 OK` ao invés da página do Laravel.

## Permissões Necessárias

```bash
cd /home/gopublicom/api.gopubli.com.br
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## Cache de Configuração

Após qualquer mudança de configuração:
```bash
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan config:cache
php artisan route:cache
```
