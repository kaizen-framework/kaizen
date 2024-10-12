RUN=docker exec php

cs: ## Run the php cs fixer analyse
	$(RUN) ./tools/php-cs-fixer/vendor/bin/php-cs-fixer fix --config .php-cs-fixer.dist.php

phpstan: ## Run phpstan static analysis
	$(RUN) vendor/bin/phpstan analyse