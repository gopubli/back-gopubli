<?php

/**
 * Redirecionamento para o diretório public
 * O DocumentRoot do servidor deve apontar para /public
 */

// Redirecionar para o diretório public
header('Location: /public/index.php' . ($_SERVER['REQUEST_URI'] ?? ''));
exit;
