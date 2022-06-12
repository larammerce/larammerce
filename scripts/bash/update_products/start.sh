#!/usr/bin/env bash
#cronjob */5 * * * *

if [[ -z "${ECOMMERCE_BASE_PATH}" ]]; then
    echo "please add ECOMMERCE_BASE_PATH environment variable";
    exit 1;
fi;

export SCRIPTS_PATH=${ECOMMERCE_BASE_PATH}/scripts/bash
source ${SCRIPTS_PATH}/_headers.sh

export UP_SCRIPTS_PATH=${SCRIPTS_PATH}/update_products
export PID_FILE=${TMP_DATA_PATH}/${FIN_MAN_DRIVER}.pid

if [ ! -d ${TMP_DATA_PATH} ]; then
    mkdir -p ${TMP_DATA_PATH}
fi;

if [ -f ${PID_FILE} ];then
    OLD_PID=`cat ${PID_FILE}`
    RESULT=`ps -ef | grep ${OLD_PID} | grep ${FIN_MAN_DRIVER}`

    if [ -n "${RESULT}" ]; then
        echo "updater ${FIN_MAN_DRIVER} is already running! Exiting"
        exit 255
    fi
fi;

if [ -f ${UP_SCRIPTS_PATH}/_${FIN_MAN_DRIVER}.sh ]; then
    ${UP_SCRIPTS_PATH}/_${FIN_MAN_DRIVER}.sh
else
    echo "the selected driver does not exist, please create the ${FIN_MAN_DRIVER}.sh first";
fi;