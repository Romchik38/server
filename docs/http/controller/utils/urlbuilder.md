# Urlbuilder

`Urlbuilders` are responsible for generating HTTP URLs according to RFC 3986.

- [AbstractUrlbuilder](./../../../../src/Http/Utils/Urlbuilder/AbstractUrlbuilder.php) - base class
- [Urlbuilder](./../../../../src/Http/Utils/Urlbuilder/Urlbuilder.php) - most commonly used class
- [StaticUrlbuilder](./../../../../src/Http/Utils/Urlbuilder/StaticUrlbuilder.php) - generates the same uri with differnet `queries` and `root`

All urlbuilders can accept URL parts in the form of a [Path](./../../../../src/Http/Controller/Path.php) or an `array of unencoded strings`. As a result, you will get an `encoded URL` that can be used for a http request.
