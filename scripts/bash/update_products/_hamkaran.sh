#!/usr/bin/env bash

echo "Hamkaran Driver : started fetching products data ...";

HAMKARAN_DATA_PATH=${DATA_PATH}/hamkaran;
HAMKARAN_PRODUCTS=${HAMKARAN_DATA_PATH}/products.txt;
HAMKARAN_PRODUCTS_ERR=${HAMKARAN_DATA_PATH}/products_err.txt

if [ ! -d ${HAMKARAN_DATA_PATH} ]; then
    mkdir -p ${HAMKARAN_DATA_PATH}
fi;

echo -e "\n\nThe process ended with these errors :\n-------------------------------------" > ${HAMKARAN_PRODUCTS_ERR};

ARTISAN_UPDATE_PRODUCT="${ECOMMERCE_BASE_PATH}/artisan products:update-stock"
ARTISAN_EXPORT_PRODUCT="${ECOMMERCE_BASE_PATH}/artisan products:export --type=json --cols=code,count,latest_price"

if [ ! -d ${HAMKARAN_DATA_PATH} ];then
    mkdir -p ${HAMKARAN_DATA_PATH};
fi;

#grab pid of this process and update the pid file with it
PID=`ps -ef | grep ${FIN_MAN_DRIVER} | head -n1 |  awk ' {print $2;} '`;
echo "${PID}" > ${PID_FILE};

${ARTISAN_EXPORT_PRODUCT} \
    | jq -r '.[] |.code' \
    | sort > ${HAMKARAN_PRODUCTS};

while read P_CODE; do
    ${ARTISAN_UPDATE_PRODUCT} --code="${P_CODE}"
    RES_CODE=$?;
    if [ ${RES_CODE} -eq '1' ];then
        echo -e "code: ${P_CODE}\t failed : there are no product with this code." >> ${HAMKARAN_PRODUCTS_ERR};
    elif [ ${RES_CODE} -eq '2' ];then
        echo -e "code: ${P_CODE}\t failed : there are more than one product with this code." >> ${HAMKARAN_PRODUCTS_ERR};
    elif [ ${RES_CODE} -eq '3' ];then
        echo -e "code: ${P_CODE}\t failed : there were error in fetching data from fin server or saving it on local database." >> ${HAMKARAN_PRODUCTS_ERR};
    fi;
done <${HAMKARAN_PRODUCTS}
cat ${HAMKARAN_PRODUCTS_ERR};

if [ -f ${PID_FILE} ]; then
    rm -f ${PID_FILE}
fi
