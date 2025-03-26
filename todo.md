# todo

## Current

- translate
  - move interfaces to `Translate` folder
    - [+] `TranslateEntityDTO`
    - [+] `TranslateEntityDTOInterface`
    - [+] `TranslateInterface`
    - [+] `TranslateStorageInterface`
  - remove
    - [+] `TranslateStorage`
    - [+] `TranslateEntityDTOFactory`
  - depracate
    - [+] `TranslateEntityModelRepositoryInterface`
    - [+] `TranslateEntityDTOFactoryInterface`
    - [+] `TranslateEntityModelInterface`
    - [+] `TranslateEntityModelFactoryInterface`
    - [+] `TranslateEntityModel`
    - [+] `TranslateEntityModelFactory`
    - [+] `TranslateEntityModelRepository`
  - translate service
    - depend only on
      - `TranslateStorageInterface`
      - `TranslateEntityDTOInterface`
    - remove `hash`
    - [+] remove var `currentLang`
  - add `AbstractTranslate`
  - refactor `Translate` to `TranslateUseDefaultRoot`
- controller has an uniqe id
  - [+] created id
  - [-] check unique

## Next

- @todos
- retriving path by id from root
- dynamic router takes default lang from headers  
- HEAD method
