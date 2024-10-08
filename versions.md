# Versions

## upcoming

- class `Translate` refactored
- added a few tests for class `Translate`

## v1.5.0

- refactored *View* class
- [!] *CantCreateDTOException* class renamed into *InvalidArgumentException*  
- [!] *PageView* refactored

## v1.4.1

- added *View* class
- added *JsonView* class
- added tests for View, JsonView and dtos

## v1.4.0

- refactored Page view
[!] function `getContent` removed from *DefaultViewDTOInterface*

## v1.3.0

[!] method `setMetadata` removed from View interface  
[!] method `setMetadata` PageView class become protected  
[!] Session service and interface refactored  

- added tests for Session service

## v1.2.3

- fixed view interface
- added default view dto interfaces and models

## v1.2.2

- repository deleteById takes mixed id
- added psr-7 server request interface ( partial )

## v1.2.1

- PlasticineRouter now uses HeaderColection
- some code refactor

## v1.2.0

- added Multilanguage system

## v1.1.5

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