# todo

## Current

- [+] remove
  - config/error `MissingRequiredParameterInFileErrorException`
  - errors, that do not handle server code
    - `ActionProcessException`
    - `EntityLogicException`
    - `src/Models/Errors/InvalidArgumentException.php`
    - `RepositoryConsistencyException`
    - `CantCreateViewException`
- [+] move
  - error to their module folders
    - `EarlyAccessToCurrentRootErrorException`
    - `CantCreateControllerTreeElementException`
    - `CantCreateRedirectException`
    - `CantSendEmailException`
    - `SessionDoesnWorkException`
    - `TranslateException`
- types from ControllerResult
  - `ControllerInterface` method execute returns `ResponseInterface`
  - Controller
  - Routers
    - `DynamicRootRouter`
    - `PlasticineRouter`
  - tests
  - remove
    - `ControllerResult`
    - `ControllerResultInterface`

## Next

- @todos
- dynamic router takes default lang from headers  
- HEAD method
- Urlbuilder
  - controller has an uniqe id
  - retriving path by id from root
