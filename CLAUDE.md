# CLAUDE.md

このファイルは、このリポジトリのコードを扱う際にClaude Code (claude.ai/code) に提供されるガイダンスです。

## プロジェクト概要

TsumiageはLaravel 12バックエンドAPI（PHP 8.2+）で、タスクと作業時間を追跡するアプリケーションです。開発環境にはMySQL、Redis、Nginx、phpMyAdminを含むDockerを使用します。認証はLaravel SanctumとJWTトークン（tymon/jwt-auth）で処理されます。

## 開発環境のセットアップ

```bash
# 初期セットアップ
docker compose up -d --build
docker compose exec app bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate:refresh --seed
php artisan migrate --env=testing
```

## よく使うコマンド

### Dockerコンテナ内
```bash
# appコンテナに入る
docker compose exec app bash

# データベース
php artisan migrate
php artisan migrate:refresh --seed
php artisan migrate --env=testing

# テスト
php artisan test                    # すべてのテストを実行
php artisan test --filter=TestName  # 特定のテストを実行

# コードフォーマット
./vendor/bin/pint                   # Laravel Pint (PSR-12)

# キャッシュクリア
composer dump-autoload && php artisan cache:clear && php artisan config:clear && php artisan route:clear && php artisan view:clear

# キューワーカー (Redis)
php artisan queue:work redis --queue=default
```

### Dockerコンテナ外
```bash
# Composerコマンド
composer dev      # サーバー、キュー、ログ、viteを同時実行
composer test     # 設定をクリアしてテストを実行

# MySQLアクセス
docker compose exec mysql bash
mysql -u root -ppassword -h mysql -P 3306
```

### ポート
- **8000**: Webサーバー (Nginx)
- **8080**: phpMyAdmin
- **3306**: MySQL
- **6379**: Redis

## Architecture: Action-Based Domain-Driven Design

This codebase follows a **pragmatic Clean Architecture pattern** where each user action is a separate domain.

### Request Flow

```
HTTP Request
    ↓
Controller (app/Http/Controllers/)
    → Creates Parameter from Request (app/Http/Requests/)
    ↓
Service (app/Domains/*/Services/)
    → Orchestrates business logic
    → Uses LoginInfoService for auth
    → Validates business rules
    ↓
Query (app/Domains/*/Queries/)
    → Direct database access via Eloquent
    ↓
Factory (app/Domains/*/Factories/)
    → Converts Models to Entities (for read operations)
    ↓
Controller returns Resource (app/Http/Resources/)
```

### Domain Structure

Each action domain (e.g., `TaskCreate`, `WorkReadMonth`, `UserDelete`) contains:

- **`Services/[Domain]Service.php`** - Business logic orchestrator. Handles authorization, validation, and orchestration. Injects Query and Shared services.
- **`Parameters/[Domain]Parameter.php`** - Immutable data transfer object (readonly class) created from validated request data.
- **`Queries/[Domain]Query.php`** - Database interaction layer using Eloquent models. No business logic.
- **`Factories/[Domain]Factory.php`** _(read operations only)_ - Converts Eloquent models to domain entities.
- **`Entities/[Domain]Entity.php`** _(read operations only)_ - Immutable domain objects with getter methods.

### Shared Domain (`app/Domains/Shared/`)

Contains reusable cross-cutting concerns used by action domains:

- **`LoginInfo/`** - Retrieve authenticated user data (`LoginInfoService`, `LoginInfoEntity`)
- **`Auth/`** - Authentication logic and session management
- **`User/`, `Task/`, `Work/`, `Invitation/`** - Shared entities, factories, and queries
- **`CheckIsExistEmail/`** - Email existence validation service
- **`CheckIsFriends/`** - Friendship validation service
- **`Calendar/`** - Calendar utilities

Action domains inject these services as dependencies.

### Controllers

Controllers are thin entry points (app/Http/Controllers/):
1. Inject domain Service via constructor
2. Receive validated Request
3. Create Parameter from `$request->validated()`
4. Call Service method
5. Return `SuccessResource` or `ErrorResource`

**One-to-one mapping**: Each action domain has exactly one controller with the same name.

### Models

Eloquent models are in `app/Models/` and represent database tables directly. They are only used within Query classes.

### Authentication

- Uses **Laravel Sanctum** for session-based auth with cookies
- JWT tokens via **tymon/jwt-auth**
- Routes require `['web']` middleware for cookie auth
- Protected routes add `['auth:sanctum']` middleware
- `LoginInfoService` provides current authenticated user throughout the app

## Key Conventions

### Creating New Features

When adding a new action (e.g., "TaskUpdate"):

1. **Create domain directory**: `app/Domains/TaskUpdate/`
2. **Add Query** (`Queries/TaskUpdateQuery.php`) - Database operations
3. **Add Parameter** (`Parameters/TaskUpdateParameter.php`) - Readonly DTO with `makeParams()` factory
4. **Add Service** (`Services/TaskUpdateService.php`) - Business logic, inject dependencies
5. **Add Request** (`app/Http/Requests/TaskUpdateRequest.php`) - Validation rules extending `BaseFormRequest`
6. **Add Controller** (`app/Http/Controllers/TaskUpdateController.php`) - Thin entry point
7. **Add route** in `routes/api.php` with appropriate middleware
8. **Add test** in `tests/Feature/TaskUpdateTest.php` extending `FeatureTestCase`

For read operations, also add Factory and Entity classes.

### Error Handling

Throw `AppHttpException` from Services for business rule violations:
```php
throw new AppHttpException(404, 'タスクが存在しません');
```

Controllers catch all `Throwable` and return `ErrorResource`.

### Testing

- Tests extend `FeatureTestCase` which provides `createUser()` and `loginUser()`
- Use `actingAs()` for authenticated requests
- Test database is `tsumiage_test` configured in `phpunit.xml`
- Always test both success and error cases

### Code Style

- Follow PSR-12 via Laravel Pint: `./vendor/bin/pint`
- Use readonly properties for Parameter and Entity classes
- Use dependency injection in constructors
- Services receive Request objects to access authenticated user via Sanctum
