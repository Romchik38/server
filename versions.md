# Versions

## upcoming

## v1.1.4

- added abstract class RouterHeader

## v1.1.3

- added functions to RouterHeadersInterface ( to work with upcoming Header Collection )

## v1.1.2

- refactored HttpRouterResultInterface
- fixes database postgres
- Controller's method `setChild` now throws an error when the root controller is added as a child

## v.1.1.1

- controller name 'root' moved to sitemap interface

## v.1.1.0

- added Redirect service to router
- tests for Redirect service and a test for router

## v.1.0.0

- http server
- http router
- controller
- actions
- sql repositories
  repository ( 1 table )  
  composedId ( primary id has more than 1 column )  
  entity ( EAV )  
  Virtual ( 2 and more tables )  
- sql database ( PostgreSql)
- Models and DTOs
- Logger
  Echo
  Email
  File
- Mailer ( phpmail )
- http Session
- sitemap
- http view