# Control

Controller is created with this principles

1. Path
2. Publicity
3. Controller Rusult Factory
4. Execution on last part of the url
5. Default Action
6. Dynamic Action
7. On any action

## 1. Path

1. It's name - execute
2. Not it's name - throw Controller Not Found Error

## 2. Publicity

Has no control. It's needed for other services.

## 3. Controller Rusult Factory

1. Needed by any action

## 4. Execution on the last part of the url

1. Last part is a controller name - execute Default Action
2. Last part not a controller name - execute Dynamic Action

## 5. Default Action

1. Return result
2. Throw Action Not found error

## 6. Dynamic Action

1. Return result
2. Throw Action Not found error

## 7. On any action

1. Create Controller Result on action response
2. Throw Controller Not Found Error on Action Not Found error
