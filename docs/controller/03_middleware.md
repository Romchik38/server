# Middleware

Middleware is an interceptor, that sits between routing system and actions.

## Types

Middlewares can be one of two types:

- [Request Middleware](./../../src/Api/Controllers/Middleware/RequestMiddlewareInterface.php)
- [Response Middleware](./../../src/Api/Controllers/Middleware/ResponseMiddlewareInterface.php)

## Request Middleware

Request Middleware is placed before actions and intercepts an execution control.
It deсides what to do:

- stop execution and return its own result
- continue execution

## Response Middleware

Response Middleware is placed behind actions and intercepts an action's response. Response Middleware can modify response and must return it back.

## Conditions

- There are can be as many middlewares as you want.
- Middleware on the root controller will be executed on each controller and so on

## Example

- Root controller has
  - 1 request middleware with auth check
  - 2 response middleware
    - first adds `no cache` header if no `Cache-Control` was set
    - second adds `text/html` header if no `Content-Type` was set
- Product controller is a child of the Root controller
  - it does not have any middleware, because it's the last in the controller chain

As a result:

- all requests to Root and Product controllers will be passed through 3 middlewares.
- unauthorized requests will be blocked.
- all responses will be with `Cache-Control` and `Content-Type` headers respectively.

## Schema

                              Request
                                 |
                          Request Middleware1
                                ...
                          Request middlewareN
                                 |
                              Action
                                 |
                          Response middleware1
                                ...
                          Response middlewareN
                                 |
                              Response

## Request Middleware class

Must implement `RequestMiddlewareInterface`

```php
class SomeMiddleware implements RequestMiddlewareInterface
{
    public function __invoke(): ?ResponseInterface
    {
      // do some job
      // return new Response() to stop execution
      // or
      return null; // to pass
    }
};
```

```php
// add a request middleware to controller chain
$someMiddleware = new SomeMiddleware();
$rootController->addRequestMiddleware($someMiddleware);
```

## Response Middleware class

Must implement `ResponseMiddlewareInterface`

```php
class SomeResponseMiddleware implements ResponseMiddlewareInterface
{
    public function __invoke(ResponseInterface $response): ResponseInterface
    {
        return $response->withHeader('Cache-Control', 'no-cache');
    }
};
```

```php
// add a response middleware to controller chain
$someResponseMiddleware = new SomeResponseMiddleware();
$rootController->addResponseMiddleware($someResponseMiddleware);
```
