# Controller Tree

*Controller Tree* is a representation of the interrelationships of *controllers* in the system. It is a secure way to read the current location. The unit is a *controller DTO*, which contains information about the current controller and all its descendants.

Tree is a singly linked list. Initially, you receive the root element and can move forward through its children.

## Resolving name conflicts

In case the *name* of the current *child* matches the *name* of a *dynamic route*, the current child's name takes precedence. This behavior is implemented by the following code in `ControllerTree`

```php
// skip dynamic routes which names equal to children names
if (array_search($dynamicRoute, $childrenNames) !== false) {
    continue;
}
```
