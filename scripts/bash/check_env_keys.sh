#!/usr/bin/env bash

if [[ -z "${ECOMMERCE_BASE_PATH}" ]]; then
    echo "please add ECOMMERCE_BASE_PATH environment variable"
    exit 1
fi

export SCRIPTS_PATH=${ECOMMERCE_BASE_PATH}/scripts/bash
source ${SCRIPTS_PATH}/_headers.sh

ENV_FILE=${ECOMMERCE_BASE_PATH}/.env
ENV_EXAMPLE_FILE=${ECOMMERCE_BASE_PATH}/.env.example
printf "\n**********************************\n"
printf "* You should delete these items: *\n"
printf "**********************************\n"
comm -13 <(cat ${ENV_EXAMPLE_FILE} | sed '/^$/d' | sed '/^#.*/d' | sed -E 's/([A-Z_]+)(\=.*)+/\1/' | sort) \
    <(cat ${ENV_FILE} | sed '/^$/d' | sed '/^#.*/d' | sed -E 's/([A-Z_]+)(\=.*)+/\1/' | sort)

printf "\n**********************************\n"
printf "*  You should add these items:   *\n"
printf "**********************************\n"
comm -23 <(cat ${ENV_EXAMPLE_FILE} | sed '/^$/d' | sed '/^#.*/d' | sed -E 's/([A-Z_]+)(\=.*)+/\1/' | sort) \
    <(cat ${ENV_FILE} | sed '/^$/d' | sed '/^#.*/d' | sed -E 's/([A-Z_]+)(\=.*)+/\1/' | sort)
