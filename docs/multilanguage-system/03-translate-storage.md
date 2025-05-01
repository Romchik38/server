# TranslateStorage

You must implement `Romchik38\Server\Utils\Translate\TranslateStorageInterface` to use translate.

It has only one method `getByKey` and it must return `TranslateEntityDTOInterface`.
So `TranslateStorage` is look like just `repository`. It goes to the database, takes rows,  fill a Dto and returns it back to translate. Not so hard.
