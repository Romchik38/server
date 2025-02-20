# PHP Server for web app under php-fpm

## Features (v1.14.0)

- Http
  - Server
  - Router
  - Session
  - Views
    - abstract
    - json
    - page
    - twig (in progress site2)
  - Server Request (psr-7)
  - Response (psr-7)
- Controller system
- Sql
  - database ( PostgreSql)
  - repositories  
    - repository ( 1 table )  
    - composedId ( primary id has more than 1 column )  
    - entity ( EAV )  
    - Virtual ( 2 and more tables )  
- Models and DTOs
- Logger psr-3 ( with alternative logging )  
  - Echo  
  - Email  
  - File  
- Mailer ( phpmail )  
- Sitemap system
- [multilanguage system](./docs/multilanguage-system/01-readme.md)

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
