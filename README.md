## Hounslow API Client for Jadu.net

London Borough of Hounslow API Client specifically for use by [Jadu.net](www.jadu.net)

This version of the client ensures compatibility with the current build of Jadu used by LB Hounslow.

See [example.php](example.php) for usage.

### Releases

- These are covered in [the Changelog](CHANGELOG.md)

### Requirements

- [PHP 7.2+](https://www.php.net/downloads.php)
- [Git](https://git-scm.com/downloads)
- [Composer](https://getcomposer.org)

### Contributing

This repository is currently closed for contribution. Please [report an an issue](https://github.com/LBHounslow/hounslow-api-client/issues) if you come across one.

### Setup

- Run `composer require lb-hounslow/hounslow-api-client`.
- See [example.php](example.php) for usage.
- Requires the `API url`, `Client ID`, `Client Secret` and an active `user account` with the correct roles.

### Tests

There are 35 tests with 97 assertions.

Run `./vendor/bin/phpunit tests`
