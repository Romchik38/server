# todo

## Current

- middleware
  - RequestMiddleware
    - [+] Interface
    - [+] ControllerInterface
      - [+] `addRequestMiddleware`
      - [+] `requestMiddlewares`
    - Controller
      - [+] `addRequestMiddleware`
      - [+] `requestMiddlewares`
      - [+] `tests`
  - ResponseMiddlewareInterface
    - Interface
    - Controller
      - `addResponseMiddleware`
      - `responseMiddlewares`
      - tests
- Controller execute
  - logic
    - RequestMiddleware
    - ResponseMiddleware
  - tests
    - RequestMiddleware
    - ResponseMiddleware

## Next

- remove
  - config/error
  - tempstream
  - errors, that do not handle server code
- dynamic router takes default lang from headers  
- HEAD method
