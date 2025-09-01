# PHP Server for web app under php-fpm

![status](https://placehold.co/15x15/f03c15/f03c15.png) `status: development`
![phpstan](https://placehold.co/15x15/1589F0/1589F0.png) `phpstan: level 8`
![phpunit](https://placehold.co/15x15/c5f015/c5f015.png) `phpunit: partially`

## Features (v1.18.0)

- Http routing based on `PSR-7` Server Request and Response
  - Server
  - [Router](./docs/router/)
  - Views
    - abstract
    - page
  - [Controller system](./docs/controller/00_readme.md)
    - actions
    - [middlewares](./docs/controller/03_middleware.md)
    - mappers:
      - Sitemap
      - Breadcrumbs
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

## Examples

Examples shows how *php-server* works.

### Site1

- simple site with dynamic pages and login system
- [github](https://github.com/Romchik38/site1)
- [live preview](https://site1.romanenko-studio.dev/)

### Site2

Coming soon. In progress.

- multilanguage system
- View based on twig

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