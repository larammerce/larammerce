#!/usr/bin/env bash

if [[ -z "${ECOMMERCE_BASE_PATH}" ]]; then
    echo "please add ECOMMERCE_BASE_PATH environment variable";
    exit 1;
fi;

export SCRIPTS_PATH=${ECOMMERCE_BASE_PATH}/scripts/bash
source ${SCRIPTS_PATH}/_headers.sh

ARPA_HOST=`${SCRIPTS_PATH}/_get_env.sh FIN_MAN_HOST`;
ARPA_PORT=`${SCRIPTS_PATH}/_get_env.sh FIN_MAN_PORT`
ARPA_TOKEN=`${SCRIPTS_PATH}/_get_env.sh FIN_MAN_TOKEN`;

ARTISAN_EXPORT_PRODUCT="${ECOMMERCE_BASE_PATH}/artisan products:export --type=json --cols=code,title"

${ARTISAN_EXPORT_PRODUCT} \
    | jq -r '.[] | "{\"code\":\"\(.code)\", \"kitline_title\":\"\(.title)\""' \
    | tee -a ${TMP_DATA_PATH}/items_1.json \
    | sed 's/.*"code"\:"\(.*\)", "kitline_title".*"/\1/' \
    | xargs -I '{}' curl -H "content-type: application/json" \
        -H "authorization: Bearer ${ARPA_TOKEN}" -s \
        http://${ARPA_HOST}:${ARPA_PORT}/serv/api/GetItem?ItemCode={} \
    | jq -r '.data[] | ", \"arpa_title\":\"\(.itemName)\", \"arpa_price\": \"\(.salePrice)\" },"' \
    >> ${TMP_DATA_PATH}/items_2.json

paste ${TMP_DATA_PATH}/items_1.json ${TMP_DATA_PATH}/items_2.json > ${TMP_DATA_PATH}/items.json
