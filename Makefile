install:
	composer install
validate:
	composer validate
lint:
	composer exec --verbose phpcs -- --standard=PSR12 src/ bin/
test:
	composer exec --verbose phpunit tests
test-coverage:
	composer exec --verbose phpunit tests -- --coverage-clover build/logs/clover.xml
xml:
	bin/gendiff files/file1.json files/file2.json
yml:
	bin/gendiff files/file1.yaml files/file2.yaml