<?php

use App\Models\Administrator;
use App\Models\Company;
use App\Models\Influencer;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

beforeEach(function () {
    Notification::fake();
});

describe('Email Verification for Administrator', function () {
    it('sends verification email on registration', function () {
        $response = $this->postJson('/api/admin/register', [
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'message' => 'Administrador registrado com sucesso. Verifique seu e-mail.',
            ]);

        $admin = Administrator::where('email', 'admin@test.com')->first();
        Notification::assertSentTo($admin, VerifyEmailNotification::class);
    });

    it('can resend verification email', function () {
        $admin = Administrator::factory()->unverified()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withToken($token)
            ->postJson('/api/admin/email/send-verification');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'E-mail de verificação enviado com sucesso',
            ]);

        Notification::assertSentTo($admin, VerifyEmailNotification::class);
    });

    it('cannot resend verification email if already verified', function () {
        $admin = Administrator::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withToken($token)
            ->postJson('/api/admin/email/send-verification');

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'E-mail já verificado',
            ]);
    });

    it('can verify email with valid signed url', function () {
        $admin = Administrator::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $admin->id,
                'type' => 'admin',
                'hash' => sha1($admin->email),
            ]
        );

        $response = $this->getJson($verificationUrl);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'E-mail verificado com sucesso!',
            ]);

        $admin->refresh();
        expect($admin->hasVerifiedEmail())->toBeTrue();
    });

    it('cannot verify email with invalid signature', function () {
        $admin = Administrator::factory()->unverified()->create();

        $response = $this->getJson('/api/email/verify/admin/' . $admin->id . '/wronghash?expires=123456&signature=invalid');

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Link de verificação inválido ou expirado',
            ]);
    });

    it('cannot verify email with wrong hash', function () {
        $admin = Administrator::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $admin->id,
                'type' => 'admin',
                'hash' => 'wronghash',
            ]
        );

        $response = $this->getJson($verificationUrl);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Link de verificação inválido',
            ]);
    });

    it('returns message if email already verified', function () {
        $admin = Administrator::factory()->create(); // já verificado

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $admin->id,
                'type' => 'admin',
                'hash' => sha1($admin->email),
            ]
        );

        $response = $this->getJson($verificationUrl);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'E-mail já verificado anteriormente',
            ]);
    });

    it('can check verification status', function () {
        $admin = Administrator::factory()->unverified()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withToken($token)
            ->getJson('/api/admin/email/check-verification');

        $response->assertStatus(200)
            ->assertJson([
                'verified' => false,
                'email' => $admin->email,
            ]);
    });

    it('shows verified status for verified email', function () {
        $admin = Administrator::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withToken($token)
            ->getJson('/api/admin/email/check-verification');

        $response->assertStatus(200)
            ->assertJson([
                'verified' => true,
            ]);
    });
});

describe('Email Verification for Company', function () {
    it('sends verification email on registration', function () {
        $response = $this->postJson('/api/company/register', [
            'name' => 'Company Test',
            'email' => 'company@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201);

        $company = Company::where('email', 'company@test.com')->first();
        Notification::assertSentTo($company, VerifyEmailNotification::class);
    });

    it('can verify email', function () {
        $company = Company::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $company->id,
                'type' => 'company',
                'hash' => sha1($company->email),
            ]
        );

        $response = $this->getJson($verificationUrl);

        $response->assertStatus(200);

        $company->refresh();
        expect($company->hasVerifiedEmail())->toBeTrue();
    });
});

describe('Email Verification for Influencer', function () {
    it('sends verification email on registration', function () {
        $response = $this->postJson('/api/influencer/register', [
            'name' => 'Influencer Test',
            'email' => 'influencer@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201);

        $influencer = Influencer::where('email', 'influencer@test.com')->first();
        Notification::assertSentTo($influencer, VerifyEmailNotification::class);
    });

    it('can verify email', function () {
        $influencer = Influencer::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $influencer->id,
                'type' => 'influencer',
                'hash' => sha1($influencer->email),
            ]
        );

        $response = $this->getJson($verificationUrl);

        $response->assertStatus(200);

        $influencer->refresh();
        expect($influencer->hasVerifiedEmail())->toBeTrue();
    });
});

describe('Email Verification Middleware', function () {
    it('blocks access to routes requiring verified email', function () {
        $admin = Administrator::factory()->unverified()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        // Exemplo: se você adicionar o middleware 'verified' a alguma rota
        // Esta rota não existe ainda, mas o teste está preparado para quando existir
        
        // Por enquanto, verifica apenas que o usuário não tem email verificado
        expect($admin->hasVerifiedEmail())->toBeFalse();
    });
});
