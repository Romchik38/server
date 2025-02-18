# todo

## Current

- PSR-7
  - [+] replace `interface ServerRequestInterface` with interface from psr-7 package
    - [+] delete:
      - [+] `src/Api/Services/Request/Http`
      - [+] `src/Services/Request/Http`
      - [+] `tests/unit/Services/Request/Http/ServerRequestTest.php`
    - [+] refactor:
      - [+] `src/Routers/Http/PlasticineRouter.php`
      - [+] `src/Routers/Http/DynamicRootRouter.php`
      - [+] `src/Services/Redirect/Http/Redirect.php`
      - [+] `tests/unit/Services/Redirect/Http/RedirectTest.php`
      - [+] `tests/unit/Routers/Http/PlasticineRouterTest.php`
      - [+] `tests/unit/Routers/Http/DynamicRootRouterTest.php`
  - create a tag
  - check how it works
  - replace `HttpRouterResult` with `ResponseInterface` from psr-7 package

## Next

- Required extension: pgsql in `Database`
- middleware
  - PSR-15: HTTP
- Virtual repository
- phpstan  
- dynamic router takes default lang from headers  
- HEAD method
