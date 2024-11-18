# todo

## Current

- [-] Sitemap  
  - [+] Controller DTO  
    - [+] add description  
    - [+] test  
  - [+] Default action  
    - [+] new method `getDescription` to `DefaultActionInterface`  
    - [+] test  
  - [+] Dynamic Action  
    - [+] change getRoutes must return array of DynamicRouteDTO  
    - [+] create DynamicRouteDTO  
      - [+] replace `getRoutes` with `getDynamicRoutes` in the `DynamicActionInterface`  
    - [+] added new method `getDescription`
      - [+] `getDescription` must throw exception on not found description
    - [+] test  
      [+] DynamicRouteDTO  
      - [+] Dynamic Action  
      - [+] implement
      - [+] tests
  - [+] Update Controller  
  - [+] Update `Action` - method `getPath` uses `controller->getFullPath()` to build the path.
  - [+] Sitemap (ControllerTree)
    - [+] rename Sitemap to ControllerTree
    - [+] remove `ControllerDTOFactory`
    - [+] implement new logic  
    - [+] test  
  - [-+] LinkTree mapper  
    - [+] implement new logic  
    - [+] removed `LinkCollection` and `LinkDTOFactory`
    - [+] test
  - [+] LinkTreeDTO
    - [+] `__construct` throws InvalidArgumentException  
    - [+] test
    - [+] remove `LinkTreeDTOFactoryInterface`
    - [+] remove `LinkTreeDTOFactory`
  - [-] Breadcrumb mapper  
    - [+] remove `BreadcrumbDTOFactoryInterface` and `LinkDTOCollectionInterface`
    - [-] delete `BreadcrumbDTOFactory` and `BreadcrumbDTOFactoryInterface`
    - [+] implement logic  
    - [+] test  
  - [-] Action tests
- [-] create new v  

## Next

[?] Virtual repository  
[-] add controller collection to router  
[-] phpstan  
[-] dynamic router takes default lang from headers  
[-] middleware  
[-] HEAD method
