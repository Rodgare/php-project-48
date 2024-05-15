install:
	composer install
validate:
	composer validate
lint:
	phpcs --standard=PSR12 src/
test:
	composer exec --verbose phpunit tests
test-coverage:
	composer exec --verbose phpunit tests -- --coverage-clover build/logs/clover.xml