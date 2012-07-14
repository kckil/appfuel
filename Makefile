AF_ENV ?= prod
PHPUNIT_DIR := test
PHPUNIT_XML := phpunit.xml.dist
PHPUNIT_CMD := phpunit --configuration=$(PHPUNIT_XML)
CONFIG_BUILD_CMD := bin/build-config
CONFIG_BUILD_FILE := app/build/config.json

config: app/config-settings.php
	$(CONFIG_BUILD_CMD) --env=$(AF_ENV) --build-file=$(CONFIG_BUILD_FILE)

.PHONY: test

test: 
	$(PHPUNIT_CMD)
