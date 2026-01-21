<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Arquivo principal de rotas da API.
| As rotas são organizadas por versão em arquivos separados:
| - routes/apiv1.php - API Version 1
| - routes/apiv2.php - API Version 2 (futuro)
|
*/

/*
|--------------------------------------------------------------------------
| API Version 1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(base_path('routes/apiv1.php'));

/*
|--------------------------------------------------------------------------
| API Version 2 (Futuro)
|--------------------------------------------------------------------------
|
| Descomente quando criar a v2:
| Route::prefix('v2')->group(base_path('routes/apiv2.php'));
|
*/
