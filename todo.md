# todo

## Current

- routers
  - `HttpRouterInterface` removed method execute
  - `PlasticineRouter`
    - method `execute` renamed to `handle` with `request` param
    - removed `ServerRequestInterface` property
  - `DynamicRootRouter`
    - method `execute` renamed to `handle` with `request` param
    - removed `ServerRequestInterface` property

- server
  - `ServerInterface` deprecated
  - `run` method renamed to `handle` with `request` param
  - changed property `serverErrorController` to `RequestHandlerInterface`
  - server now uses `RequestHandlerInterface` to gracefully work with error
  - changed property `$router` from `HttpRouterInterface` to `RequestHandlerInterface`

- controller
  - method `execute` changed to `handle` with `request` param

- HandlerRouter
  - main router
  - redirect handler
  - dynamic root handler
  - controller handler
  - not found handler
  - handlers, controller and action uses a Request and returns a Response

- VO
  - id in
  - id string
  - field

- controller has an uniqe id
  - [+] created id
  - [-] check unique

## Next

- @todos
- retriving path by id from root
- dynamic router takes default lang from headers  
- HEAD method
- Transactions in the application layer
