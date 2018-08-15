docker-up: memory
  docker-compose up -d

docker-down:
  docker-compose down

docker-build: memory
  docker-compose up --build -d

test:
  docker-compose exec php-cli vendor/bin/phpunit

assets-install:
  docker-compose exec node yarn install

assets-rebuild:
  docker-compose exec node npm rebuild node-sass --force

assets-dev:
  docker-compose exec node yarn run dev

assets-watch:
  docker-compose exec node yarn run watch

queue:
  docker-compose exec php-cli php artisan queue:work

memory:
  sudo sysctl -w vm.max_map_count=262144
