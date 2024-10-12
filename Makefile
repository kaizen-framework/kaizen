RUN=docker exec php

cs: ## Run the php cs fixer analyse
	$(RUN) vendor/bin/php-cs-fixer fix --config .php-cs-fixer.dist.php

phpstan: ## Run phpstan static analysis
	$(RUN) vendor/bin/phpstan analyse

rector: ## Run rector analysis (dry-run)
	$(RUN) vendor/bin/rector process --dry-run --config=rector.php

rector-real: ## Run rector analysis
	$(RUN) vendor/bin/rector process --config=rector.php
