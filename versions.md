# Versions

[n] - new  
[!] - breaking changes  
[f] - fix  

## Next

[see todo](./todo.md)

## v1.24.2

- [n] - Added Value objects to Domain - `Text` and `NonEmpty`. Created tests for them
- [n] - `Vo` now `Stringable`

## v1.24.1

- [n] - Added Value objects to Domain - `Vo`, `Number` and `Positive`. Created tests for them

## v1.24.0

- [!] `HttpRouterInterface` - removed constant `NOT_FOUND_CONTROLLER_NAME`
- [!] - `DatabaseSqlInterface` and `DatabasePostgresql` - removed `transactionQueryParams`
- [!] - `DatabasePostgresql` - refactored `queryParams` can be used in transaction

## v1.23.0

- [n] `RequestHandlerTrait` - method `serializeAcceptHeader` helps serialize accept header
- [!] refactored routers - changed not found handler name and type, refactored tests
  - `DynamicRootRouter`
  - `PlasticineRouter`
  - `RouterTrait`
- [!] `LinkTree` encode url parts
- [f] `DatabaseSqlInterface` methods `queryParams` and `transactionQueryParams` accept array with null
- [!] `Fileloader`
  - removed `__construct`
  - changed `load` logic

## v1.22.0

- [!] `Urlbuilder` - removed `request` from dep

## v1.21.0

- [!]`RequestMiddlewareInterface` method `__invoke` takes a `request` as a param

## v1.20.0

- [!] routers
  - `HttpRouterInterface` removed method execute
  - `PlasticineRouter`
    - method `execute` renamed to `handle` with `request` param
    - removed `ServerRequestInterface` property
  - `DynamicRootRouter`
    - method `execute` renamed to `handle` with `request` param
    - removed `ServerRequestInterface` property

- [!] server
  - `ServerInterface` deprecated
  - `run` method renamed to `handle` with `request` param
  - changed property `serverErrorController` to `RequestHandlerInterface`
  - server now uses `RequestHandlerInterface` to gracefully work with error
  - changed property `$router` from `HttpRouterInterface` to `RequestHandlerInterface`

- [!] controller
  - method `execute` changed to `handle` with `request` param

## v1.19.1

- [n] `OrderBy` to create sql SearchCriteria
- `DatabasePostgresql`
  - [f!] functions `queryParams` and `transactionQueryParams` can return `null`
  - refactor methods behavior
    - check connection is opened and is ok
    - `queryParams` does not throw DatabaseExcaption

## v1.19.0

- [!] fully refactored progect structure.

## v1.18.0

- [n] Controller `Name` can contain percent sign `%`
- [n] `DatabasePostgresql`
  - added Integration tests
  - new functions `close`, `connectionStatus`, `isConnected`
  - refactored other functions
  - `transactionStart` takes isolation level
  - `__construct` takes flag to create a new connection
- [!] removed `CreateConnectionException`
- [!] renamed `DatabaseInterface` to `DatabaseSqlInterface`

## v1.17.1

- [n] Controller `Name` can contain unreserved characters
  - alpha  = lowalpha | hialpha
  - digit  = "0" | "1" | "2" | "3" | "4" | "5" | "6" | "7" | "8" | "9"
  - safe   = "$" | "-" | "_" | "." | "+"
  - extra  = "!" | "*" | "'" | "(" | ")" | ","
- [n] Translate Storage that implements `TranslateStorageInterface` must use `TranslateStorageException` on any database/structure errors
- [n] `TranslateUseDynamicRoot` when catch a`TranslateStorageException`:
  - do log
  - return the key

## v1.17.0

- [!] Refactored `Translate` service
  - moved interfaces to `Translate` folder
    - `TranslateEntityDTO`
    - `TranslateEntityDTOInterface`
    - `TranslateInterface`
    - `TranslateStorageInterface`
  - removed
    - `TranslateStorage`
    - `TranslateEntityDTOFactory`
  - depracate
    - `TranslateEntityModelRepositoryInterface`
    - `TranslateEntityDTOFactoryInterface`
    - `TranslateEntityModelInterface`
    - `TranslateEntityModelFactoryInterface`
    - `TranslateEntityModel`
    - `TranslateEntityModelFactory`
    - `TranslateEntityModelRepository`
  - translate service
    - removed `hash`
    - removed @var `currentLang`
    - renamed to `TranslateUseDynamicRoot`
    - add `AbstractTranslate` class
    - added tests

## v1.16.4

- [f] Controller `Name` can contain `0` and `-`
- [!] `DatabaseInterface` moved to models
- [n] `PostgresDatabase` does not show html warnings

## v1.16.3

- [n] `DatabasePostgresql` - added function to work with transactions
- [n] Controller `Name` can contain number

## v1.16.2

- [f] `Session` - public functions moved to `SessionInterface`
- [n] `Controller` - has an unique id. Based on the name if not set.

## v1.16.1

- [!] removed `UrlbuilderInterface`, `UrlbuilderFactoryInterface`, Http `Urlbuilder`, Http UrlbuilderFactory`
- [n] `Urlbuilder` - added new function `fromArray`

## v1.16.0

- [f] `Urlbuiler` - changed request property type from `RequestInterface` to `ServerRequestInterface` because it uses incoming server request to form an uri
- [f] DynamicRootRouter - add port to redirect or replace with authority
- [n] `Urlbuilder` - added `DynamicTarget` to create dynamic urls
- [!] removed `DynamicRootDTOFactoryInterface` and DynamicRootDTOFactory`
- [!] refactored `DynamicRoot` - all classes/interfaces at the same namespace

## v1.15.3

- [n] Controller path refactoring is started:
  - changed `Controller` property `path` to `name`
  - added new entity `Path` which represents a query path
  - adden new entity `Name`. This is a controller name and used to construct an url
  - added service `Urlbuilder`
  - added helper class `Target` to creat request target for non dynamic root
  - tests

## v1.15.2

- removed
  - `MissingRequiredParameterInFileErrorException`
  - `ActionProcessException`
  - `EntityLogicException`
  - `src/Models/Errors/InvalidArgumentException.php`
  - `RepositoryConsistencyException`
  - `CantCreateViewException`
- moved to module folders
  - `EarlyAccessToCurrentRootErrorException`
  - `CantCreateControllerTreeElementException`
  - `CantCreateRedirectException`
  - `CantSendEmailException`
  - `SessionDoesnWorkException`
  - `TranslateException`
- changed Controller return type from `ControllerResultInterface` to `ResponseInterface`
- removed
  - `ControllerResult`
  - `ControllerResultInterface`

## v1.15.1

[n] Request middleware - `RequestMiddlewareInterface`, controller execution and tests
[n] Response middleware - `ResponseMiddlewareInterface`, controller execution and tests
[n] docs - updated controller docs, added middleware section

## v1.15.0

- added laminas-coding-standard
- changed class name:
  - `MissingRequiredParameterInFile` to `MissingRequiredParameterInFileException`
  - `View` to `AbstractView`
  - `CannotCreateMetadataError` to `CannotCreateMetadataErrorException`
  - `CantCreateControllerTreeElement` to `CantCreateControllerTreeElementException`
  - `RouterProccessError` to `RouterProccessErrorException`
  - `Logger` to `AbstractLogger`
  - `EarlyAccessToCurrentRootError` to `EarlyAccessToCurrentRootErrorException`
  - `QueryExeption` to `QueryException`
  - `DynamicRootAction` to `AbstractDynamicRootAction`
  - `MultiLanguageAction` to `AbstractMultiLanguageAction`
  - `CreateConnectionExeption` to `CreateConnectionException`
  - `Action` to `AbstractAction`
  - `CantCreateControllerChain` to `CantCreateControllerChainException`
- `Repository` changed type of `$id` param from `mixed` to `int|string`

## v1.14.1

- [n] - `DTO` now can be serialized to json with json_encode
- [!] - `PageView` method `__construct` params now are type of `Closure`
  - `$controllerTemplate`
  - `$generateTemplate`
- [!] removed `CompositeId` models
- added `phpstan` checks level 8

## v1.14.0

- [!] PSR-7 Response - Routers, actions, controller, server now use `ResponseInterface`.
  - replace `HttpRouterResult` with `ResponseInterface` from psr-7 package
    - refactor:
      - `DefaultActionInterface`
      - `DynamicActionInterface`
      - `ControllerResultInterface`
      - `Controller`
        - delete:
          - `src/Results/Controller/ControllerResultFactory.php`
          - `src/Api/Results/Controller/ControllerResultFactoryInterface.php`
        - move to Controller dir
          - `src/Results/Controller/ControllerResult.php`
          - `ControllerResultInterface`
        - refactor
          - `Controller`
          - `ControllerResult`
      - Router
        - refactor:
          - `src/Api/Routers/Http/HttpRouterInterface.php`
          - `src/Routers/Http/PlasticineRouter.php`
          - `src/Routers/Http/DynamicRootRouter.php`
        - delete
          - `src/Api/Routers/RouterInterface.php`
          - `src/Results/Http/HttpRouterResult.php`
          - `src/Api/Results/Http/HttpRouterResultInterface.php`
      - Server
        - `src/Servers/Http/DefaultServer.php`
      - Tests
  - removed `Headers` classes

## v1.13.0

- [!] Routers and Redirect Service uses PSR-7 ServerRequestInterface. Refactored tests

## v1.12.0

- [!] `PlasticineRouter` - controller array replaced with `ControllersCollection`, tests, some fixes
- [!] `Controller` - method `getDescription` now throws error on wrong dynamic route, changed return type - removed null.
- [!] `Controller` - now `ControllerResultFactory` needed if an action added
- [n] `Controller` - is fully tested

## v1.11.1

- [n] Added `FileLoader` class to services  
- [f] fixed class name `CantCreateControllerTreeElement`  
- [n] Added `TempStream` - can write data to `php://temp` and do this with own `write` method, or with given callable. Returns data as a string.  

## v1.11.0

- [f] `LinkTree` - description now become "home" on "root" controller  
- [n] Added new `ActionNotFoundException` - must be used in any `Action` to indicate not found error.  
- [!] Removed `DynamicActionNotFoundException`. Use `ActionNotFoundException` instead.  
- [!] Refactored `Controller` - now catches `ActionNotFoundException` on dynamic and default action `execute` call.  

## v1.10.1

- [n] added new service `Urlbuilder`  
- [n] added new Action exception `ActionProcessException`  
- [n] added new Model exceptions `EntityLogicException` and `RepositoryConsistencyException`  

## v1.10.0

- [n] `ControllerDTO` - added `getDescription` method and tests  
- [n] `DefaultActionInterface` - new method `getDescription` and tests  
- [!n] `DynamicActionInterface` - removed `getRoutes`, added `getDynamicRoutes`, added new method `getDescription`  
- [n] created `DynamicRouteDTO`, test  
- refactored `Controller` - method `getFullPath` is public now  
- refactored `Action` - method `getPath` uses `controller->getFullPath()` to build the path, added tests  
- [!] Fully refactored `Sitemap` - renamed to `ControllerTree`, implement new logic do show description, tests  
- [!] removed `ControllerDTOFactory`  
- [!] `LinkTree` mapper - implemented new logic to work with `ControllerTree`, tests  
- [!] removed `LinkCollection` and `LinkDTOFactory`  
- [!n] LinkTreeDTO - `__construct` now throws InvalidArgumentException, tests  
- [!] removed `LinkTreeDTOFactoryInterface`  
- [!] removed `LinkTreeDTOFactory`  
- [!] `Breadcrumb` mapper - implemented new logic to work with `ControllerTree`, tests, removed `BreadcrumbDTOFactoryInterface` and `LinkDTOCollectionInterface` from dependencies  
- [!] deleted `BreadcrumbDTOFactory` and `BreadcrumbDTOFactoryInterface`  

## v1.9.4

- [n] Adde a new method `getQueryParams` to `ServerRequestInterface`  

## v1.9.3

- Added requirement PHP v.8.3 to composer.json  
- `DefaultServer` now logs error catched from the server error controller  
- [n] Added a new method to `MultiLanguageAction`  

## v1.9.2

- [!] `DynamicRootRouter` - refactor 404 controller name  
- `ServerInterface`, `DefaultServer` - refactor server-error controller name  

## v1.9.1

- `DynamicRootRouter` - fix bug with controller name 404  

## v1.9.0

- [!] refactored `LinkDTOCollectionInterface` - $paths become optional  
- [n] `Action` - added new function *getPath*  
- [n] `MultiLanguageAction` - added new function *getLanguage*  
- [n] `LinkTree` - mapper from ControllerDTO to LinkTreeDTO and tests.  
- [n] `LinkTreeDTO` - Represents a http link to visit. Can have children. Also added tests.  

## v1.8.0

- [!] refactor folders `Sitemap`, `Breadcrumb`  
- added test for `Breadcrumb` service  
- refactored `BreadcrumbDTO`  

## v1.7.1

[n] - `LinkDTO` for Breadcrumb service, and tests  
[n] - `Breadcrumb` service  

## v1.7.0

[n] - added `BreadcrumbDTO`, `BreadcrumbDTOFactory` and tests for  them  
[!] - Refactored `DynamicRootRouter`, added `ControllersCollection`, and tests.  

## v1.6.1

- `Translate`, `TranslateEntityModelRepository`, `TranslateStorage`, `TranslateStorageInterface`, `TranslateEntityModelRepositoryInterface` - refactored, added new methods, `Translate` can works with specific language via *translate* method. Tested.

## v1.6.0

- `ControllerDTO` - fixed interface, added tests
- `Translate` - added new function *translate*
- [!] Refactored `ActionInterface` - renamed TYPE_DEFAULT_ACTION and it's value
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
