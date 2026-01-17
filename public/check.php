<?php
/**
 * Arquivo de verificação de ambiente - Check Environment
 * Acesse: https://api.gopubli.com.br/check.php
 * 
 * IMPORTANTE: Delete este arquivo após verificar que tudo está funcionando!
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check - GoPubli API</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        .success { color: #28a745; }
        .warning { color: #ffc107; }
        .error { color: #dc3545; }
        .section { background: white; padding: 20px; margin: 20px 0; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        h2 { color: #666; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: bold; }
        .code { background: #f4f4f4; padding: 10px; border-radius: 3px; font-family: monospace; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔍 Verificação de Ambiente - GoPubli API</h1>
    
    <div class="section">
        <h2>📊 Informações do PHP</h2>
        <table>
            <tr>
                <th>Item</th>
                <th>Valor</th>
                <th>Status</th>
            </tr>
            <tr>
                <td>Versão do PHP</td>
                <td><?php echo phpversion(); ?></td>
                <td class="<?php echo version_compare(phpversion(), '8.2.0', '>=') ? 'success' : 'warning'; ?>">
                    <?php echo version_compare(phpversion(), '8.2.0', '>=') ? '✅ OK' : '⚠️ Recomendado 8.2+'; ?>
                </td>
            </tr>
            <tr>
                <td>Server API</td>
                <td><?php echo php_sapi_name(); ?></td>
                <td class="success">✅</td>
            </tr>
            <tr>
                <td>Memory Limit</td>
                <td><?php echo ini_get('memory_limit'); ?></td>
                <td class="<?php echo (int)ini_get('memory_limit') >= 128 ? 'success' : 'warning'; ?>">
                    <?php echo (int)ini_get('memory_limit') >= 128 ? '✅ OK' : '⚠️ Recomendado 128M+'; ?>
                </td>
            </tr>
            <tr>
                <td>Upload Max Filesize</td>
                <td><?php echo ini_get('upload_max_filesize'); ?></td>
                <td class="success">✅</td>
            </tr>
            <tr>
                <td>Post Max Size</td>
                <td><?php echo ini_get('post_max_size'); ?></td>
                <td class="success">✅</td>
            </tr>
            <tr>
                <td>Max Execution Time</td>
                <td><?php echo ini_get('max_execution_time'); ?>s</td>
                <td class="success">✅</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2>🔧 Extensões PHP Requeridas</h2>
        <table>
            <?php
            $required = ['mbstring', 'xml', 'ctype', 'iconv', 'pdo', 'pdo_mysql', 'json', 'openssl', 'tokenizer'];
            foreach ($required as $ext) {
                $loaded = extension_loaded($ext);
                echo "<tr>";
                echo "<td>$ext</td>";
                echo "<td class='" . ($loaded ? 'success' : 'error') . "'>";
                echo $loaded ? '✅ Instalada' : '❌ Não Instalada';
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </table>
    </div>

    <div class="section">
        <h2>📁 Diretórios e Permissões</h2>
        <table>
            <?php
            $base = dirname(__DIR__);
            $dirs = [
                'storage/app' => $base . '/storage/app',
                'storage/framework/cache' => $base . '/storage/framework/cache',
                'storage/framework/sessions' => $base . '/storage/framework/sessions',
                'storage/framework/views' => $base . '/storage/framework/views',
                'storage/logs' => $base . '/storage/logs',
                'bootstrap/cache' => $base . '/bootstrap/cache'
            ];
            
            foreach ($dirs as $name => $path) {
                $exists = is_dir($path);
                $writable = $exists && is_writable($path);
                echo "<tr>";
                echo "<td>$name</td>";
                echo "<td>$path</td>";
                echo "<td>";
                if ($exists && $writable) {
                    echo "<span class='success'>✅ OK</span>";
                } elseif ($exists) {
                    echo "<span class='warning'>⚠️ Existe mas não é gravável</span>";
                } else {
                    echo "<span class='error'>❌ Não existe</span>";
                }
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </table>
    </div>

    <div class="section">
        <h2>⚙️ Arquivos de Configuração</h2>
        <table>
            <?php
            $base = dirname(__DIR__);
            $files = [
                '.env' => $base . '/.env',
                'vendor/autoload.php' => $base . '/vendor/autoload.php',
                'bootstrap/app.php' => $base . '/bootstrap/app.php',
                'public/index.php' => __DIR__ . '/index.php'
            ];
            
            foreach ($files as $name => $path) {
                $exists = file_exists($path);
                echo "<tr>";
                echo "<td>$name</td>";
                echo "<td class='" . ($exists ? 'success' : 'error') . "'>";
                echo $exists ? '✅ Existe' : '❌ Não encontrado';
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </table>
    </div>

    <div class="section">
        <h2>🚀 Teste Laravel</h2>
        <?php
        try {
            require dirname(__DIR__) . '/vendor/autoload.php';
            $app = require_once dirname(__DIR__) . '/bootstrap/app.php';
            echo "<p class='success'>✅ Laravel carregado com sucesso!</p>";
            
            // Tentar obter configurações
            try {
                $app->make(\Illuminate\Contracts\Console\Kernel::class);
                echo "<p class='success'>✅ Kernel do Laravel inicializado!</p>";
            } catch (Exception $e) {
                echo "<p class='warning'>⚠️ Aviso ao inicializar Kernel: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
            
        } catch (Exception $e) {
            echo "<p class='error'>❌ Erro ao carregar Laravel:</p>";
            echo "<div class='code'>" . htmlspecialchars($e->getMessage()) . "</div>";
            echo "<p>Trace:</p>";
            echo "<div class='code'>" . htmlspecialchars($e->getTraceAsString()) . "</div>";
        }
        ?>
    </div>

    <div class="section">
        <h2>📝 Variáveis de Ambiente</h2>
        <table>
            <tr>
                <th>Variável</th>
                <th>Valor</th>
            </tr>
            <tr>
                <td>Document Root</td>
                <td><?php echo $_SERVER['DOCUMENT_ROOT'] ?? 'N/A'; ?></td>
            </tr>
            <tr>
                <td>Script Filename</td>
                <td><?php echo $_SERVER['SCRIPT_FILENAME'] ?? 'N/A'; ?></td>
            </tr>
            <tr>
                <td>HTTP Host</td>
                <td><?php echo $_SERVER['HTTP_HOST'] ?? 'N/A'; ?></td>
            </tr>
            <tr>
                <td>Server Software</td>
                <td><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'N/A'; ?></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2>⚠️ Ações Recomendadas</h2>
        <ul>
            <li>Se tudo estiver OK, acesse: <a href="/setup-server.php?secret=gopubli2026">setup-server.php?secret=gopubli2026</a></li>
            <li>Após configurar, <strong>DELETE ESTE ARQUIVO</strong> (check.php) por segurança</li>
            <li>Delete também o setup-server.php após a configuração</li>
            <li>Verifique se o arquivo .env existe e está configurado corretamente</li>
            <li>Certifique-se de que as permissões dos diretórios storage/ e bootstrap/cache/ estão corretas (775)</li>
        </ul>
    </div>

    <div class="section">
        <p><small>Gerado em: <?php echo date('d/m/Y H:i:s'); ?></small></p>
    </div>
</body>
</html>
