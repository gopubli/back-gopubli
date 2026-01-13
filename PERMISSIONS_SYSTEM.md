# Sistema de Permissões - GoPubli API

## 📋 Visão Geral

Sistema completo de **RBAC (Role-Based Access Control)** implementado para gerenciar permissões de **Administradores** e **Empresas** no painel do GoPubli.

---

## 🔑 Componentes Principais

### 1. **Roles (Papéis)**
Grupos de permissões que podem ser atribuídos a usuários.

**Campos:**
- `name`: Identificador único (ex: `super-admin`, `company-manager`)
- `display_name`: Nome exibido (ex: "Super Administrador")
- `description`: Descrição do papel
- `active`: Status ativo/inativo

**Roles Padrão:**
- `super-admin`: Acesso total ao sistema
- `admin`: Administrador com permissões limitadas
- `company-manager`: Gerencia campanhas e contratos da empresa
- `company-viewer`: Apenas visualiza dados da empresa

### 2. **Permissions (Permissões)**
Ações específicas que podem ser executadas no sistema.

**Campos:**
- `name`: Identificador único (ex: `contracts.view`, `campaigns.create`)
- `display_name`: Nome exibido
- `description`: Descrição da permissão
- `module`: Módulo relacionado (ex: `contratos`, `campanhas`)
- `active`: Status ativo/inativo

**Módulos e Permissões:**
- **Usuários**: `users.view`, `users.create`, `users.edit`, `users.delete`
- **Empresas**: `companies.view`, `companies.create`, `companies.edit`, `companies.delete`
- **Influencers**: `influencers.view`, `influencers.create`, `influencers.edit`, `influencers.delete`
- **Campanhas**: `campaigns.view`, `campaigns.create`, `campaigns.edit`, `campaigns.delete`, `campaigns.manage`
- **Contratos**: `contracts.view`, `contracts.create`, `contracts.edit`, `contracts.delete`, `contracts.approve`
- **Financeiro**: `financial.view`, `financial.manage`
- **Relatórios**: `reports.view`, `reports.export`
- **Configurações**: `settings.view`, `settings.edit`
- **Permissões**: `roles.view`, `roles.create`, `roles.edit`, `roles.delete`, `permissions.manage`

### 3. **Menus**
Sistema de menu dinâmico baseado em permissões.

**Campos:**
- `name`: Identificador único
- `display_name`: Nome exibido no menu
- `icon`: Ícone FontAwesome
- `route`: Nome da rota
- `url`: URL direta (opcional)
- `parent_id`: Menu pai (para hierarquia)
- `order`: Ordem de exibição
- `active`: Status ativo/inativo

**Menus Padrão:**
- Dashboard (público - sem permissão)
- Usuários (com submenus: Administradores, Empresas, Influencers)
- Campanhas
- Contratos
- Financeiro
- Relatórios
- Configurações (com submenus: Papéis e Permissões, Sistema)

---

## 🔐 Trait HasRoles

Adicionada aos models `Administrator` e `Company` para gerenciar roles e permissões.

### Métodos Disponíveis:

#### Gerenciamento de Roles:
```php
// Atribuir um papel ao usuário
$user->assignRole('admin');
$user->assignRole($roleModel);
$user->assignRole(['admin', 'company-manager']);

// Remover um papel
$user->removeRole('admin');

// Verificar se tem um papel
$user->hasRole('admin'); // true/false

// Verificar se tem algum dos papéis
$user->hasAnyRole(['admin', 'super-admin']); // true/false

// Verificar se tem todos os papéis
$user->hasAllRoles(['admin', 'manager']); // true/false
```

#### Gerenciamento de Permissões:
```php
// Verificar se tem uma permissão
$user->hasPermission('contracts.view'); // true/false

// Verificar se tem alguma das permissões
$user->hasAnyPermission(['contracts.view', 'contracts.create']); // true/false

// Verificar se tem todas as permissões
$user->hasAllPermissions(['contracts.view', 'contracts.create']); // true/false

// Obter todas as permissões do usuário
$permissions = $user->getAllPermissions();

// Obter menus disponíveis para o usuário
$menus = $user->getAvailableMenus();
```

---

## 🛡️ Middleware CheckPermission

Protege rotas com base em permissões.

### Uso:
```php
// No arquivo de rotas
Route::middleware(['auth:sanctum', 'permission:contracts.view'])
    ->get('/contracts', [ContractController::class, 'index']);

// Ou em grupo
Route::middleware(['auth:sanctum'])->group(function () {
    Route::middleware('permission:contracts.view')
        ->get('/contracts', [ContractController::class, 'index']);
    
    Route::middleware('permission:contracts.create')
        ->post('/contracts', [ContractController::class, 'store']);
});
```

### Resposta de Erro (403):
```json
{
  "message": "Você não tem permissão para acessar este recurso.",
  "required_permission": "contracts.view"
}
```

---

## 🚫 Registro de Administrador

**IMPORTANTE:** O registro público de administradores foi **REMOVIDO**.

- ❌ Rota removida: `POST /api/admin/register`
- ✅ Administradores só podem ser criados internamente pelo painel
- ✅ Login continua público: `POST /api/admin/login`

---

## 🏢 Acesso ao Painel

Tanto **Administradores** quanto **Empresas** têm acesso ao painel web:

### Administradores:
- Acesso completo baseado em suas roles
- Podem gerenciar todo o sistema
- Visualizam todos os módulos com permissão

### Empresas:
- Acesso ao painel para gerenciar suas campanhas
- Acesso ao mobile para operações rápidas
- Permissões limitadas ao escopo de sua empresa
- Visualizam apenas dados relacionados à sua empresa

---

## 📊 Estrutura do Banco de Dados

### Tabelas Criadas:

1. **roles** - Papéis do sistema
2. **permissions** - Permissões disponíveis
3. **menus** - Menus do painel
4. **permission_role** - Relacionamento N:N (Permissão ↔ Papel)
5. **role_user** - Relacionamento Polimórfico N:N (Papel ↔ Administrator/Company)
6. **menu_permission** - Relacionamento N:N (Menu ↔ Permissão)

### Diagrama de Relacionamentos:

```
┌─────────────┐       ┌──────────────┐       ┌──────────────┐
│    Roles    │───────│PermissionRole│───────│ Permissions  │
└─────────────┘       └──────────────┘       └──────────────┘
      │                                              │
      │                                              │
┌─────────────┐                               ┌──────────────┐
│  RoleUser   │                               │MenuPermission│
│ (Polym.)    │                               └──────────────┘
└─────────────┘                                      │
      │                                              │
      ├──────────────────┐                    ┌─────────────┐
      │                  │                    │    Menus    │
┌─────────────┐   ┌─────────────┐            └─────────────┘
│Administrator│   │   Company   │
└─────────────┘   └─────────────┘
```

---

## 🎯 Próximos Passos

1. **Popular o banco com o seeder:**
   ```bash
   php artisan db:seed --class=RolesAndPermissionsSeeder
   ```

2. **Criar controllers:**
   - `RoleController` - Gerenciar roles
   - `PermissionController` - Gerenciar permissões
   - `MenuController` - Gerenciar menus
   - `AdminManagementController` - Criar administradores internamente

3. **Criar rotas protegidas:**
   - Rotas de gerenciamento de roles (apenas super-admin)
   - Rotas de gerenciamento de menus
   - Rota interna para criar administradores

4. **Implementar no Frontend:**
   - Sistema de login unificado (Admin + Company)
   - Menu dinâmico baseado em permissões
   - Validação de permissões em componentes
   - Dashboard diferenciado por tipo de usuário

5. **Testes:**
   - Testar atribuição de roles
   - Testar verificação de permissões
   - Testar middleware de permissões
   - Testar menu dinâmico

---

## 💡 Exemplos de Uso

### Criar um Administrador e Atribuir Role:
```php
$admin = Administrator::create([
    'name' => 'João Silva',
    'email' => 'joao@gopubli.com',
    'password' => Hash::make('password'),
    'active' => true,
]);

$admin->assignRole('admin');
```

### Criar uma Empresa e Atribuir Role:
```php
$company = Company::create([
    'name' => 'Empresa XYZ Ltda',
    'email' => 'contato@xyz.com',
    'cnpj' => '12.345.678/0001-90',
    'password' => Hash::make('password'),
    'active' => true,
]);

$company->assignRole('company-manager');
```

### Verificar Permissão em Controller:
```php
public function index(Request $request)
{
    if (!$request->user()->hasPermission('contracts.view')) {
        return response()->json([
            'message' => 'Sem permissão'
        ], 403);
    }
    
    // Continuar com a lógica
}
```

### Obter Menu do Usuário:
```php
public function getMenu(Request $request)
{
    $user = $request->user();
    $menus = $user->getAvailableMenus();
    
    return response()->json([
        'menus' => $menus
    ]);
}
```

---

## ⚠️ Importante

- **Sempre use o middleware `permission`** para proteger rotas sensíveis
- **Super-admin tem acesso total** - use com cuidado
- **Empresas só veem seus próprios dados** - implementar filtros por company_id
- **Administradores não podem se auto-registrar** - apenas criação interna
- **Menus são filtrados automaticamente** - use o método `getAvailableMenus()`

---

## 📚 Documentação Relacionada

- [API_DOCUMENTATION.md](API_DOCUMENTATION.md) - Documentação completa da API
- [PRACTICAL_EXAMPLES.md](PRACTICAL_EXAMPLES.md) - Exemplos práticos de uso
- Insomnia Collection - Para testes de API

---

**Sistema implementado com sucesso! 🎉**
