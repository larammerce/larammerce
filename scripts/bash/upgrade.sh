#!/bin/bash

export UPGRADE_LOG="${ECOMMERCE_BASE_PATH}/storage/logs/upgrade.log"
echo "Upgrade log ..." >"${UPGRADE_LOG}"
{
  echo "PHP: $(php -v)"
  echo "NODE: $(node -v)"
  echo "NPM: $(npm -v)"
  echo "COMPOSER: $(composer -V)"
} >>"${UPGRADE_LOG}"

check_prerequisites() {
  command -v php >>"${UPGRADE_LOG}" 2>&1 || {
    echo >&2 "I require PHP but it's not installed. Please install it and run again."
    exit 1
  }
  command -v composer >>"${UPGRADE_LOG}" 2>&1 || {
    echo >&2 "I require Composer but it's not installed. Please install it and run again."
    exit 1
  }
  command -v npm >>"${UPGRADE_LOG}" 2>&1 || {
    echo >&2 "I require npm but it's not installed. Please install it and run again."
    exit 1
  }

  NODE_VERSION=$(node -v | cut -d'v' -f2)
  MAJOR_VERSION=$(echo $NODE_VERSION | cut -d'.' -f1)
  if ((MAJOR_VERSION > 16)); then
    export NODE_OPTIONS=--openssl-legacy-provider
  fi
}

update_core() {
  export ECOMMERCE_BASE_PATH=${ECOMMERCE_BASE_PATH:-""}
  core_branch="master"
  core_repo=""

  for param in "$@"; do
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

  if [[ ! -z "${core_repo}" && ! -d "${ECOMMERCE_BASE_PATH}/.git" ]]; then
    echo "Initializing git..."
    git -C "${ECOMMERCE_BASE_PATH}" init || {
      echo "Failed initializing git. Please check if the directory exists and you have the necessary permissions."
      exit 1
    }
  fi

  if [[ ! -z "${core_repo}" ]]; then
    echo "Updating git remote origin..."
    git -C "${ECOMMERCE_BASE_PATH}" remote set-url origin "${core_repo}" || {
      echo "Failed updating git remote origin. Please check the repository URL."
      exit 1
    }
    echo "Updating git remote origin...done!"

    echo "Fetching and resetting to the latest changes on branch ${core_branch}..."
    git -C "${ECOMMERCE_BASE_PATH}" fetch || {
      echo "Failed fetching from git. Please check your network connection."
      exit 1
    }
    git -C "${ECOMMERCE_BASE_PATH}" reset --hard origin/"${core_branch}" || {
      echo "Failed resetting to branch ${core_branch}. Please check if the branch exists."
      exit 1
    }
    echo "Fetching and resetting to the latest changes on branch ${core_branch}...done!"
  fi

  echo "Installing composer packages..."
  composer install --working-dir="${ECOMMERCE_BASE_PATH}" >>"${UPGRADE_LOG}" 2>&1 || {
    echo "Failed installing composer packages. Please check your composer.json file."
    exit 1
  }
  echo "Installing composer packages...done!"

  echo "Installing npm packages..."
  npm --prefix "${ECOMMERCE_BASE_PATH}" install "${ECOMMERCE_BASE_PATH}" >>"${UPGRADE_LOG}" 2>&1 || {
    echo "Failed installing npm packages. Please check your package.json file."
    exit 1
  }
  echo "Installing npm packages...done!"

  echo "Running database migrations..."
  php "${ECOMMERCE_BASE_PATH}/artisan" migrate --force || {
    echo "Failed running migrations. Please check your database connection and migration files."
    exit 1
  }
  echo "Running database migrations...done!"

  echo "Running npm production build..."
  npm --prefix "${ECOMMERCE_BASE_PATH}" run production >>"${UPGRADE_LOG}" 2>&1 || {
    echo "Failed running npm production build. Please check your scripts in package.json."
    exit 1
  }
  echo "Running npm production build...done!"

  echo "Core Update successfully done!"
}

update_theme() {
  THEME_BASE_PATH=${THEME_BASE_PATH:-"$ECOMMERCE_BASE_PATH/data/themes/default"}
  theme_branch="master"
  theme_repo=""

  for param in "$@"; do
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

  if [[ ! -z "${theme_repo}" && (! -d "${THEME_BASE_PATH}" || -z "$(ls -A "${THEME_BASE_PATH}")") ]]; then
    echo "Cloning theme from repo ${theme_repo}..."
    git clone "${theme_repo}" "${THEME_BASE_PATH}" || {
      echo "Failed cloning theme from repo ${theme_repo}. Please check the repository URL."
      exit 1
    }
    git -C "${THEME_BASE_PATH}" checkout "${theme_branch}" || {
      echo "Failed checking out branch ${theme_branch}. Please check if the branch exists."
      exit 1
    }
    echo "Cloning theme from repo ${theme_repo}...done!"
  elif [[ ! -z "${theme_repo}" && ! -d "${THEME_BASE_PATH}/.git" ]]; then
    echo "Initializing git..."
    git -C "${THEME_BASE_PATH}" init || {
      echo "Failed initializing git. Please check if the directory exists and you have the necessary permissions."
      exit 1
    }
  fi

  if [[ ! -z "${theme_repo}" ]]; then
    echo "Updating git remote origin..."
    git -C "${THEME_BASE_PATH}" remote set-url origin "${theme_repo}" || {
      echo "Failed updating git remote origin. Please check the repository URL."
      exit 1
    }
    echo "Updating git remote origin...done!"

    echo "Fetching and resetting to the latest changes on branch ${theme_branch}..."
    git -C "${THEME_BASE_PATH}" fetch || {
      echo "Failed fetching from git. Please check your network connection."
      exit 1
    }
    git -C "${THEME_BASE_PATH}" reset --hard origin/"${theme_branch}" || {
      echo "Failed resetting to branch ${theme_branch}. Please check if the branch exists."
      exit 1
    }
    echo "Fetching and resetting to the latest changes on branch ${theme_branch}...done!"
  fi

  echo "Installing npm packages..."
  npm --prefix "${THEME_BASE_PATH}" install "${THEME_BASE_PATH}" >>"${UPGRADE_LOG}" 2>&1 || {
    echo "Failed installing npm packages. Please check your package.json file."
    exit 1
  }
  echo "Installing npm packages...done!"

  echo "Running npm production build..."
  npm --prefix "${THEME_BASE_PATH}" run production >>"${UPGRADE_LOG}" 2>&1 || {
    echo "Failed running npm production build. Please check your scripts in package.json."
    exit 1
  }
  echo "Running npm production build...done!"

  if [[ ! -f "${THEME_BASE_PATH}/deploy.sh" ]]; then
    echo "There is no deploy.sh script in the theme project"
    exit 1
  fi

  echo "Running theme deploy script..."
  pushd "${THEME_BASE_PATH}" >>"${UPGRADE_LOG}" || {
    echo "Failed changing directory to ${THEME_BASE_PATH}. Please check if the directory exists."
    exit 1
  }
  bash deploy.sh >>"${UPGRADE_LOG}" 2>&1 || {
    echo "Failed running theme deploy script. Please check your deploy.sh."
    popd >>"${UPGRADE_LOG}"
    exit 1
  }
  popd >>"${UPGRADE_LOG}" || {
    echo "Failed returning to original directory. Please check your filesystem."
    exit 1
  }
  echo "Running theme deploy script...done!"

  echo "Theme Update successfully done!"
}

check_prerequisites

# Parse the input parameters
only_core=0
only_theme=0

for param in "$@"; do
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
