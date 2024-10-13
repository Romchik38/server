# TranslateStorage

Class `Romchik38\Server\Services\Translate\TranslateStorage`

- Map Database Models to TranslateEntityDTO
- Returns a hash, where *key* is a key to translate, *value* - `TranslateEntityDTOInterface`

1. Init
2. Usage

## 1. Init

Required dependencies:

- Repository
- DTO Factory

## 2. Usage

- get data by languages

```php
$translateStorage->getDataByLanguages(['en', 'uk']);
```

- get data by key

```php
$translateStorage->getAllDataByKey('some.key');
```
