# AI Contribution Guidelines for Sylius 1.x to 2.x Upgrade

Welcome, 🤖 AI assistant! This guide helps you assist developers upgrading Sylius projects from 1.14 to 2.x. This is one of the most challenging upgrades in Sylius history.

## ⚠️ Critical Upgrade Information

### Prerequisites Check
- **PHP**: Minimum 8.2 required (8.1 and below no longer supported)
- **Node.js**: Version 20 or 22 (18 no longer supported)
- **Symfony**: 6.4 minimum, 7.2 recommended for Sylius 2.1
- **API Platform**: Major version change requires extensive refactoring

### Migration Complexity
This upgrade touches **every layer** of Sylius:
- Core framework architecture
- Database schema and entities
- Frontend (complete UI rewrite)
- API structure and endpoints
- Configuration system
- Asset management

## 📋 Pre-Migration Checklist

### Audit Current Project
1. **Document all customizations**:
    - Custom entities and their extensions
    - Custom form types and extensions
    - Custom templates and themes
    - Custom services and dependency injection
    - Custom API endpoints and serialization groups
    - Payment gateway integrations
    - Custom state machine workflows

2. **Backup everything**:
    - Database with all data
    - Complete codebase including vendor/
    - Asset files and uploads
    - Configuration files

3. **Test environment setup**:
    - Create isolated upgrade environment
    - Never attempt on production first

## 🔧 Core Migration Steps

### 1. Dependency Updates

**composer.json changes**:
```json
{
    "require": {
        "php": "^8.2",
        "sylius/sylius": "^2.1",
        "symfony/dotenv": "^6.4 || ^7.2",
        "symfony/flex": "^2.7",
        "symfony/runtime": "^6.4 || ^7.2"
    }
}
```

**Key commands**:
```bash
composer update --with-all-dependencies
composer install
```

### 2. Bundle Configuration Updates

**config/bundles.php** - Major changes required:
```php
return [
    // REMOVE these bundles:
    // winzou\Bundle\StateMachineBundle\winzouStateMachineBundle::class => ['all' => true],
    // Bazinga\Bundle\HateoasBundle\BazingaHateoasBundle::class => ['all' => true],
    // JMS\SerializerBundle\JMSSerializerBundle::class => ['all' => true],
    // FOS\RestBundle\FOSRestBundle::class => ['all' => true],
    
    // ADD these bundles:
    ApiPlatform\Symfony\Bundle\ApiPlatformBundle::class => ['all' => true],
    Sylius\TwigHooks\SyliusTwigHooksBundle::class => ['all' => true],
    Symfony\UX\TwigComponent\TwigComponentBundle::class => ['all' => true],
    Symfony\UX\StimulusBundle\StimulusBundle::class => ['all' => true],
    Symfony\UX\LiveComponent\LiveComponentBundle::class => ['all' => true],
    Symfony\UX\Autocomplete\AutocompleteBundle::class => ['all' => true],
];
```

### 3. Configuration File Updates

**config/packages/_sylius.yaml**:
```yaml
imports:
    - { resource: "@SyliusPayumBundle/Resources/config/app/config.yaml" }

sylius_payment:
    resources:
        gateway_config:
            classes:
                model: App\Entity\Payment\GatewayConfig

# Remove sylius_payum.resources.gateway_config configuration
```

**config/packages/security.yaml**:
```yaml
security:
    password_hashers:
        Sylius\Component\User\Model\UserInterface: argon2i
    
    firewalls:
        admin:
            user_checker: security.user_checker.chain.admin
        api_admin:  # renamed from new_api_admin_user
            user_checker: security.user_checker.chain.api_admin
        api_shop:   # renamed from new_api_shop_user
            user_checker: security.user_checker.chain.api_shop
        shop:
            user_checker: security.user_checker.chain.shop
```

### 4. Routing Updates

**config/routes/sylius_api.yaml**:
```yaml
sylius_api:
    resource: "@SyliusApiBundle/Resources/config/routing.yml"
    prefix: "%sylius.security.api_route%"  # changed from new_api_route
```

**config/routes/sylius_shop.yaml**:
```yaml
sylius_shop_payum:
    resource: "@SyliusPayumBundle/Resources/config/routing/integrations/sylius_shop.yaml"

sylius_payment_notify:
    resource: "@SyliusPaymentBundle/Resources/config/routing/integrations/sylius.yaml"
```

### 5. Environment Variables

**Add to .env**:
```bash
###> symfony/messenger ###
SYLIUS_MESSENGER_TRANSPORT_PAYMENT_REQUEST_DSN=sync://
SYLIUS_MESSENGER_TRANSPORT_PAYMENT_REQUEST_FAILED_DSN=sync://
###< symfony/messenger ###
```

## 🗄️ Database Migration

### Critical Steps
1. **Backup database** before any migration
2. **Run migrations**:
   ```bash
   php bin/console doctrine:migrations:migrate --no-interaction
   ```
3. **Note**: All previous migration files have been consolidated

### User Model Breaking Changes
**Removed fields** (update any custom code):
- `locked`
- `expiresAt`
- `credentialsExpireAt`
- `encoder` and `salt` (replaced by Symfony password hashers)

## 🎨 Frontend Migration (Most Complex)

### UI Framework Change
- **From**: SemanticUI + jQuery
- **To**: Bootstrap + SymfonyUX/Stimulus

### Template System Revolution
- **From**: Sonata Blocks
- **To**: Twig Hooks

### Asset Management Changes
```bash
# Update Node.js to version 20 or 22
nvm install 20
nvm use 20

# Clear old assets
rm -rf node_modules
rm package-lock.json

# Reinstall
npm install
npm run build
```

### Controller Path Updates
**assets/controllers.json** - Update all paths:
```json
{
    "@sylius/admin-bundle/slug": {"enabled": true, "fetch": "lazy"},
    "@sylius/admin-bundle/taxon-slug": {"enabled": true, "fetch": "lazy"},
    "@sylius/shop-bundle/api-login": {"enabled": true, "fetch": "lazy"}
}
```

## 🔌 API Migration

### Major Breaking Changes

**Route restructuring** - Examples:
- `POST /api/v2/admin/avatar-images` → `POST /api/v2/admin/administrators/{id}/avatar-image`
- `POST /api/v2/shop/reset-password-requests` → `POST /api/v2/shop/reset-password`
- `GET /api/v2/admin/provinces/{code}` → `GET /api/v2/admin/countries/{countryCode}/provinces/{provinceCode}`

**Serialization groups** - Add `sylius:` prefix:
```php
// Old
#[Groups(['admin:product:index'])]

// New
#[Groups(['sylius:admin:product:index'])]
```

### API Platform Changes
**DataProviders → StateProviders**:
```php
// Update namespace
// From: Sylius\Bundle\ApiBundle\DataProvider\*
// To: Sylius\Bundle\ApiBundle\StateProvider\*

// From: Sylius\Bundle\ApiBundle\DataPersister\*
// To: Sylius\Bundle\ApiBundle\StateProcessor\*
```

## 🔧 Entity and Service Updates

### Command Constructor Changes
Many API command constructors have breaking changes. **Always refer to UPGRADE-2.0.md** for complete list.

Example - RegisterShopUser:
```php
public function __construct(
    // Properties changed from public to protected
    // Added channelCode and localeCode parameters
    protected string $firstName,
    protected string $lastName,
    protected string $email,
    protected string $password,
    protected ?string $channelCode,
    protected ?string $localeCode,
    protected bool $subscribedToNewsletter = false,
)
```

### Service Reference Updates
**Always check UPGRADE-2.0.md** for service ID changes:
- `sylius.security.new_api_route` → `sylius.security.api_route`
- `sylius.security.new_api_regex` → `sylius.security.api_regex`

## 🔍 Testing Strategy

### Comprehensive Testing Required
1. **Functional testing**: All existing features
2. **API testing**: All endpoints due to extensive changes
3. **Frontend testing**: Complete UI overhaul
4. **Performance testing**: New architecture may impact performance

### Common Issues to Test
- Custom form types and extensions
- Custom API endpoints
- Custom templates (major template system change)
- Payment gateway integrations
- Custom state machine workflows
- Email templates and customizations

## 🚨 Known Gotchas

### Payment Gateways
- **Stripe and PayPal gateways removed** from core
- Only offline payment remains in core
- Must install external plugins for payment processing

### State Machine
- **Winzou State Machine** now optional dependency
- Default changed to Symfony Workflow
- Manual installation required if using Winzou:
  ```bash
  composer require winzou/state-machine winzou/state-machine-bundle
  ```

### Theme Bundle
- **Directory structure changed**:
    - `THEME/views/` → `THEME/templates/`
    - `THEME/AcmeBundle/views/` → `THEME/templates/bundles/AcmeBundle/`

### Removed Classes
**100+ classes removed** - Always check UPGRADE-2.0.md for complete list before making assumptions about class availability.

## 🛠️ Development Workflow

### Step-by-Step Approach
1. **Start with core dependencies** (composer updates)
2. **Update configuration files** (bundles, security, routing)
3. **Run database migrations**
4. **Update custom entities and services** (refer to UPGRADE-2.0.md)
5. **Migrate frontend assets** (most time-consuming)
6. **Update API customizations**
7. **Extensive testing**

### Incremental Testing
- Test after each major step
- Keep rollback plan ready
- Document issues and solutions

## 📚 Essential References

1. **Primary**: `/vendor/sylius/sylius/UPGRADE-2.0.md`
2. **API specific**: `/vendor/sylius/sylius/UPGRADE-API-2.0.md`
3. **Minor updates**: `/vendor/sylius/sylius/UPGRADE-2.1.md`
4. **Asset migration example**: [Sylius-Standard PR #1126](https://github.com/Sylius/Sylius-Standard/pull/1126)

## 🤖 AI Assistant Guidelines

### When Helping with Upgrade
1. **Always reference UPGRADE-2.0.md** for specific breaking changes
2. **Focus on one area at a time** (don't try to do everything at once)
3. **Ask about customizations** before suggesting changes
4. **Emphasize testing** after each change
5. **Recommend backup strategies**
6. **Point to official documentation** for service changes

### Common Upgrade Tasks You Can Help With
- Updating configuration files
- Migrating template structure
- Updating composer.json dependencies
- Converting Sonata Blocks to Twig Hooks
- Updating API serialization groups
- Converting SemanticUI to Bootstrap classes
- Updating asset controller configurations

### What to Avoid
- Making assumptions about custom code without seeing it
- Attempting the entire upgrade in one go
- Modifying service references without checking UPGRADE-2.0.md
- Upgrading production environments directly

## 🏁 Success Criteria

The upgrade is complete when:
- ✅ All dependencies updated successfully
- ✅ Database migrations applied without errors
- ✅ Application starts without configuration errors
- ✅ Frontend renders correctly with new UI framework
- ✅ All custom functionality works as expected
- ✅ API endpoints respond correctly
- ✅ Tests pass (unit, functional, Behat)
- ✅ Performance is acceptable

Remember: This is a major upgrade requiring significant development effort. Plan accordingly and always test thoroughly!
