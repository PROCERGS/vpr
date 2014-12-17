@echo off
svn update
rmdir "app/cache/prod" /s /q 
rmdir "app/cache/dev" /s /q
composer install --prefer-dist --optimize-autoloader && php app/console cache:clear --env=prod --no-warmup && php app/console assets:install --env=prod && php app/console assetic:dump --env=prod --no-debug && php app/console cache:clear --env=prod --no-warmup