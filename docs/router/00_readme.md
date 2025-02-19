# Router

- `Romchik38\Server\Routers\Http\PlasticineRouter`
- `Romchik38\Server\Routers\Http\DynamicRootRouter`

## Common

### Redirect

All urls, passed from Redirect Service will be normalized before `Location` header:

| type       | from Redirect Service          | Location header                |
|------------|--------------------------------|--------------------------------|
|absolute    | `http://somehost.com/some/url` | `http://somehost.com/some/url` |
|origin form | `/some/url`                    | `http://somehost.com/some/url` |
|rootless    | `some/url`                     | `http://somehost.com/some/url` |
