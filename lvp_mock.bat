@echo off
rmdir "app/cache/prod" /s /q
rmdir "app/cache/dev" /s /q
svn update
composer install --prefer-dist --optimize-autoloader && php app/console assetic:dump --env=prod --no-debug && php app/console cache:clear --env=prod --no-warmup