# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project overview

Modular monolith built on Laravel demonstrating **vertical-slice modules** + **Command/Query (CQRS)** + **DDD strategic patterns**. Pure JSON API (no PUT/PATCH/DELETE — every state change is an explicit POST command). Implements a multi-store retail offer system: store/product/offer imports from external APIs, mobile customer endpoints (JWT), and CMS admin endpoints (Sanctum).

Composer package name is `Module\\` mapped to `app/` (note: `Module\` namespace, not `App\`).

## Common commands

- Tests: `php artisan test --compact` · single file: `php artisan test --compact tests/Feature/Foo/BarTest.php` · filter: `php artisan test --compact --filter=testName`
- Coverage HTML report: `composer coverage`
- Static analysis: `composer phpstan`
- Architecture rule check (deptrac): `composer deptrac` — **must pass** before finalizing; enforces module/layer boundaries from `deptrac.yaml`
- Formatter: `vendor/bin/pint --dirty --format agent` (also `composer pint` for full sweep of `app/` and `tests/`)
- Regenerate OpenAPI: `composer openapi` → `resources/openapi/openapi.yaml`
- Local stack: `docker compose up` (runs `start-app.sh` which migrates and imports seed data via the `*:import` commands)
- Custom artisan commands: `store:import`, `product:import`, `product:quantity`, `offer:import {store}`

## Architecture

### Module/layer structure

Each top-level directory under `app/` (except `Shared/`) is a module: `Customer/`, `Offer/`, `Product/`, `Store/`, `User/`. `Shared/` is common infrastructure (middleware, exceptions, attributes, SQL DTOs, providers), not a module.

Inside a module:
```
Access/    — Console/, Controller/  (translate external calls → Action)
Action/    — Command/, Query/, Dto/, Enum/  (the public surface; one class per action)
Model/     — Eloquent models (module-private)
Support/   — internal services, repositories, internal DTOs
```

### Hard boundary rules (enforced by `composer deptrac`)

- **Cross-module access is only via another module's `Action/` layer** (Command, Query, Dto, Enum). Never reach into another module's `Model/`, `Support/`, or `Access/`.
- `Access` → may use own `Action`/`Enum`, `Shared`, vendor. Not `Model` or `Support` directly.
- `Action` → may use own `Model`, own `Support`, and **any other module's `Action`/`Enum`**, plus `Shared`/vendor.
- `Model` → may use own `Enum`, `Shared`, vendor only.
- `Enum` → `Shared`/vendor only.
- A new cross-layer dependency that fails `deptrac` is an architecture violation — fix the design, don't add to `skip_violations`.

### CQRS conventions

- One action = one class with one public method (Command writes state, Query reads). Action classes live under `app/<Module>/Action/Command/` or `Action/Query/`.
- Input/output is exclusively via Spatie Data DTOs in `Action/Dto/`. Models never cross module boundaries.
- Models use **optimistic locking** via a `version` column — `change-visibility`-style commands compare submitted `version` to the DB row and reject if stale (see the README shipment example for the rationale).
- DB table names are prefixed per module (e.g. `Offer` model → `ofr_offer` table). Keep that convention when adding tables.

### Auth

- Mobile API (`/api/...`): JWT (firebase/php-jwt) carrying `uid` and `stid` (selected store id); validated by `Module\Shared\Middleware\JwtAuth`.
- Admin/CMS API (`/api/admin/...`): Laravel Sanctum (`auth:sanctum`).

### OpenAPI

`openapi.yaml` is **generated from DTOs + validation rules** via `knuckleswtf/scribe` + `abrha/laravel-data-docs`. Don't hand-edit it — change the DTO/validation and run `composer openapi`.

## Working in this codebase

- When adding an endpoint: define DTOs in `Action/Dto/`, write the Command/Query class in `Action/Command|Query/`, add the controller method in `Access/Controller/`, wire it in `routes/api.php`. Add a feature test under `tests/Feature/<Module>/`.
- Tests live under `tests/` mirroring module names (e.g. `tests/Customer/`). Use PHPUnit (not Pest). `TestCase.php` is the shared base.
- Before finalizing any PHP change: `vendor/bin/pint --dirty --format agent` + `composer phpstan` + `composer deptrac` + the relevant `php artisan test --compact` filter.

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.5
- laravel/framework (LARAVEL) - v13
- laravel/prompts (PROMPTS) - v0
- laravel/sanctum (SANCTUM) - v4
- larastan/larastan (LARASTAN) - v3
- laravel/boost (BOOST) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- phpunit/phpunit (PHPUNIT) - v12

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Always use `search-docs` before making code changes. Do not skip this step. It returns version-specific docs based on installed packages automatically.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Follow existing application Enum naming conventions.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== tests rules ===

# Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test --compact` with a specific filename or filter.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== phpunit/core rules ===

# PHPUnit

- This application uses PHPUnit for testing. All tests must be written as PHPUnit classes. Use `php artisan make:test --phpunit {name}` to create a new test.
- If you see a test using "Pest", convert it to PHPUnit.
- Every time a test has been updated, run that singular test.
- When the tests relating to your feature are passing, ask the user if they would like to also run the entire test suite to make sure everything is still passing.
- Tests should cover all happy paths, failure paths, and edge cases.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files; these are core to the application.

## Running Tests

- Run the minimal number of tests, using an appropriate filter, before finalizing.
- To run all tests: `php artisan test --compact`.
- To run all tests in a file: `php artisan test --compact tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --compact --filter=testName` (recommended after making a change to a related file).

</laravel-boost-guidelines>
