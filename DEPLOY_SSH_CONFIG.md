# 🔐 Configuração dos Secrets do GitHub - Deploy via SSH

## Secrets Necessários

Configure os seguintes secrets no GitHub:

**Settings → Secrets and variables → Actions → New repository secret**

### 1. SSH_HOST
```
Valor: gopubli.com.br
```
Ou o IP do servidor se o domínio não estiver configurado.

### 2. SSH_USERNAME
```
Valor: gopublicom
```

### 3. SSH_PASSWORD
```
Valor: [sua senha SSH]
```

### 4. SSH_PORT
```
Valor: 22
```
(Porta padrão SSH, pode ser diferente dependendo do servidor)

---

## 📋 Como Descobrir os Valores

### Testar Conexão SSH

No PowerShell, teste a conexão:
```powershell
ssh gopublicom@gopubli.com.br
```

Se pedir porta diferente:
```powershell
ssh -p 22 gopublicom@gopubli.com.br
```

### Verificar Porta SSH

Se não souber a porta, teste as comuns:
- **22** - Porta padrão
- **2222** - Comum em hosting compartilhado
- Consulte o painel de controle do hosting

---

## 🚀 Como Adicionar os Secrets

1. Vá para: https://github.com/SEU-USUARIO/gopubli-back/settings/secrets/actions

2. Clique em **"New repository secret"**

3. Adicione cada secret:
   - **Name**: SSH_HOST
   - **Value**: gopubli.com.br
   - Clique **"Add secret"**

4. Repita para os outros 3 secrets

---

## ✅ Verificar se Está Funcionando

Após configurar os secrets:

1. **Commit e Push**:
   ```bash
   git add .
   git commit -m "feat: deploy via SSH configurado"
   git push origin main
   ```

2. **Acompanhar Deploy**:
   - Vá em: **Actions** no GitHub
   - Veja o workflow "Deploy to GoPubli via SSH"
   - Acompanhe os logs

3. **Se der erro**:
   - Verifique se os valores dos secrets estão corretos
   - Verifique se consegue conectar via SSH manualmente
   - Veja os logs do workflow para identificar o erro

---

## 🔧 Estrutura do Servidor

O deploy vai criar automaticamente:

```
/home/gopublicom/
├── api.gopubli.com.br/          # Aplicação Laravel
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── public/                   # Document Root
│   ├── storage/
│   ├── vendor/
│   └── .env                      # Você precisa criar manualmente
├── backups/                      # Backups automáticos do .env
│   └── .env.YYYYMMDD_HHMMSS
└── deploy_temp/                  # Temporário (deletado após deploy)
```

---

## 📝 Arquivo .env no Servidor

**IMPORTANTE**: O arquivo `.env` NÃO é enviado no deploy por segurança.

Você precisa criar manualmente via SSH ou FTP:

### Via SSH:
```bash
ssh gopublicom@gopubli.com.br
cd api.gopubli.com.br
nano .env
```

Cole o conteúdo do `.env`:
```env
APP_NAME="GoPubLi API"
APP_ENV=production
APP_KEY=
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
DB_PASSWORD=SUA_SENHA_MYSQL

SESSION_DRIVER=database
SESSION_LIFETIME=120

CACHE_STORE=database
QUEUE_CONNECTION=database
```

Salve: `Ctrl+O` → `Enter` → `Ctrl+X`

---

## 🔄 Fluxo do Deploy

1. **GitHub Actions**:
   - Instala PHP e dependências
   - Instala Node e compila assets
   - Cria arquivo `.tar.gz` com a aplicação

2. **Upload via SCP**:
   - Envia `deploy.tar.gz` para `~/deploy_temp/`
   - Envia `deploy-remote.sh` para `~/deploy_temp/`

3. **Execução via SSH**:
   - Cria backup do `.env` atual
   - Extrai arquivos no diretório correto
   - Restaura `.env`
   - Ajusta permissões
   - Executa migrations
   - Otimiza cache

4. **Limpeza**:
   - Remove arquivos temporários
   - Mantém backups do `.env`

---

## 🐛 Troubleshooting

### Erro de conexão SSH
```
Verifique:
- SSH_HOST está correto
- SSH_PORT está correto (tente 22 ou 2222)
- SSH_USERNAME está correto
- SSH_PASSWORD está correto
```

### Erro de permissão
```bash
# Via SSH, execute:
cd /home/gopublicom/api.gopubli.com.br
chmod -R 775 storage bootstrap/cache
```

### .env não encontrado
```bash
# Crie o arquivo .env no servidor:
ssh gopublicom@gopubli.com.br
cd api.gopubli.com.br
nano .env
# Cole o conteúdo e salve
```

### Erro de database
```
Verifique no .env:
- DB_DATABASE está correto
- DB_USERNAME está correto
- DB_PASSWORD está correto
- Banco de dados foi criado no painel
```

---

## 📞 Próximos Passos

Após configurar os secrets e criar o `.env`:

1. ✅ Faça um commit e push
2. ✅ Acompanhe o deploy no GitHub Actions
3. ✅ Acesse: https://api.gopubli.com.br/check.php (para verificar)
4. ✅ Se tudo OK, delete o check.php por segurança
5. ✅ Configure o Document Root no painel para: `/home/gopublicom/api.gopubli.com.br/public`
