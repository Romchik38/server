# todo

## Current

- middleware
  - RequestMiddleware
    - [+] Interface
    - [+] ControllerInterface
      - `addRequestMiddleware`
      - `requestMiddlewares`
    - Controller
      - `addRequestMiddleware`
      - `requestMiddlewares`
      - tests
  - ResponseMiddlewareInterface
    - Interface
    - Controller
      - `addResponseMiddleware`
      - `responseMiddlewares`
      - tests
- controller execute tests

## Next

- remove
  - config/error
  - tempstream
  - errors, that do not handle server code
- Virtual repository
- dynamic router takes default lang from headers  
- HEAD method
