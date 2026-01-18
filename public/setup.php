<?php
set_time_limit(600);
echo "<pre>🚀 Instalando GoPubLi API...\n\n";

chdir('/home/gopublicom/repositories/back-gopubli');
echo "📍 Diretório: " . getcwd() . "\n\n";

// Composer install
echo "📦 Instalando dependências do Composer (pode demorar)...\n";
passthru('/opt/cpanel/ea-php83/root/bin/php /opt/cpanel/composer/bin/composer install --no-dev --optimize-autoloader --no-interaction 2>&1');

echo "\n\n🔑 Gerando APP_KEY...\n";
passthru('/opt/cpanel/ea-php83/root/bin/php artisan key:generate --force 2>&1');

echo "\n\n🗄️ Executando migrations...\n";
passthru('/opt/cpanel/ea-php83/root/bin/php artisan migrate --force 2>&1');

echo "\n\n🔗 Criando storage link...\n";
passthru('/opt/cpanel/ea-php83/root/bin/php artisan storage:link 2>&1');

echo "\n\n⚡ Otimizando cache...\n";
passthru('/opt/cpanel/ea-php83/root/bin/php artisan config:cache 2>&1');
passthru('/opt/cpanel/ea-php83/root/bin/php artisan route:cache 2>&1');

echo "\n\n✅ INSTALAÇÃO CONCLUÍDA!";
echo "\n⚠️ DELETE este arquivo setup.php AGORA!";
echo "\n🚀 Acesse: https://api.gopubli.com.br\n";
echo "</pre>";
?>
