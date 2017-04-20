dependencies:
	composer install --quiet \
		--no-interaction \
		--no-plugins \
		--no-scripts

tests: dependencies
	vendor/bin/phpunit --configuration ./tests/phpunit.xml tests --verbose
