# So you want to contribute to Parable? Great!

## How to Pull Request

1. Fork the Parable repository.
2. Create a new branch per feature or improvement.
3. Send a pull request from each of your branches against the version branch for which your fix is intended.

It's extremely helpful to keep separate features and improvements separate, so that Pull Requests don't become combinations of changes that can't easily be merged and released separately. If changes are required for one change, other changes don't need to be delayed if they're okay.

## Style Guide

All pull requests must adhere to the [PSR-2 standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md). If there's any part that doesn't do so, StyleCI _will_ complain. 

## Unit Testing

All pull requests must be accompanied by passing tests and complete code coverage. Use `make coverage` to check. Parable uses phpunit for testing.