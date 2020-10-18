#!/usr/bin/env bash

if [[ $1 == "down" ]]
then
	docker-compose -f docker-compose.yml down
	docker-compose -f docker-compose.yml rm
else
    docker-compose -f docker-compose.yml up -d
    echo "Waiting 5 seconds for database to start"
    sleep 5
    ./dl r
fi;
