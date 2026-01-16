<?php
/**
 * Script de Setup do Servidor
 * Acesse: https://api.gopubli.com.br/setup-server.php
 * DELETE ESTE ARQUIVO APÓS EXECUTAR!
 */

// Proteção básica (remova se precisar)
$secret = $_GET['secret'] ?? '';
if ($secret !== 'gopubli2026') {
    die('Acesso negado. Use: ?secret=gopubli2026');
}

echo "<h1>🚀 Setup GoPubli API</h1>";
echo "<pre>";

try {
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    
    echo "✅ Laravel carregado!\n\n";
    
    // 1. Gerar APP_KEY
    echo "📝 Gerando APP_KEY...\n";
    $kernel->call('key:generate', ['--force' => true]);
    echo "✅ APP_KEY gerada!\n\n";
    
    // 2. Limpar caches
    echo "🧹 Limpando caches...\n";
    $kernel->call('cache:clear');
    $kernel->call('config:clear');
    $kernel->call('route:clear');
    $kernel->call('view:clear');
    echo "✅ Caches limpos!\n\n";
    
    // 3. Executar migrations
    echo "🗄️  Executando migrations...\n";
    $kernel->call('migrate', ['--force' => true]);
    echo "✅ Migrations executadas!\n\n";
    
    // 4. Executar seeders
    echo "🌱 Executando seeders...\n";
    try {
        $kernel->call('db:seed', ['--force' => true]);
        echo "✅ Seeders executados!\n\n";
    } catch (Exception $e) {
        echo "⚠️  Seeders com erro (pode ser normal): " . $e->getMessage() . "\n\n";
    }
    
    // 5. Criar storage link
    echo "🔗 Criando storage link...\n";
    try {
        $kernel->call('storage:link');
        echo "✅ Storage link criado!\n\n";
    } catch (Exception $e) {
        echo "⚠️  Storage link: " . $e->getMessage() . "\n\n";
    }
    
    // 6. Otimizar para produção
    echo "⚡ Otimizando para produção...\n";
    $kernel->call('config:cache');
    $kernel->call('route:cache');
    $kernel->call('view:cache');
    echo "✅ Otimização concluída!\n\n";
    
    echo "========================================\n";
    echo "🎉 SETUP CONCLUÍDO COM SUCESSO!\n";
    echo "========================================\n\n";
    echo "⚠️  IMPORTANTE: DELETE ESTE ARQUIVO AGORA!\n";
    echo "Arquivo: public/setup-server.php\n\n";
    echo "Acesse: https://api.gopubli.com.br\n";
    
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "\nDetalhes:\n";
    echo $e->getTraceAsString();
}

echo "</pre>";
?>
