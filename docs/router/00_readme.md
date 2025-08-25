# Router

- Main Router
- Router Middlewares

The `MiddlewareRouter`, as a main router, processes the provided middlewares sequentially. The execution flow depends on the value returned by each middleware:

- `null`, the original request is passed to the next middleware.
- `ResponseInterface`, the execution chain is terminated, and the response is returned immediately.
- `other value`, that value is added to the request, and the updated request is passed to the next middleware.

This mechanism allows for establishing a dependency between the `request handler` and the `router middleware`.

## Default group

Use as much `HandlerRouterMiddleware` as needed with `request handler` to process a request.

## Controller group

1. Choose one of the path middleware
    - `DefaultPathRouterMiddleware`
    - `DynamicPathRouterMiddleware`
2. Set a `ControllerRouterMiddleware`
3. Use as much `HandlerRouterMiddleware` as needed in any part of the chain with `request handler` to process a request. As an expample - to handle a 404 use the middleware at the end of the chain.

## HandlerRouterMiddleware

`HandlerRouterMiddleware` must returns a response only. It does not catch any exceptions.

## DynamicPathRouterMiddleware

Returns one from:

- `null` on trailing slash
- `response` with redirect to `default root` when root part not found
- `result` with `dynamic root` and `path`

## DefaultPathRouterMiddleware

Returns one from:

- `null` on trailing slash
- `result` with `path`

## ControllerRouterMiddleware

Returns one from:

- `null` path not found
- `null` missed path result
- `null` on empty controller collection
- `ResponseInterface` - from found action
- `ResponseInterface` - 405 with method not allowed on non existing
- `ResponseInterface` with empty body on `HEAD` method

## AbstractRouterMiddleware

Use `AbstractRouterMiddleware` to implement own middleware
