<?php

/**
 * Redirecionamento para o diretório public
 * O DocumentRoot do servidor deve apontar para /public
 */

// Se já estiver no public, não redirecionar
if (file_exists(__DIR__ . '/public/index.php')) {
    require_once __DIR__ . '/public/index.php';
} else {
    echo 'Erro: Diretório public não encontrado.';
}
