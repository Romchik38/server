# todo

## Current

- [!] refactor routers - change not found controller to request handler
  - `DynamicRootRouter`
    - refactor
    - test
  - `PlasticineRouter`
    - refactor
    - test
  - `RouterTrait`

- `DatabaseSqlInterface` methods `queryParams` and `transactionQueryParams` accept array with null
- show error page and log message - `error: Controller name  is invalid` when url is `/uk/` (ends with slash)
- Fileloader can be constructed withou deps

- HandlerRouter
  - main router
  - redirect handler
  - dynamic root handler
  - controller handler
  - not found handler

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
