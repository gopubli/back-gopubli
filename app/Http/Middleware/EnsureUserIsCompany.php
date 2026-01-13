<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsCompany
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'message' => 'Usuário não autenticado'
                ], 401);
            }
            
            if (!$user instanceof \App\Models\Company) {
                return response()->json([
                    'message' => 'Acesso negado. Apenas empresas podem acessar este recurso.',
                    'user_type' => get_class($user)
                ], 403);
            }

            return $next($request);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro no middleware',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }
}
