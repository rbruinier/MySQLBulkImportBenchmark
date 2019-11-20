#!/bin/bash
docker-compose rm -f
docker-compose build
docker-compose run start_dependencies
docker-compose run scripts
docker-compose stop
