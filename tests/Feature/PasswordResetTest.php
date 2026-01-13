<?php

use App\Models\Administrator;
use App\Models\Company;
use App\Models\Influencer;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    Notification::fake();
});

describe('Password Reset for Administrator', function () {
    it('can request password reset', function () {
        $admin = Administrator::factory()->create([
            'email' => 'admin@test.com',
        ]);

        $response = $this->postJson('/api/admin/forgot-password', [
            'email' => 'admin@test.com',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Se o e-mail existir em nossa base, você receberá as instruções para redefinir sua senha.',
            ]);

        // Verifica se a notificação foi enviada
        Notification::assertSentTo($admin, ResetPasswordNotification::class);

        // Verifica se o token foi salvo no banco
        $this->assertDatabaseHas('administrator_password_resets', [
            'email' => 'admin@test.com',
        ]);
    });

    it('returns same message for non-existent email', function () {
        $response = $this->postJson('/api/admin/forgot-password', [
            'email' => 'nonexistent@test.com',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Se o e-mail existir em nossa base, você receberá as instruções para redefinir sua senha.',
            ]);
    });

    it('can reset password with valid token', function () {
        $admin = Administrator::factory()->create([
            'email' => 'admin@test.com',
            'password' => Hash::make('oldpassword'),
        ]);

        $token = 'valid-reset-token';

        // Insere token no banco
        DB::table('administrator_password_resets')->insert([
            'email' => 'admin@test.com',
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        $response = $this->postJson('/api/admin/reset-password', [
            'email' => 'admin@test.com',
            'token' => $token,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Senha redefinida com sucesso! Por favor, faça login com sua nova senha.',
            ]);

        // Verifica se a senha foi atualizada
        $admin->refresh();
        expect(Hash::check('newpassword123', $admin->password))->toBeTrue();

        // Verifica se o token foi removido
        $this->assertDatabaseMissing('administrator_password_resets', [
            'email' => 'admin@test.com',
        ]);

        // Verifica se todos os tokens de acesso foram revogados
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $admin->id,
            'tokenable_type' => Administrator::class,
        ]);
    });

    it('cannot reset password with invalid token', function () {
        $admin = Administrator::factory()->create([
            'email' => 'admin@test.com',
        ]);

        DB::table('administrator_password_resets')->insert([
            'email' => 'admin@test.com',
            'token' => Hash::make('valid-token'),
            'created_at' => now(),
        ]);

        $response = $this->postJson('/api/admin/reset-password', [
            'email' => 'admin@test.com',
            'token' => 'invalid-token',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Token inválido',
            ]);
    });

    it('cannot reset password with expired token', function () {
        $admin = Administrator::factory()->create([
            'email' => 'admin@test.com',
        ]);

        $token = 'expired-token';

        DB::table('administrator_password_resets')->insert([
            'email' => 'admin@test.com',
            'token' => Hash::make($token),
            'created_at' => now()->subMinutes(61), // Expirado (60 min)
        ]);

        $response = $this->postJson('/api/admin/reset-password', [
            'email' => 'admin@test.com',
            'token' => $token,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Token expirado',
            ]);
    });

    it('validates password confirmation', function () {
        $admin = Administrator::factory()->create([
            'email' => 'admin@test.com',
        ]);

        $token = 'valid-token';

        DB::table('administrator_password_resets')->insert([
            'email' => 'admin@test.com',
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        $response = $this->postJson('/api/admin/reset-password', [
            'email' => 'admin@test.com',
            'token' => $token,
            'password' => 'newpassword123',
            'password_confirmation' => 'differentpassword',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    });
});

describe('Password Reset for Company', function () {
    it('can request password reset', function () {
        $company = Company::factory()->create([
            'email' => 'company@test.com',
        ]);

        $response = $this->postJson('/api/company/forgot-password', [
            'email' => 'company@test.com',
        ]);

        $response->assertStatus(200);

        Notification::assertSentTo($company, ResetPasswordNotification::class);

        $this->assertDatabaseHas('company_password_resets', [
            'email' => 'company@test.com',
        ]);
    });

    it('can reset password', function () {
        $company = Company::factory()->create([
            'email' => 'company@test.com',
        ]);

        $token = 'valid-token';

        DB::table('company_password_resets')->insert([
            'email' => 'company@test.com',
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        $response = $this->postJson('/api/company/reset-password', [
            'email' => 'company@test.com',
            'token' => $token,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(200);
    });
});

describe('Password Reset for Influencer', function () {
    it('can request password reset', function () {
        $influencer = Influencer::factory()->create([
            'email' => 'influencer@test.com',
        ]);

        $response = $this->postJson('/api/influencer/forgot-password', [
            'email' => 'influencer@test.com',
        ]);

        $response->assertStatus(200);

        Notification::assertSentTo($influencer, ResetPasswordNotification::class);

        $this->assertDatabaseHas('influencer_password_resets', [
            'email' => 'influencer@test.com',
        ]);
    });
});
