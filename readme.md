# PHP Server for web app under php-fpm

![status](https://placehold.co/15x15/f03c15/f03c15.png) `status: development`
![phpstan](https://placehold.co/15x15/1589F0/1589F0.png) `phpstan: level 8`
![phpunit](https://placehold.co/15x15/c5f015/c5f015.png) `phpunit: partially`

## Features (v1.29.2)

- Http routing based on `PSR-7` Server Request and Response
  - Server
  - [Router](./docs/router/00_readme.md)
  - Views
    - abstract
    - page
  - Controller system
    - actions
    - middlewares
    - mappers:
      - Sitemap
      - Breadcrumbs
      - LinkTree
  - Utils:
    - Urlbuilder
    - Session
- Sql
  - database (PostgreSql)
  - repositories  
    - repository (1 table)
    - entity (EAV)
    - Virtual (2 and more tables)
- Models and DTOs
- Logger psr-3 ( with alternative logging )  
  - Echo  
  - Email  
  - File  
- Mailer ( phpmail )  
- [Multilanguage system](./docs/multilanguage-system/01-readme.md)
- Domain Value Objects

## Code quality

- phpstan level 8
  - ![passes](https://placehold.co/15x15/0dbc79/0dbc79.png)`[OK] No errors`  
- phpunit
  - ![passes](https://placehold.co/15x15/0dbc79/0dbc79.png)`OK (230 tests, 389 assertions)`
  - tested partially
- laminas-coding-standard
  - ![passes](https://placehold.co/15x15/0dbc79/0dbc79.png)`79 / 79 (100%)`

## Docs

- [database tests](./docs/tests/database.md)

## Projects that use this product

The php-server was used in the following projects:

- Site1
- Site2

## Site1

Site1 is a simple website — that demonstrates a multi-page site with a login system, a sitemap, Google reCAPTCHA, and email-based password recovery. See source code on [github page](https://github.com/Romchik38/site1).

Also available [Live Site1 preview](https://site1.romanenko-studio.dev/).

## Site2

Site2 is a more complex version of the site1 with more functionality. It demonstrates a multilanguage system, twig view, Image Converter and other features. See source code on [github page](https://github.com/Romchik38/site2).

Also available [Live Site2 preview](https://site2.romanenko-studio.dev/en/about-this-site).
