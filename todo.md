# todo

## Current

- translate
  - move interfaces to `Translate` folder
    - `TranslateEntityDTO`
    - `TranslateInterface`
    - `TranslateStorageInterface`
  - depracate
    - `TranslateStorage`
    - `TranslateEntityModelRepositoryInterface`
    - `TranslateEntityDTOFactoryInterface`
    - `TranslateEntityModel`
    - `TranslateEntityModelFactory`
  - translate service
    - depend only on
      - `TranslateStorageInterface`
      - `TranslateEntityDTO`

- controller has an uniqe id
  - [+] created id
  - [-] check unique

## Next

- @todos
- retriving path by id from root
- dynamic router takes default lang from headers  
- HEAD method
