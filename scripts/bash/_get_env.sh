#!/usr/bin/env bash

if [[ -z "${ECOMMERCE_BASE_PATH}" ]]; then
    echo "please add ECOMMERCE_BASE_PATH environment variable";
    exit 1;
fi;

cat ${ECOMMERCE_BASE_PATH}/.env | grep "${1}" | sed s/${1}\=//