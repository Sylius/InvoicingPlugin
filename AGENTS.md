# AI Contribution Guidelines

Welcome, 🤖 AI assistant! Please follow these guidelines when contributing to this Sylius:

## General Guidelines

### Project Structure & Design

- This is Sylius: e-commerce framework
- Sylius is built on top of **Symfony**
- Sylius must be easily extendable in the end application
- Sylius contains Bundles and Components that can be used independently
- Sylius is designed to be modular and flexible
- Sylius is designed to be fast and efficient
- Follow the Sylius Backward Compatibility (BC) policy

### Compatibility & Security

- Ensure compatibility with **Symfony** and **PHP** versions defined in `composer.json`
- For API configuration, use **API Platform 2.7**
- Follow secure coding practices to prevent XSS, CSRF, injections, auth bypasses, etc.

### Coding Standards & Tooling

- Use **4 spaces** for indentation in all files (PHP, YAML, XML, Twig, etc.)
- Use **PHPUnit** for unit and functional testing
- Use **Behat** for behavior-driven scenarios
- Use **ECS** to ensure consistent code style
- Use **PHPStan** for static analysis
- Use **CI** to run all tests and checks automatically

## Commands

- Run `composer install` to install PHP dependencies
- Run `vendor/bin/ecs` to fix PHP code style issues
- Run `vendor/bin/phpstan analyse` to perform static analysis
- Run `vendor/bin/phpunit` to execute unit and functional tests
- Run `yarn install` to install JavaScript dependencies
- Run `yarn encore dev` to compile frontend assets

## PHP Code

- Use modern PHP 8.1+ syntax and features
- Declare `strict_types=1` in all PHP files
- Follow the **Sylius Coding Standard**
- Do not use deprecated features from PHP, Symfony, or Sylius
- Use `final` for all classes, except entities and repositories
- Use `readonly` for immutable services and value objects
- Add type declarations for all properties, arguments, and return values
- Use `camelCase` for variables and method names
- Use `SCREAMING_SNAKE_CASE` for constants
- Use `snake_case` for configuration keys, route names, and template variables
- Use **fast returns** instead of nesting logic unnecessarily
- Use trailing commas in multi-line arrays and argument lists
- Order array keys alphabetically where applicable
- Use PHPDoc only when necessary (e.g. `@var Collection<ProductInterface>`)
- Group class elements in this order: constants, properties, constructor, public methods, protected methods, private methods
- Group getter and setter methods for the same properties together
- Suffix interfaces with Interface, traits with Trait
- Use `use` statements for all non-global classes
- Sort `use` imports alphabetically and group by type (classes, functions, constants)

## Templates

- Use modern HTML5 syntax
- Always use the most modern Twig syntax and features
- Use translations for all strings in templates, never hardcode text

## UPGRADE to 2.0

- Read the [UPGRADE guide](./UPGRADE_RULES.md) for details on changes and migration steps
