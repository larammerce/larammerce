#!/usr/bin/env bash

if [[ -z "${ECOMMERCE_BASE_PATH}" ]]; then
    echo "please add ECOMMERCE_BASE_PATH environment variable";
    exit 1;
fi;

export SCRIPTS_PATH=${ECOMMERCE_BASE_PATH}/scripts/bash
source ${SCRIPTS_PATH}/_headers.sh

ARPA_PORT=`${SCRIPTS_PATH}/_get_env.sh FIN_MAN_PORT`
ARPA_HOST=`${SCRIPTS_PATH}/_get_env.sh FIN_MAN_HOST`

TMP_TOKEN=`curl -H "content-type: application/json" -s \
    "http://${ARPA_HOST}:${ARPA_PORT}/serv/token/getApiToken" \
    | sed 's/\"//g'`

echo "New token is: ${TMP_TOKEN}"

sed -i "s/FIN_MAN_TOKEN=.*/FIN_MAN_TOKEN=${TMP_TOKEN}/g" ${ECOMMERCE_BASE_PATH}/.env