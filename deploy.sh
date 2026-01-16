#!/bin/bash

# Script de Deploy - GoPubLi API
# Este script é executado no servidor após o upload dos arquivos

set -e  # Parar em caso de erro

# Cores para output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${GREEN}🚀 Iniciando deploy...${NC}"

# Variáveis
RELEASE_ID=$1
DEPLOY_PATH="${DEPLOY_PATH:-/home/gopublicom/api.gopubli.com.br}"
RELEASE_PATH="$DEPLOY_PATH/releases/$RELEASE_ID"
CURRENT_PATH="$DEPLOY_PATH/current"
SHARED_PATH="$DEPLOY_PATH/shared"

echo -e "${YELLOW}📂 Release: $RELEASE_ID${NC}"
echo -e "${YELLOW}📁 Path: $DEPLOY_PATH${NC}"

# Criar diretórios shared se não existirem
echo -e "${GREEN}📁 Configurando diretórios compartilhados...${NC}"
mkdir -p $SHARED_PATH/storage/{app,framework,logs}
mkdir -p $SHARED_PATH/storage/framework/{cache,sessions,views}
mkdir -p $SHARED_PATH/storage/app/public
mkdir -p $SHARED_PATH/bootstrap/cache

# Criar link simbólico para storage e bootstrap/cache
echo -e "${GREEN}🔗 Criando links simbólicos...${NC}"
rm -rf $RELEASE_PATH/storage
ln -nfs $SHARED_PATH/storage $RELEASE_PATH/storage

rm -rf $RELEASE_PATH/bootstrap/cache
ln -nfs $SHARED_PATH/bootstrap/cache $RELEASE_PATH/bootstrap/cache

# Link para .env
echo -e "${GREEN}🔗 Configurando .env...${NC}"
ln -nfs $SHARED_PATH/.env $RELEASE_PATH/.env

# Verificar se .env existe
if [ ! -f "$SHARED_PATH/.env" ]; then
    echo -e "${RED}❌ Arquivo .env não encontrado em $SHARED_PATH/.env${NC}"
    echo -e "${YELLOW}Por favor, crie o arquivo .env antes de continuar${NC}"
    exit 1
fi

# Navegar para o diretório da release
cd $RELEASE_PATH

# Instalar/atualizar dependências do Composer
echo -e "${GREEN}📦 Instalando dependências do Composer...${NC}"
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Verificar se APP_KEY existe, senão gerar
echo -e "${GREEN}🔑 Verificando APP_KEY...${NC}"
if ! grep -q "APP_KEY=base64:" $SHARED_PATH/.env; then
    echo -e "${YELLOW}Gerando APP_KEY...${NC}"
    php artisan key:generate --force
fi

# Executar migrations
echo -e "${GREEN}🗄️  Executando migrations...${NC}"
php artisan migrate --force

# Limpar e recriar cache
echo -e "${GREEN}🧹 Limpando cache...${NC}"
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Otimizar aplicação
echo -e "${GREEN}⚡ Otimizando aplicação...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Criar link do storage público (se necessário)
echo -e "${GREEN}🔗 Criando link do storage público...${NC}"
php artisan storage:link || true

# Ajustar permissões
echo -e "${GREEN}🔒 Ajustando permissões...${NC}"
chmod -R 775 $SHARED_PATH/storage
chmod -R 775 $SHARED_PATH/bootstrap/cache

# Atualizar link simbólico 'current' para a nova release
echo -e "${GREEN}🔄 Atualizando release atual...${NC}"
ln -nfs $RELEASE_PATH $CURRENT_PATH

# Remover releases antigas (manter apenas as 3 últimas)
echo -e "${GREEN}🧹 Removendo releases antigas...${NC}"
cd $DEPLOY_PATH/releases
ls -t | tail -n +4 | xargs -r rm -rf

# Restart queue workers (se estiver usando)
# echo -e "${GREEN}🔄 Reiniciando queue workers...${NC}"
# php $CURRENT_PATH/artisan queue:restart

echo -e "${GREEN}✅ Deploy concluído com sucesso!${NC}"
echo -e "${GREEN}📍 Aplicação disponível em: $CURRENT_PATH${NC}"

# Verificar saúde da aplicação
echo -e "${GREEN}🏥 Verificando saúde da aplicação...${NC}"
cd $CURRENT_PATH

# Testar se a aplicação está respondendo
if php artisan about > /dev/null 2>&1; then
    echo -e "${GREEN}✅ Aplicação está funcionando corretamente!${NC}"
else
    echo -e "${RED}⚠️  Aviso: Não foi possível verificar o status da aplicação${NC}"
fi

echo -e "${GREEN}🎉 Deploy finalizado!${NC}"
