#!/usr/bin/env bash

if [[ -z "${ECOMMERCE_BASE_PATH}" ]]; then
    echo "please add ECOMMERCE_BASE_PATH environment variable";
    exit 1;
fi;

export SCRIPTS_PATH=${ECOMMERCE_BASE_PATH}/scripts/bash
source ${SCRIPTS_PATH}/_headers.sh
TMP_DATA=${ECOMMERCE_BASE_PATH}/data/tmp

ARPA_HOST=`${SCRIPTS_PATH}/_get_env.sh FIN_MAN_HOST`;
ARPA_PORT=`${SCRIPTS_PATH}/_get_env.sh FIN_MAN_PORT`
ARPA_TOKEN=`${SCRIPTS_PATH}/_get_env.sh FIN_MAN_TOKEN`;

curl -H "content-type: application/json" \
    -H "authorization: Bearer ${ARPA_TOKEN}" -s \
    http://${ARPA_HOST}:${ARPA_PORT}/serv/api/GetCity \
    | jq -r '.data[] | "\(.cityID) : \"cityID\": \"\(.cityID)\", \"cityName\": \"\(.cityName)\", \"provinceID\": \"\(.provinceID)\", \"provinceName\": \"\(.provinceName)\""' \
    | sort -n
