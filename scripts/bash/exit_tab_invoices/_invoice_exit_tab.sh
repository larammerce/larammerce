#!/usr/bin/env bash

PID=`ps -ef | grep '_invoice_exit_tab' | head -n1 |  awk ' {print $2;} '`;
echo "${PID}" > ${PID_FILE};

ARTISAN_EXIT_TAB_INVOICE="${ECOMMERCE_BASE_PATH}/artisan invoice:exit_tab ${NO_NOTIFY} --id="
ARTISAN_EXPORT_INVOICE="${ECOMMERCE_BASE_PATH}/artisan invoices:export --payment-status=1,2 --shipment-status=1"

echo -e "\n\nThe process ended with these errors :\n-------------------------------------" > ${TMP_DATA_PATH}/exit_tab_invoices_error.txt;
${ARTISAN_EXPORT_INVOICE} | jq -r '.[] | "\(.id)"' > ${TMP_DATA_PATH}/must_check_exit_tab_invoices.txt

while read I_ID; do
    ${ARTISAN_EXIT_TAB_INVOICE}${I_ID}
    RES_CODE=$?;
    if [[ ${RES_CODE} -eq '1' ]];then
        echo -e "id: ${I_ID}\t failed : there are no invoice with this id." >> ${TMP_DATA_PATH}/exit_tab_invoices_error.txt
    elif [[ ${RES_CODE} -eq '2' ]];then
        echo -e "id: ${I_ID}\t failed : no exit tab for this invoice yet." >> ${TMP_DATA_PATH}/exit_tab_invoices_error.txt
    fi;
done < ${TMP_DATA_PATH}/must_check_exit_tab_invoices.txt

cat ${TMP_DATA_PATH}/exit_tab_invoices_error.txt

if [[ -f ${PID_FILE} ]]; then
    rm -f ${PID_FILE}
fi