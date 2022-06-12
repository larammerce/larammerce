#!/usr/bin/env bash

PID=`ps -ef | grep '_invoice_disabler' | head -n1 |  awk ' {print $2;} '`;
echo "${PID}" > ${PID_FILE};

ARTISAN_DISABLE_INVOICE="${ECOMMERCE_BASE_PATH}/artisan invoices:disable --id="
ARTISAN_EXPORT_INVOICE="${ECOMMERCE_BASE_PATH}/artisan invoices:export --payment-status='0,4,5,6' --time-diff='10'"

echo -e "\n\nThe process ended with these errors :\n-------------------------------------" > ${TMP_DATA_PATH}/disable_invoices_error.txt;

${ARTISAN_EXPORT_INVOICE} | jq -r '.[] | "\(.id)"' > ${TMP_DATA_PATH}/must_disabled_invoices.txt

while read I_ID; do
    ${ARTISAN_DISABLE_INVOICE}${I_ID}
    RES_CODE=$?;
    if [ ${RES_CODE} -eq '1' ];then
        echo -e "id: ${I_ID}\t failed : there are no invoice with this id." >> ${TMP_DATA_PATH}/disable_invoices_error.txt
    elif [ ${RES_CODE} -eq '2' ];then
        echo -e "id: ${I_ID}\t failed : fin man deletion failed." >> ${TMP_DATA_PATH}/disable_invoices_error.txt
    fi;
done < ${TMP_DATA_PATH}/must_disabled_invoices.txt

cat ${TMP_DATA_PATH}/disable_invoices_error.txt

if [ -f ${PID_FILE} ]; then
    rm -f ${PID_FILE}
fi