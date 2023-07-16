#!/bin/bash

check_prerequisites() {
    command -v php >/dev/null 2>&1 || { echo >&2 "I require PHP but it's not installed. Please install it and run again."; exit 1; }
    command -v composer >/dev/null 2>&1 || { echo >&2 "I require Composer but it's not installed. Please install it and run again."; exit 1; }
    command -v npm >/dev/null 2>&1 || { echo >&2 "I require npm but it's not installed. Please install it and run again."; exit 1; }

    NODE_VERSION=$(node -v | cut -d'v' -f2)
    MAJOR_VERSION=$(echo $NODE_VERSION | cut -d'.' -f1)
    if (( MAJOR_VERSION > 16 )); then
        export NODE_OPTIONS=--openssl-legacy-provider
    fi
}

update_core() {
    export ECOMMERCE_BASE_PATH=${ECOMMERCE_BASE_PATH:-""}
    core_branch="master"
    for param in "$@"
    do
        case $param in
            --core-path=*)
            export ECOMMERCE_BASE_PATH="${param#*=}"
            shift
            ;;
            --core-repo=*)
            core_repo="${param#*=}"
            shift
            ;;
            --core-branch=*)
            core_branch="${param#*=}"
            shift
            ;;
        esac
    done

    if [[ -z "${ECOMMERCE_BASE_PATH}" ]]; then
        echo "Please consider setting ECOMMERCE_BASE_PATH env variable or pass the base path using --core-path parameter"
        exit 1
    fi

    if [[ ! -d "${ECOMMERCE_BASE_PATH}/.git" ]]; then
        git -C "${ECOMMERCE_BASE_PATH}" init
    fi

    git -C "${ECOMMERCE_BASE_PATH}" remote set-url origin "${core_repo}"
    git -C "${ECOMMERCE_BASE_PATH}" fetch
    git -C "${ECOMMERCE_BASE_PATH}" reset --hard origin/"${core_branch}"
    composer install --working-dir="${ECOMMERCE_BASE_PATH}"
    npm --prefix "${ECOMMERCE_BASE_PATH}" install "${ECOMMERCE_BASE_PATH}"
    php "${ECOMMERCE_BASE_PATH}/artisan" migrate --force
    npm --prefix "${ECOMMERCE_BASE_PATH}" run production
    echo "Core Update successfully done!"
}

update_theme() {
    THEME_BASE_PATH=${THEME_BASE_PATH:-"$ECOMMERCE_BASE_PATH/data/themes/default"}
    theme_branch="master"
    for param in "$@"
    do
        case $param in
            --theme-path=*)
            THEME_BASE_PATH="${param#*=}"
            shift
            ;;
            --theme-repo=*)
            theme_repo="${param#*=}"
            shift
            ;;
            --theme-branch=*)
            theme_branch="${param#*=}"
            shift
            ;;
        esac
    done

    if [[ -z "${THEME_BASE_PATH}" ]]; then
        echo "Please consider setting ECOMMERCE_BASE_PATH env variable or pass the base path using --core-path parameter"
        exit 1
    fi

    if [[ ! -d "${THEME_BASE_PATH}" ]]; then
        git clone "${theme_repo}" "${THEME_BASE_PATH}"
        git -C "${THEME_BASE_PATH}" checkout "${theme_branch}"
    elif [[ ! -d "${THEME_BASE_PATH}/.git" ]]; then
        git -C "${THEME_BASE_PATH}" init
    fi

    git -C "${THEME_BASE_PATH}" remote set-url origin "${theme_repo}"
    git -C "${THEME_BASE_PATH}" fetch
    git -C "${THEME_BASE_PATH}" reset --hard origin/"${theme_branch}"
    npm --prefix "${THEME_BASE_PATH}" install "${THEME_BASE_PATH}"
    npm --prefix "${THEME_BASE_PATH}" run production

    if [[ ! -f "${THEME_BASE_PATH}/deploy.sh" ]]; then
        echo "There is no deploy.sh script in the theme project"
        exit 1
    fi

    cd "${THEME_BASE_PATH}" && bash "${THEME_BASE_PATH}/deploy.sh"
    cd "${ECOMMERCE_BASE_PATH}" || exit
    echo "Theme Update successfully done!"
}

check_prerequisites

# Check if no arguments were provided, if so, update both core and theme
if [[ $# -eq 0 ]] ; then
    echo "Please provide parameters for --only-core or --only-theme"
    exit 0
fi

# Parse the input parameters
only_core=0
only_theme=0

for param in "$@"
do
    case $param in
        --only-core*)
        only_core=1
        shift
        ;;
        --only-theme*)
        only_theme=1
        shift
        ;;
    esac
done

if ((only_core)); then
    update_core "$@"
elif ((only_theme)); then
    update_theme "$@"
else
    update_core "$@"
    update_theme "$@"
fi
