<?php

use App\Models\Administrator;
use App\Models\Company;
use App\Models\Influencer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
});

describe('Profile Update', function () {
    it('can update administrator profile', function () {
        $admin = Administrator::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withToken($token)
            ->putJson('/api/admin/profile', [
                'name' => 'Updated Admin Name',
                'phone' => '11888888888',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Perfil atualizado com sucesso',
            ]);

        $this->assertDatabaseHas('administrators', [
            'id' => $admin->id,
            'name' => 'Updated Admin Name',
            'phone' => '11888888888',
        ]);
    });

    it('can update company profile with cnpj', function () {
        $company = Company::factory()->create();
        $token = $company->createToken('test-token')->plainTextToken;

        $response = $this->withToken($token)
            ->putJson('/api/company/profile', [
                'name' => 'Updated Company',
                'cnpj' => '98765432000100',
                'address' => 'New Address, 123',
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'name' => 'Updated Company',
            'cnpj' => '98765432000100',
        ]);
    });

    it('can update influencer profile with social media', function () {
        $influencer = Influencer::factory()->create();
        $token = $influencer->createToken('test-token')->plainTextToken;

        $response = $this->withToken($token)
            ->putJson('/api/influencer/profile', [
                'name' => 'Updated Influencer',
                'instagram' => '@updated_insta',
                'tiktok' => '@updated_tiktok',
                'bio' => 'Updated bio text',
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('influencers', [
            'id' => $influencer->id,
            'instagram' => '@updated_insta',
            'tiktok' => '@updated_tiktok',
            'bio' => 'Updated bio text',
        ]);
    });
});

describe('Avatar Upload', function () {
    it('can upload avatar for administrator', function () {
        $admin = Administrator::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $file = UploadedFile::fake()->image('avatar.jpg', 100, 100);

        $response = $this->withToken($token)
            ->postJson('/api/admin/profile/avatar', [
                'avatar' => $file,
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'avatar_url',
                'user',
            ]);

        // Verifica se o arquivo foi armazenado
        Storage::disk('public')->assertExists('avatars/' . $file->hashName());

        // Verifica se o caminho foi salvo no banco
        $admin->refresh();
        expect($admin->avatar)->not->toBeNull();
    });

    it('can upload avatar for influencer', function () {
        $influencer = Influencer::factory()->create();
        $token = $influencer->createToken('test-token')->plainTextToken;

        $file = UploadedFile::fake()->image('avatar.png', 200, 200);

        $response = $this->withToken($token)
            ->postJson('/api/influencer/profile/avatar', [
                'avatar' => $file,
            ]);

        $response->assertStatus(200);
        Storage::disk('public')->assertExists('avatars/' . $file->hashName());
    });

    it('validates avatar file size', function () {
        $admin = Administrator::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        // Arquivo maior que 2MB
        $file = UploadedFile::fake()->create('avatar.jpg', 3000);

        $response = $this->withToken($token)
            ->postJson('/api/admin/profile/avatar', [
                'avatar' => $file,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['avatar']);
    });

    it('validates avatar file type', function () {
        $admin = Administrator::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $file = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->withToken($token)
            ->postJson('/api/admin/profile/avatar', [
                'avatar' => $file,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['avatar']);
    });

    it('replaces old avatar when uploading new one', function () {
        $admin = Administrator::factory()->create([
            'avatar' => 'avatars/old-avatar.jpg',
        ]);
        $token = $admin->createToken('test-token')->plainTextToken;

        // Cria arquivo antigo no storage
        Storage::disk('public')->put('avatars/old-avatar.jpg', 'old content');

        $newFile = UploadedFile::fake()->image('new-avatar.jpg');

        $response = $this->withToken($token)
            ->postJson('/api/admin/profile/avatar', [
                'avatar' => $newFile,
            ]);

        $response->assertStatus(200);

        // Verifica que o arquivo antigo foi removido
        Storage::disk('public')->assertMissing('avatars/old-avatar.jpg');

        // Verifica que o novo arquivo existe
        Storage::disk('public')->assertExists('avatars/' . $newFile->hashName());
    });
});

describe('Logo Upload (Company)', function () {
    it('can upload logo for company', function () {
        $company = Company::factory()->create();
        $token = $company->createToken('test-token')->plainTextToken;

        $file = UploadedFile::fake()->image('logo.png', 300, 300);

        $response = $this->withToken($token)
            ->postJson('/api/company/profile/logo', [
                'logo' => $file,
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'logo_url',
                'user',
            ]);

        Storage::disk('public')->assertExists('logos/' . $file->hashName());
    });

    it('cannot upload logo as administrator', function () {
        $admin = Administrator::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $file = UploadedFile::fake()->image('logo.png');

        $response = $this->withToken($token)
            ->postJson('/api/admin/profile/logo', [
                'logo' => $file,
            ]);

        $response->assertStatus(404); // Rota não existe para admin
    });

    it('cannot upload logo as influencer', function () {
        $influencer = Influencer::factory()->create();
        $token = $influencer->createToken('test-token')->plainTextToken;

        $file = UploadedFile::fake()->image('logo.png');

        $response = $this->withToken($token)
            ->postJson('/api/influencer/profile/logo', [
                'logo' => $file,
            ]);

        $response->assertStatus(404); // Rota não existe para influencer
    });
});

describe('Delete Avatar', function () {
    it('can delete avatar', function () {
        $admin = Administrator::factory()->create([
            'avatar' => 'avatars/test-avatar.jpg',
        ]);
        $token = $admin->createToken('test-token')->plainTextToken;

        Storage::disk('public')->put('avatars/test-avatar.jpg', 'content');

        $response = $this->withToken($token)
            ->deleteJson('/api/admin/profile/avatar');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Imagem removida com sucesso',
            ]);

        Storage::disk('public')->assertMissing('avatars/test-avatar.jpg');

        $admin->refresh();
        expect($admin->avatar)->toBeNull();
    });

    it('can delete logo for company', function () {
        $company = Company::factory()->create([
            'logo' => 'logos/test-logo.png',
        ]);
        $token = $company->createToken('test-token')->plainTextToken;

        Storage::disk('public')->put('logos/test-logo.png', 'content');

        $response = $this->withToken($token)
            ->deleteJson('/api/company/profile/avatar');

        $response->assertStatus(200);

        Storage::disk('public')->assertMissing('logos/test-logo.png');

        $company->refresh();
        expect($company->logo)->toBeNull();
    });
});
