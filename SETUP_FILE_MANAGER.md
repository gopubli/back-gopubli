# 📁 Configurar Servidor via File Manager (Gerenciador de Arquivos)

## Passo 1: Abrir File Manager

1. No cPanel, procure **"Gerenciador de Arquivos"** ou **"File Manager"**
2. Clique para abrir

## Passo 2: Ir para o Diretório Correto

1. No lado esquerdo, navegue até: `/home/gopublicom/`
2. Encontre a pasta `api.gopubli.com.br`
3. Entre nela (clique duplo)

## Passo 3: Criar Estrutura de Pastas

Dentro de `/home/gopublicom/api.gopubli.com.br`, crie estas pastas:

### 3.1 Criar pasta `releases`
1. Clique em **"+ Pasta"** ou **"New Folder"**
2. Nome: `releases`
3. Confirme

### 3.2 Criar pasta `shared`
1. Clique em **"+ Pasta"**
2. Nome: `shared`
3. Confirme

### 3.3 Criar subpastas dentro de `shared`
1. Entre na pasta `shared` (clique duplo)
2. Crie pasta: `storage`
3. Crie pasta: `bootstrap`

### 3.4 Criar subpastas dentro de `shared/storage`
1. Entre na pasta `storage`
2. Crie pasta: `app`
3. Crie pasta: `framework`
4. Crie pasta: `logs`

### 3.5 Criar subpastas dentro de `shared/storage/framework`
1. Entre na pasta `framework`
2. Crie pasta: `cache`
3. Crie pasta: `sessions`
4. Crie pasta: `views`

### 3.6 Voltar e criar `shared/storage/app/public`
1. Volte para `shared/storage/app`
2. Crie pasta: `public`

### 3.7 Criar `shared/bootstrap/cache`
1. Volte para `shared/bootstrap`
2. Crie pasta: `cache`

## ✅ Estrutura Final

Você deve ter criado esta estrutura:

```
/home/gopublicom/api.gopubli.com.br/
├── releases/
└── shared/
    ├── storage/
    │   ├── app/
    │   │   └── public/
    │   ├── framework/
    │   │   ├── cache/
    │   │   ├── sessions/
    │   │   └── views/
    │   └── logs/
    └── bootstrap/
        └── cache/
```

## Passo 4: Criar Arquivo .env

1. Navegue até `/home/gopublicom/api.gopubli.com.br/shared/`
2. Clique em **"+ Arquivo"** ou **"New File"**
3. Nome do arquivo: `.env` (com ponto no início!)
4. Confirme
5. Clique com botão direito no arquivo `.env` → **"Edit"** ou **"Editar"**
6. Cole o conteúdo abaixo:

```env
APP_NAME="GoPubLi API"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://api.gopubli.com.br

APP_LOCALE=pt_BR
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=gopublicom_gopubli
DB_USERNAME=gopublicom_gopubli
DB_PASSWORD=COLOQUE_SUA_SENHA_DO_MYSQL_AQUI

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
MAIL_HOST=mail.gopubli.com.br
MAIL_PORT=587
MAIL_USERNAME=noreply@gopubli.com.br
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@gopubli.com.br"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"
```

7. **IMPORTANTE**: Substitua `COLOQUE_SUA_SENHA_DO_MYSQL_AQUI` pela senha real do banco MySQL
8. Clique em **"Save Changes"** ou **"Salvar Alterações"**
9. Feche o editor

## Passo 5: Ajustar Permissões das Pastas

1. Volte para `/home/gopublicom/api.gopubli.com.br/shared/`
2. Selecione a pasta `storage` (marque o checkbox)
3. Clique em **"Permissions"** ou **"Permissões"** no topo
4. Configure para `775` ou marque as caixas:
   - Owner: Read, Write, Execute
   - Group: Read, Write, Execute
   - World: Read, Execute
5. ✅ Marque **"Recurse into subdirectories"** (aplicar em subpastas)
6. Clique em **"Change Permissions"**

7. Repita para a pasta `bootstrap/cache`:
   - Selecione `bootstrap`
   - Permissões `775`
   - Recurse into subdirectories
   - Change Permissions

## Passo 6: Configurar Document Root

1. Saia do File Manager
2. No cPanel, vá em **"Domínios"** ou **"Domains"**
3. Encontre `api.gopubli.com.br`
4. Clique em **"Gerenciar"** ou **"Manage"**
5. Altere **"Document Root"** para: `/home/gopublicom/api.gopubli.com.br/current/public`
6. Salve

## Passo 7: Autorizar Chave SSH

1. No cPanel, vá em **"Acesso SSH"** ou **"SSH Access"**
2. Clique em **"Gerenciar chaves SSH"**
3. Na seção **"Chaves públicas"**, encontre `id_rsa`
4. Clique em **"Gerenciar"** (ícone de engrenagem)
5. Clique em **"Autorizar"**
6. Confirme

## ✅ Próximo Passo: Fazer Deploy

Agora no seu computador local:

```bash
cd c:\laragon\www\gopubli-back
git add .
git commit -m "Initial deployment setup"
git push origin main
```

O GitHub Actions fará o deploy automaticamente! 🚀

Acompanhe em: **GitHub → Seu repositório → Aba "Actions"**
