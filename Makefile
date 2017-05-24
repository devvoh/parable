dependencies:
	composer install \
		--no-interaction \
		--no-plugins \
		--no-scripts

tests: dependencies
	vendor/bin/phpunit tests --verbose
