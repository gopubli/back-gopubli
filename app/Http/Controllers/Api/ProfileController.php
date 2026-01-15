<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Upload de avatar/logo
     */
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = $request->user();
        
        // Remove avatar anterior se existir
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Salva novo avatar
        $path = $request->file('avatar')->store('avatars', 'public');
        
        $user->avatar = $path;
        $user->save();

        return response()->json([
            'message' => 'Avatar atualizado com sucesso',
            'avatar_url' => config('app.url') . '/storage/' . $path,
            'user' => $user
        ]);
    }

    /**
     * Upload de logo (específico para empresas)
     */
    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $user = $request->user();
        
        // Verifica se é uma empresa
        if (!$user instanceof \App\Models\Company) {
            return response()->json([
                'message' => 'Esta funcionalidade está disponível apenas para empresas'
            ], 403);
        }

        // Remove logo anterior se existir
        if ($user->logo && Storage::disk('public')->exists($user->logo)) {
            Storage::disk('public')->delete($user->logo);
        }

        // Salva novo logo
        $path = $request->file('logo')->store('logos', 'public');
        
        $user->logo = $path;
        $user->save();

        return response()->json([
            'message' => 'Logo atualizado com sucesso',
            'logo_url' => config('app.url') . '/storage/' . $path,
            'user' => $user
        ]);
    }

    /**
     * Remove avatar/logo
     */
    public function deleteAvatar(Request $request)
    {
        $user = $request->user();
        
        $field = $user instanceof \App\Models\Company ? 'logo' : 'avatar';
        
        if ($user->$field && Storage::disk('public')->exists($user->$field)) {
            Storage::disk('public')->delete($user->$field);
        }

        $user->$field = null;
        $user->save();

        return response()->json([
            'message' => 'Imagem removida com sucesso',
            'user' => $user
        ]);
    }

    /**
     * Atualizar perfil
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        
        // Validações baseadas no tipo de usuário
        $rules = [
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|nullable|string',
        ];

        if ($user instanceof \App\Models\Company) {
            $rules['cnpj'] = 'sometimes|nullable|string|unique:companies,cnpj,' . $user->id;
            $rules['address'] = 'sometimes|nullable|string';
        } elseif ($user instanceof \App\Models\Influencer) {
            $rules['cpf'] = 'sometimes|nullable|string|unique:influencers,cpf,' . $user->id;
            $rules['instagram'] = 'sometimes|nullable|string';
            $rules['tiktok'] = 'sometimes|nullable|string';
            $rules['youtube'] = 'sometimes|nullable|string';
            $rules['bio'] = 'sometimes|nullable|string';
        }

        $validated = $request->validate($rules);
        
        $user->update($validated);

        return response()->json([
            'message' => 'Perfil atualizado com sucesso',
            'user' => $user->fresh()
        ]);
    }
}
