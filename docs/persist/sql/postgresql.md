# Postgresql

## Restore connection

Connection for single queries can be restored. To use this pass `true` as a 3d parameter to class `__construct` method.

```php
new DatabasePostgresql('some_params', 0, true);
```

After that you can use `queryParams` as many as you want. This will `not work` when using `transactions`.
