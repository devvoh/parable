dependencies:
	composer install \
		--no-interaction \
		--no-plugins \
		--no-scripts

tests: dependencies
	vendor/bin/phpunit --verbose tests

coverage: dependencies
	rm -rf ./coverage
	vendor/bin/phpunit --coverage-html ./coverage tests
