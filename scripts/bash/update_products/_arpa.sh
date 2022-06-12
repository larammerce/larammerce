#!/usr/bin/env bash

echo "Arpa Driver : started fetching products data ...";

ARPA_DATA_PATH=${DATA_PATH}/arpa;
ARPA_PRODUCTS_NEW=${ARPA_DATA_PATH}/products_new.txt
ARPA_PRODUCTS_OLD=${ARPA_DATA_PATH}/products_old.txt
ARPA_PRODUCTS_DIF=${ARPA_DATA_PATH}/products_dif.txt
ARPA_PRODUCTS_ERR=${ARPA_DATA_PATH}/products_err.txt

if [ ! -d ${ARPA_DATA_PATH} ]; then
    mkdir -p ${ARPA_DATA_PATH}
fi;

echo -e "\n\nThe process ended with these errors :\n-------------------------------------" > ${ARPA_PRODUCTS_ERR};

ARTISAN_UPDATE_PRODUCT="${ECOMMERCE_BASE_PATH}/artisan products:update-stock"
ARTISAN_EXPORT_PRODUCT="${ECOMMERCE_BASE_PATH}/artisan products:export --type=json --cols=code,count,latest_price"

if [ ! -d ${ARPA_DATA_PATH} ];then
    mkdir -p ${ARPA_DATA_PATH};
fi;

#grab pid of this process and update the pid file with it
PID=`ps -ef | grep ${FIN_MAN_DRIVER} | head -n1 |  awk ' {print $2;} '`;
echo "${PID}" > ${PID_FILE};

ARPA_HOST=`${SCRIPTS_PATH}/_get_env.sh FIN_MAN_HOST`;
ARPA_PORT=`${SCRIPTS_PATH}/_get_env.sh FIN_MAN_PORT`
ARPA_TOKEN=`${SCRIPTS_PATH}/_get_env.sh FIN_MAN_TOKEN`;

${ARTISAN_EXPORT_PRODUCT} \
    | jq -r '.[] | "{\"code\":\"\(.code)\", \"count\":\"\(.count)\", \"price\":\"\(.fin_man_price)\"}"' \
    | sort > ${ARPA_PRODUCTS_OLD};

timeout 60s curl -H "content-type: application/json" \
    -H "authorization: Bearer ${ARPA_TOKEN}" -s \
    http://${ARPA_HOST}:${ARPA_PORT}/serv/api/GetItem \
    | jq -r '.data[] | "{\"code\":\"\(.itemCode)\", \"count\":\"\(.qty)\", \"price\":\"\(.salePrice)\"}"' \
    | sort > ${ARPA_PRODUCTS_NEW};

if [ -s ${ARPA_PRODUCTS_NEW} ];then
    if [ ! -f ${ARPA_PRODUCTS_OLD} ];then
        touch ${ARPA_PRODUCTS_OLD}
    fi;

    comm -23 ${ARPA_PRODUCTS_OLD} ${ARPA_PRODUCTS_NEW} | jq -r '.code' \
        | uniq > ${ARPA_PRODUCTS_DIF};

    while read P_CODE; do
        ${ARTISAN_UPDATE_PRODUCT} --code="${P_CODE}"
        RES_CODE=$?;
        if [ ${RES_CODE} -eq '1' ];then
            echo -e "code: ${P_CODE}\t failed : there are no product with this code." >> ${ARPA_PRODUCTS_ERR};
        elif [ ${RES_CODE} -eq '2' ];then
            echo -e "code: ${P_CODE}\t failed : there are more than one product with this code." >> ${ARPA_PRODUCTS_ERR};
        elif [ ${RES_CODE} -eq '3' ];then
            echo -e "code: ${P_CODE}\t failed : there were error in fetching data from fin server or saving it on local database." >> ${ARPA_PRODUCTS_ERR};
        fi;

    done <${ARPA_PRODUCTS_DIF}

    cat ${ARPA_PRODUCTS_ERR};
fi;

if [ -f ${PID_FILE} ]; then
    rm -f ${PID_FILE}
fi

