# PHP Server for web app under php-fpm

## Features (v1.6.1)

- http
  - server
  - router
  - session
  - views
    - abstract
    - json
    - page
  - request psr-7 ( partial )
  - server request psr-7 ( partial )
  - headers
- controller system
- sql
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
- sitemap system
- [multilanguage system](./docs/multilanguage-system/01-readme.md)

## Examples

Examples shows how *php-server* works.

### Site1

- simple site with dynamic pages and login system
- [github](https://github.com/Romchik38/site1)
- [live preview](https://site1.romanenko-studio.dev/)