<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Não autenticado.'
            ], 401);
        }

        // Verifica se o usuário tem o método hasPermission (Administrator ou Company)
        if (!method_exists($user, 'hasPermission')) {
            return response()->json([
                'message' => 'Acesso negado.'
            ], 403);
        }

        if (!$user->hasPermission($permission)) {
            return response()->json([
                'message' => 'Você não tem permissão para acessar este recurso.',
                'required_permission' => $permission
            ], 403);
        }

        return $next($request);
    }
}
