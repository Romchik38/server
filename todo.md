# todo

## Current

- remove
  - [+] config/error `MissingRequiredParameterInFileErrorException`
  - errors, that do not handle server code
    - `ActionProcessException`
    - `EntityLogicException`
    - `src/Models/Errors/InvalidArgumentException.php`
    - `RepositoryConsistencyException`
    - `CantCreateViewException`
- move
  - error to their module folders
    - `EarlyAccessToCurrentRootErrorException`
    - `CantCreateControllerTreeElementException`
    - `CantCreateRedirectException`
    - `CantSendEmailException`
- types from ControllerResult

## Next

- dynamic router takes default lang from headers  
- HEAD method
