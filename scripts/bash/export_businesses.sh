#!/usr/bin/env bash

if [[ -z "${ECOMMERCE_BASE_PATH}" ]]; then
    echo "please add ECOMMERCE_BASE_PATH environment variable";
    exit 1;
fi;

export SCRIPTS_PATH=${ECOMMERCE_BASE_PATH}/scripts/bash
source ${SCRIPTS_PATH}/_headers.sh

ARPA_PORT=`${SCRIPTS_PATH}/_get_env.sh FIN_MAN_PORT`
ARPA_HOST=`${SCRIPTS_PATH}/_get_env.sh FIN_MAN_HOST`
ARPA_TOKEN=`${SCRIPTS_PATH}/_get_env.sh FIN_MAN_TOKEN`

curl -H "content-type: application/json" \
    -H "authorization: Bearer ${ARPA_TOKEN}" -s \
    http://${ARPA_HOST}:${ARPA_PORT}/serv/api/GetBusiness | tee ${TMP_DATA_PATH}/businesses.json \
    | jq -r '.data[] | "{\"mobile\":\"\(.mobile)\",\t\t\"name\":\"\(.businessName)\"\t\t}"' \
    | grep '"091[0-9]\{8\}"' | sed 's/.*\"mobile\"\:\"\(091[0-9]\{8\}\)\".*/\1/' \
    | sort > ${TMP_DATA_PATH}/phone_numbers.txt;

cat ${TMP_DATA_PATH}/businesses.json \
    | jq -r '.data[] | "{\"mobile\":\"\(.mobile)\",\t\t\"name\":\"\(.businessName)\"\t\t},"' \
    | grep '"mobile":' | sed 's/.*\"mobile\"\:\"\(09[0-9]\{9\}\)\".*//' | sed '/^$/d' \
    > ${TMP_DATA_PATH}/bad_phone_numbers.json;

