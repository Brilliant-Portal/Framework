# Changelog

All notable changes to `brilliant-portal/framework` will be documented in this file.

# 2.1.0 — 2023-11-06

- Feature: allow other BrilliantPortal packages to specify project for modified vendor files URL
- Bugfix: fix users API test for combined vs. distinct name fields

# 2.0.0 — 2023-09-18

- Feature: add compatibility with Jetstream 4/Livewire 3

# 1.18.0 — 2023-11-06

- Feature: allow other BrilliantPortal pages to specify project for modified vendor files URL

# 1.17.1 — 2023-07-27

- Bugfix: add typehints to `JsonResource` to support Laravel 10’s type hints

# 1.17.0 — 2023-06-15

- Feature: add optional middleware to block SEO in non-production environments

# 1.16.1 — 2023-05-18

- Bugfix: add missing read-only `name` field for apps with distinct name fields

# 1.16.0 — 2023-05-18

- Refactor: remove unnecessary authorization check for Nova requests
- Feature: fix Vite open-in-editor URLs
- Feature: add `HasIndividualNameFields` trait for apps with distinct name fields

# 1.15.0 — 2023-05-02

- Feature: add OpenAPI documentation deep links
- Bugfix: check for authentication first before checking for team
- Bugfix: don’t check API token when authorizing models if not present

# 1.14.1 — 2023-05-01

- Bugfix: fix test return types and capitalization

# 1.14.0 — 2023-05-01

- Feature: add `HasTeamPermission` middleware
- Feature/bugfix: improve Inertia support
- Bugfix: fix OpenAPI response documentation for creating users
- Bugfix: fix skipped tests

# 1.13.0 — 2023-02-17

- Feature: Laravel 10 compatibility

# 1.12.3 — 2023-01-13

- Bugfix: fix permissions for models not owned by teams
- Bugfix: improve authorization checks
- Bugfix: fix user factory

# 1.12.2 — 2022-10-28

- Bugfix: fix `php artisan brilliant-portal:publish-branding` command for apps with no published Jetstream resources

# 1.12.1 — 2022-10-24

- Bugfix: fix installation command typo
- Tweak: improve IDE support for Ignition and Laravel Debugbar
- Tweak: improve Telescope and Horizon scheduled commands

# 1.12.0 — 2022-09-23

- Feature: automatically register OpenAPI tags
- Chore: update OpenAPI documentation viewer
- Bugfix: fix OpenAPI documentation errors

# 1.11.0 — 2022-09-23

- Feature: automatically register OpenAPI resource locations
- Bugfix: allow super-admins to do anything even without API features
- Bugfix: add Airdrop S3 dependency and env keys
- Bugfix: fix Airdrop default directory and installation
- Bugfix: don’t install Livewire Vite config for non-Livewire apps

# 1.10.1 — 2022-09-01

- Bugfix: verify that database connection works before trying to migrate
- Bugfix: don’t run the long-running `npm run dev` Vite process
- Bugfix: fix Sparkpost `.env` data

# 1.10.0 — 2022-09-01

- Feature: add Sparkpost mail driver as recommended dependency
- Feature: add Laravel Pint as recommended dev dependency
- Feature: add Vite and Airdrop configuration

# 1.9.0 — 2022-08-03

- Feature: add `NotImplemented` API response schema
- Feature: improve dark mode typography for API documentation
- Feature: add a `Gate::before()` check to allow super-admins any ability
- Bugfix: fix a test class name

# 1.8.0 — 2022-04-14

- Feature: improve dark mode typography

# 1.7.1 — 2022-03-03

- Feature: update dependencies to add Laravel 9 compatibility

# 1.7.0 — 2022-02-10

- Feature: add Laravel 9 compatibility
- Feature: add deployment test for route and config caching
- Feature: add [Larastan](https://github.com/nunomaduro/larastan) as a suggested dev dependency
- Bugfix: fix failing test when teams support is disabled
- Bugfix: fix facade accessor
- Tweak: add default bottom margin to button components

# 1.6.0 — 2021-07-28

- Feature: add pill view components
- Bugfix: fix text color on disabled button component

# 1.5.0 — 2021-07-21

- Feature: fire the `Illuminate\Auth\Events\Registered` event when a user is created
- Tweak: improve placement of the `is_super_admin` column on the `users` table
- Bugfix: always add the `is_super_admin` column to the `users` table

# 1.4.2 — 2021-07-21

- Feature: make components more extensible

# 1.4.1 — 2021-07-21

- Feature: add env keys test
- Bugfix: fix env key path and example env content

# 1.4.0 — 2021-07-21

- Feature: add heading and button components
- Feature: add env keys for dependencies

# 1.3.2 — 2021-07-14

- Feature: add `brilliant-portal:install-tests` artisan command to install/update tests
- Bugfix: improve API auth key mechanism
- Bugfix: allow user update API request including user’s email address

# 1.3.1 — 2021-07-09

- Bugfix: fix typo in test installation

# 1.3.0 — 2021-07-09

- Feature: add super-admin middleware
- Feature: enforce minimum password complexity

# 1.2.3 — 2021-07-01

- Bugfix: fix a Jetstream conditional

# 1.2.2 — 2021-07-01

- Bugfix: fix OpenAPI security scheme config caching issue

# 1.2.1 — 202106-30

- Feature: ask to install recommended dependencies
- Bugfix: fix issue with Telescope scheduled command
- Bugfix: tweak API docs permissions

# 1.2.0 — 2021-05-12

- Feature: move some installation methods to a base file for use by other BrilliantPortal packages

# 1.1.0 — 2021-05-12

- Feature: track and report on modified vendor files for package maintainability
- Feature: suggest and install dev dependencies

## 1.0.0 — 2021-05-11

- Feature: PHP 8.0 support
- Feature: simplify OpenAPI class names
- Feature: improve IDE handling for stubs

## 0.2.0 — 2021-05-10

- Feature: add team and user invitation API endpoints and documentation

## 0.1.0 — 2021-05-10

- Initial release
