<?php
// Instalar dependências manualmente
// Acesse: https://api.gopubli.com.br/install.php?secret=gopubli2026

$secret = $_GET['secret'] ?? '';
if ($secret !== 'gopubli2026') die('Acesso negado');

set_time_limit(600); // 10 minutos
echo "<pre>";
echo "🚀 Instalando GoPubli API...\n\n";

$basePath = '/home/gopublicom/repositories/back-gopubli';
chdir($basePath);

echo "📍 Diretório: " . getcwd() . "\n\n";

// Instalar Composer
echo "📦 Instalando dependências do Composer...\n";
exec('/opt/cpanel/ea-php83/root/bin/php /opt/cpanel/composer/bin/composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev 2>&1', $output, $return);
echo implode("\n", $output) . "\n\n";

// Gerar APP_KEY
echo "🔑 Gerando APP_KEY...\n";
exec('/opt/cpanel/ea-php83/root/bin/php artisan key:generate --force 2>&1', $output2);
echo implode("\n", $output2) . "\n\n";

// Migrations
echo "🗄️ Executando migrations...\n";
exec('/opt/cpanel/ea-php83/root/bin/php artisan migrate --force 2>&1', $output3);
echo implode("\n", $output3) . "\n\n";

// Storage link
echo "🔗 Criando storage link...\n";
exec('/opt/cpanel/ea-php83/root/bin/php artisan storage:link 2>&1', $output4);
echo implode("\n", $output4) . "\n\n";

// Cache
echo "⚡ Otimizando cache...\n";
exec('/opt/cpanel/ea-php83/root/bin/php artisan config:cache 2>&1');
exec('/opt/cpanel/ea-php83/root/bin/php artisan route:cache 2>&1');
exec('/opt/cpanel/ea-php83/root/bin/php artisan view:cache 2>&1');

echo "\n✅ Instalação concluída!\n";
echo "⚠️ DELETE este arquivo agora!\n";
echo "Acesse: https://api.gopubli.com.br\n";
echo "</pre>";
?>
