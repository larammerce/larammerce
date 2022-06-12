#!/usr/bin/env bash
#cronjob * * * * *

if [[ -z "${ECOMMERCE_BASE_PATH}" ]]; then
    echo "please add ECOMMERCE_BASE_PATH environment variable";
    exit 1;
fi;

NO_NOTIFY=""

while [[ ! $# -eq 0 ]]
do
	case "$1" in
		--help | -h )
			echo "Usage: ./start.sh [--no-notify]"
			exit
			;;
		--no-notify | -n)
			NO_NOTIFY="--no-notify"
			;;
        *)
			echo "Unrecognized option: $1"
			echo "Help: ./start.sh -h"
			exit
			;;
	esac
	shift
done

export NO_NOTIFY

export SCRIPTS_PATH=${ECOMMERCE_BASE_PATH}/scripts/bash
source ${SCRIPTS_PATH}/_headers.sh

export EXIT_TAB_SCRIPTS_PATH=${SCRIPTS_PATH}/exit_tab_invoices
export PID_FILE=${TMP_DATA_PATH}/invoice_exit_tab.pid

if [[ ! -d ${TMP_DATA_PATH} ]]; then
    mkdir -p ${TMP_DATA_PATH}
fi;

if [[ -f ${PID_FILE} ]]; then
    OLD_PID=`cat ${PID_FILE}`
    RESULT=`ps -ef | grep ${OLD_PID} | grep '_invoice_exit_tab'`

    if [[ -n "${RESULT}" ]]; then
        echo "updater invoice_exit_tab is already running! Exiting"
        exit 255
    fi
fi;

if [[ -f ${EXIT_TAB_SCRIPTS_PATH}/_invoice_exit_tab.sh ]]; then
    ${EXIT_TAB_SCRIPTS_PATH}/_invoice_exit_tab.sh
else
    echo "the selected driver does not exist, please create the _invoice_exit_tab.sh first";
fi;