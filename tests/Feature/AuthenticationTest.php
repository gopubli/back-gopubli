<?php

use App\Models\Administrator;
use App\Models\Company;
use App\Models\Influencer;

beforeEach(function () {
    // Limpa o banco antes de cada teste
    Administrator::query()->delete();
    Company::query()->delete();
    Influencer::query()->delete();
});

describe('Administrator Authentication', function () {
    it('can register a new administrator', function () {
        $response = $this->postJson('/api/admin/register', [
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'name', 'email'],
                'token',
                'type',
            ])
            ->assertJson([
                'type' => 'administrator',
            ]);

        $this->assertDatabaseHas('administrators', [
            'email' => 'admin@test.com',
        ]);
    });

    it('cannot register with invalid email', function () {
        $response = $this->postJson('/api/admin/register', [
            'name' => 'Admin Test',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    });

    it('cannot register with mismatched passwords', function () {
        $response = $this->postJson('/api/admin/register', [
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => 'password123',
            'password_confirmation' => 'different123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    });

    it('can login with valid credentials', function () {
        $admin = Administrator::factory()->create([
            'email' => 'admin@test.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/admin/login', [
            'email' => 'admin@test.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'user',
                'token',
                'type',
            ]);
    });

    it('cannot login with invalid credentials', function () {
        $admin = Administrator::factory()->create([
            'email' => 'admin@test.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/admin/login', [
            'email' => 'admin@test.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    });

    it('cannot login when account is inactive', function () {
        $admin = Administrator::factory()->create([
            'email' => 'admin@test.com',
            'password' => bcrypt('password123'),
            'active' => false,
        ]);

        $response = $this->postJson('/api/admin/login', [
            'email' => 'admin@test.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    });

    it('can get authenticated user profile', function () {
        $admin = Administrator::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withToken($token)
            ->getJson('/api/admin/me');

        $response->assertStatus(200)
            ->assertJson([
                'type' => 'administrator',
            ])
            ->assertJsonStructure([
                'user' => ['id', 'name', 'email'],
            ]);
    });

    it('cannot access profile without token', function () {
        $response = $this->getJson('/api/admin/me');

        $response->assertStatus(401);
    });

    it('can logout successfully', function () {
        $admin = Administrator::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withToken($token)
            ->postJson('/api/admin/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Logout realizado com sucesso',
            ]);

        // Token deve ser revogado
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $admin->id,
            'tokenable_type' => Administrator::class,
        ]);
    });
});

describe('Company Authentication', function () {
    it('can register a new company', function () {
        $response = $this->postJson('/api/company/register', [
            'name' => 'Test Company',
            'email' => 'company@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'cnpj' => '12345678000190',
            'phone' => '11999999999',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'type' => 'company',
            ]);

        $this->assertDatabaseHas('companies', [
            'email' => 'company@test.com',
            'cnpj' => '12345678000190',
        ]);
    });

    it('can login as company', function () {
        $company = Company::factory()->create([
            'email' => 'company@test.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/company/login', [
            'email' => 'company@test.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'type' => 'company',
            ]);
    });

    it('cannot access admin routes with company token', function () {
        $company = Company::factory()->create();
        $token = $company->createToken('test-token')->plainTextToken;

        $response = $this->withToken($token)
            ->getJson('/api/admin/me');

        $response->assertStatus(403);
    });
});

describe('Influencer Authentication', function () {
    it('can register a new influencer', function () {
        $response = $this->postJson('/api/influencer/register', [
            'name' => 'Test Influencer',
            'email' => 'influencer@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'cpf' => '12345678900',
            'instagram' => '@testinfluencer',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'type' => 'influencer',
            ]);

        $this->assertDatabaseHas('influencers', [
            'email' => 'influencer@test.com',
            'instagram' => '@testinfluencer',
        ]);
    });

    it('can login as influencer', function () {
        $influencer = Influencer::factory()->create([
            'email' => 'influencer@test.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/influencer/login', [
            'email' => 'influencer@test.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'type' => 'influencer',
            ]);
    });
});
