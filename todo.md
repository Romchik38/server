# todo

## Current

- PSR-7
  - replace `HttpRouterResult` with `ResponseInterface` from psr-7 package
    - refactor:
      - [+] `DefaultActionInterface`
      - [+] `DynamicActionInterface`
      - [+] `ControllerResultInterface`
      - [+] `Controller`
        - delete:
          - [+] `src/Results/Controller/ControllerResultFactory.php`
          - [+] `src/Api/Results/Controller/ControllerResultFactoryInterface.php`
        - move to Controller dir
          - [+] `src/Results/Controller/ControllerResult.php`
          - [+] `ControllerResultInterface`
        - refactor
          - [+] `Controller`
          - [+] `ControllerResult`
      - Router
        - refactor:
          - `src/Api/Routers/Http/HttpRouterInterface.php`
          - `src/Routers/Http/DynamicRootRouter.php`
          - `src/Routers/Http/PlasticineRouter.php`
          - `src/Servers/Http/DefaultServer.php`
        - delete
          - `src/Api/Routers/RouterInterface.php`
          - `src/Results/Http/HttpRouterResult.php`
          - `src/Api/Results/Http/HttpRouterResultInterface.php`
  - remove `Headers`

## Next

- Required extension: pgsql in `Database`
- middleware
  - PSR-15: HTTP
- Virtual repository
- phpstan  
- dynamic router takes default lang from headers  
- HEAD method
