RUN=docker exec php

cs: ## Run the php cs fixer analyse
	$(RUN) vendor/bin/php-cs-fixer fix --config .php-cs-fixer.dist.php

phpstan: ## Run phpstan static analysis
	$(RUN) vendor/bin/phpstan analyse

rector: ## Run rector analysis (dry-run)
	$(RUN) vendor/bin/rector process --dry-run --config=rector.php

rector-real: ## Run rector analysis
	$(RUN) vendor/bin/rector process --config=rector.php

tests: ## Run phpunit tests suit of all components
	$(RUN) vendor/bin/phpunit -c phpunit.xml src/Components/**/Tests

merge-monorepo: ## Merge the composer.json files of the components into the main one
	$(RUN) vendor/bin/monorepo-builder merge

qa: ## Run all the code quality analyse
	@echo Run cs fixer
	$(RUN) vendor/bin/php-cs-fixer fix --dry-run --config .php-cs-fixer.dist.php
	@echo Run rector
	$(RUN) vendor/bin/rector process --dry-run --config=rector.php
	@echo Run phpstan
	$(RUN) vendor/bin/phpstan analyse
	@echo Run phpunit tests
	$(RUN) vendor/bin/phpunit --color -c phpunit.xml src/Components/**/Tests


BLACK        := $(shell tput -Txterm setaf 0)
RED          := $(shell tput -Txterm setaf 1)
GREEN        := $(shell tput -Txterm setaf 2)
YELLOW       := $(shell tput -Txterm setaf 3)
LIGHTPURPLE  := $(shell tput -Txterm setaf 4)
PURPLE       := $(shell tput -Txterm setaf 5)
BLUE         := $(shell tput -Txterm setaf 6)
WHITE        := $(shell tput -Txterm setaf 7)

.DEFAULT_GOAL := help

colors: ## show all the colors
	@echo "${BLACK}BLACK${RESET}"
	@echo "${RED}RED${RESET}"
	@echo "${GREEN}GREEN${RESET}"
	@echo "${YELLOW}YELLOW${RESET}"
	@echo "${LIGHTPURPLE}LIGHTPURPLE${RESET}"
	@echo "${PURPLE}PURPLE${RESET}"
	@echo "${BLUE}BLUE${RESET}"
	@echo "${WHITE}WHITE${RESET}"

help:
	@grep -E '^[a-zA-Z_0-9%-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "${PURPLE}%-30s${RESET} %s\n", $$1, $$2}'