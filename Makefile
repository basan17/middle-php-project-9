PORT ?= 8000

.PHONY: start
start:
	PHP_CLI_SERVER_WORKERS=5 php -S 0.0.0.0:$(PORT) -t public

.PHONY: lint
lint:
	phpcs --standard=PSR12 ./public