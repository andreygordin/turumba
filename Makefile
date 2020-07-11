init: docker-down-clear api-clear docker-pull docker-build docker-up api-init
up: docker-up
down: docker-down
restart: down up
check: lint analyze api-validate-schema test
lint: api-lint
analyze: api-analyze
validate-schema: api-validate-schema
test: api-test
test-unit: api-test-unit
test-functional: api-test-functional

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose down -v --remove-orphans

docker-pull:
	docker-compose pull

docker-build:
	docker-compose build

api-clear:
	docker run --rm -v ${PWD}/api:/app -w /app alpine sh -c 'rm -rf var/cache/* var/log/* var/upload/*'

api-init: api-permissions api-composer-install api-wait-for-db api-migrate

api-permissions:
	docker run --rm -v ${PWD}/api:/app -w /app alpine mkdir -p {var/cache,var/log,var/upload}
	docker run --rm -v ${PWD}/api:/app -w /app alpine chmod 777 var/cache var/log var/upload

api-composer-install:
	docker-compose run --rm api-php-cli composer install

api-wait-for-db:
	docker-compose run --rm api-php-cli wait-for-it api-postgres:5432 -t 30

api-migrate:
	docker-compose run --rm api-php-cli composer app migrations:migrate

api-validate-schema:
	docker-compose run --rm api-php-cli composer app orm:validate-schema

api-lint:
	docker-compose run --rm api-php-cli composer lint
	docker-compose run --rm api-php-cli composer cs-check

api-analyze:
	docker-compose run --rm api-php-cli composer psalm

api-test:
	docker-compose run --rm api-php-cli composer test

api-test-unit:
	docker-compose run --rm api-php-cli composer test -- --testsuite=unit

api-test-functional:
	docker-compose run --rm api-php-cli composer test -- --testsuite=functional

build: build-gateway build-frontend build-api

build-gateway:
	docker --log-level=debug build --pull --file=gateway/docker/prod/nginx/Dockerfile --tag=${REGISTRY}/gateway:${IMAGE_TAG} gateway/docker/prod/nginx

build-frontend:
	docker --log-level=debug build --pull --file=frontend/docker/prod/nginx/Dockerfile --tag=${REGISTRY}/frontend:${IMAGE_TAG} frontend

build-api:
	docker --log-level=debug build --pull --file=api/docker/prod/nginx/Dockerfile --tag=${REGISTRY}/api:${IMAGE_TAG} api
	docker --log-level=debug build --pull --file=api/docker/prod/php-fpm/Dockerfile --tag=${REGISTRY}/api-php-fpm:${IMAGE_TAG} api
	docker --log-level=debug build --pull --file=api/docker/prod/php-cli/Dockerfile --tag=${REGISTRY}/api-php-cli:${IMAGE_TAG} api

try-build:
	REGISTRY=localhost IMAGE_TAG=0 make build

push: push-gateway push-frontend push-api

push-gateway:
	docker push ${REGISTRY}/gateway:${IMAGE_TAG}

push-frontend:
	docker push ${REGISTRY}/frontend:${IMAGE_TAG}

push-api:
	docker push ${REGISTRY}/api:${IMAGE_TAG}
	docker push ${REGISTRY}/api-php-fpm:${IMAGE_TAG}
	docker push ${REGISTRY}/api-php-cli:${IMAGE_TAG}

deploy:
	ssh ${HOST} -p ${PORT} 'rm -rf ${REMOTE_PATH}/site_${BUILD_NUMBER}'
	ssh ${HOST} -p ${PORT} 'mkdir ${REMOTE_PATH}/site_${BUILD_NUMBER}'
	scp -P ${PORT} docker-compose-prod.yml ${HOST}:${REMOTE_PATH}/site_${BUILD_NUMBER}/docker-compose-prod.yml
	ssh ${HOST} -p ${PORT} 'cd ${REMOTE_PATH}/site_${BUILD_NUMBER} && echo "COMPOSE_PROJECT_NAME=turumba" >> .env'
	ssh ${HOST} -p ${PORT} 'cd ${REMOTE_PATH}/site_${BUILD_NUMBER} && echo "REGISTRY=${REGISTRY}" >> .env'
	ssh ${HOST} -p ${PORT} 'cd ${REMOTE_PATH}/site_${BUILD_NUMBER} && echo "IMAGE_TAG=${IMAGE_TAG}" >> .env'
	ssh ${HOST} -p ${PORT} 'cd ${REMOTE_PATH}/site_${BUILD_NUMBER} && echo "API_DB_PASSWORD=${API_DB_PASSWORD}" >> .env'
	ssh ${HOST} -p ${PORT} 'cd ${REMOTE_PATH}/site_${BUILD_NUMBER} && docker-compose -f docker-compose-prod.yml pull'
	ssh ${HOST} -p ${PORT} 'cd ${REMOTE_PATH}/site_${BUILD_NUMBER} && docker-compose -f docker-compose-prod.yml up --build -d api-postgres'
	ssh ${HOST} -p ${PORT} 'cd ${REMOTE_PATH}/site_${BUILD_NUMBER} && docker-compose -f docker-compose-prod.yml run --rm api-php-cli wait-for-it api-postgres:5432 -t 60'
	ssh ${HOST} -p ${PORT} 'cd ${REMOTE_PATH}/site_${BUILD_NUMBER} && docker-compose -f docker-compose-prod.yml run --rm api-php-cli php bin/app.php migrations:migrate --no-interaction'
	ssh ${HOST} -p ${PORT} 'cd ${REMOTE_PATH}/site_${BUILD_NUMBER} && docker-compose -f docker-compose-prod.yml up --build --remove-orphans -d'
	ssh ${HOST} -p ${PORT} 'rm -rf ${REMOTE_PATH}/site'
	ssh ${HOST} -p ${PORT} 'ln -sr ${REMOTE_PATH}/site_${BUILD_NUMBER} ${REMOTE_PATH}/site'

rollback:
	ssh ${HOST} -p ${PORT} 'cd ${REMOTE_PATH}/site_${BUILD_NUMBER} && docker-compose -f docker-compose-prod.yml pull'
	ssh ${HOST} -p ${PORT} 'cd ${REMOTE_PATH}/site_${BUILD_NUMBER} && docker-compose -f docker-compose-prod.yml up --build --remove-orphans -d'
	ssh ${HOST} -p ${PORT} 'rm -rf ${REMOTE_PATH}/site'
	ssh ${HOST} -p ${PORT} 'ln -sr ${REMOTE_PATH}/site_${BUILD_NUMBER} ${REMOTE_PATH}/site'

run:
	docker-compose run --rm api-php-cli $(command)
