#!/usr/bin/env bash
#cronjob * * * * *

if [[ -z "${ECOMMERCE_BASE_PATH}" ]]; then
    echo "please add ECOMMERCE_BASE_PATH environment variable";
    exit 1;
fi;

export SCRIPTS_PATH=${ECOMMERCE_BASE_PATH}/scripts/bash
source ${SCRIPTS_PATH}/_headers.sh

export DIS_SCRIPTS_PATH=${SCRIPTS_PATH}/disable_invoices
export PID_FILE=${TMP_DATA_PATH}/invoice_disabler.pid

if [ ! -d ${TMP_DATA_PATH} ]; then
    mkdir -p ${TMP_DATA_PATH}
fi;

if [ -f ${PID_FILE} ];then
    OLD_PID=`cat ${PID_FILE}`
    RESULT=`ps -ef | grep ${OLD_PID} | grep '_invoice_disabler'`

    if [ -n "${RESULT}" ]; then
        echo "updater invoice_disabler is already running! Exiting"
        exit 255
    fi
fi;

if [ -f ${DIS_SCRIPTS_PATH}/_invoice_disabler.sh ]; then
    ${DIS_SCRIPTS_PATH}/_invoice_disabler.sh
else
    echo "the selected driver does not exist, please create the _invoice_disabler.sh first";
fi;