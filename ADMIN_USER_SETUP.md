# Criação de Usuário Admin Padrão

## Executar o Seeder

Para criar o usuário administrador padrão, execute:

```bash
php artisan db:seed --class=AdminUserSeeder
```

Ou rode todos os seeders:

```bash
php artisan db:seed
```

## Credenciais Padrão

```
Email: admin@gopubli.com
Senha: admin123456
```

⚠️ **IMPORTANTE**: Altere a senha após o primeiro login!

## Login via API

### Endpoint
```
POST /api/v1/admin/auth/login
```

### Request Body
```json
{
  "email": "admin@gopubli.com",
  "password": "admin123456"
}
```

### Response
```json
{
  "message": "Login realizado com sucesso",
  "data": {
    "user": {
      "id": 1,
      "name": "Administrador GoPubli",
      "email": "admin@gopubli.com",
      "status": "active",
      "type": "administrator"
    },
    "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
  }
}
```

## Usar o Token

Após fazer login, use o token retornado em todas as requisições administrativas:

```bash
curl -X GET "http://localhost/api/v1/admin/reports/dashboard" \
  -H "Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" \
  -H "Accept: application/json"
```

## Alterar Senha

Após primeiro login, altere a senha através do perfil ou crie um endpoint específico:

```bash
# Exemplo futuro
PUT /api/v1/admin/profile/password
```

## Criar Novos Admins (Manual)

Se precisar criar mais administradores manualmente via tinker:

```bash
php artisan tinker
```

```php
use App\Models\Administrator;
use Illuminate\Support\Facades\Hash;

Administrator::create([
    'name' => 'Outro Admin',
    'email' => 'outro@gopubli.com',
    'password' => Hash::make('senha_segura'),
    'status' => 'active',
    'email_verified_at' => now(),
]);
```

## Verificar se o Admin Existe

```bash
php artisan tinker
```

```php
App\Models\Administrator::where('email', 'admin@gopubli.com')->first();
```
