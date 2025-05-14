# todo

## Current

- server
  - `ServerInterface` deprecated
  - `run` method renamed to `handle` with `request` param
  - changed property `serverErrorController` to `RequestHandlerInterface`
  - server now uses `RequestHandlerInterface` to gracefully work with error
  - changed property `$router` from `HttpRouterInterface` to `RequestHandlerInterface`

- controller
  - interface
  - class

- controller and action use the Request
  - controller
  - action
  - router

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
