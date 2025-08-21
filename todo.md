# todo

## Current

- [n] controller - can hold request middleware result
  - `null` - pass the same `request` to the next handler
  - `ResponseInterface` - returns the `response`
  - other type - add a result to the `request` as an attribute and pass modified(new) `request` to the next handler

## Next

- HandlerRouter
  - main router
  - redirect handler
  - dynamic root handler
  - controller handler
  - not found handler

- Controller tree
  - can indicate about action not present, so Linktree will form blank url

- VO
  - add `JsonSerializable`
  - set of values
  - pattern

- controller has an uniqe id
  - [+] created id
  - [-] check unique

- retriving path by id from root
- dynamic router takes default lang from headers  
- HEAD method
- Transactions in the application layer
