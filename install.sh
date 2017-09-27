#!/usr/bin/env bash
#
# Install the Diagnostic requirements and do the necessary configuration
set -e

# Custom error function
function raiseError() {
  echo -e "\033[91m[$(date +'%Y-%m-%dT%H:%M:%S%z')]: $@" >&2
}

function main() {
    clear

    echo -e "\033[93m###############################################################################"
    echo -e "\033[93m#                             Diagnostic installer                            #"
    echo -e "\033[93m###############################################################################"

    # Getting current application directory name
    readonly applicationDir=${PWD##*/}

    # Getting current user and group id
    readonly user="$(whoami)"

    # Quickly adding current user to www-data group
    sudo usermod -a -G www-data $user

    # Running installation scripts
    ./scripts/php_install.sh
    if [ "$?" -ne 0 ]; then
      raiseError "php_install script error"
    fi

    ./scripts/apache_install.sh
    if [ "$?" -ne 0 ]; then
      raiseError "apache_install script error"
    fi

    ./scripts/composer_install.sh
    if [ "$?" -ne 0 ]; then
      raiseError "composer_install script error"
    fi

    ./scripts/mysql_install.sh
    if [ "$?" -ne 0 ]; then
      raiseError "mysql_install script error"
    fi

    . ./scripts/virtual_host_install.sh
    if [ "$?" -ne 0 ]; then
      raiseError "virtual_host_install script error"
    fi

    # Setting default language
    case "${1}" in
        [fF][rR]|[fF])
            lang="fr_FR"
            ;;
        *)
            lang="en_EN"
            ;;
    esac

    key="%%LANG%%"
    replace="${lang}"
    sudo sed -i "s/${key}/${replace}/g" ../$applicationDir/module/Diagnostic/config/module.config.php

    # Setting right access and changing group for apache2
    sudo chgrp -R www-data ../$applicationDir
    sudo chmod 2750 ../$applicationDir
    sudo chmod 2770 ../$applicationDir/data
    if [ "$?" -ne 0 ]; then
      raiseError "Error while granting rights to ../$applicationDir"
    fi

    # Restarting all involved services
    sudo service apache2 restart
    sudo service mysql restart
    if [ "$?" -ne 0 ]; then
      raiseError "Error while attempting to restart services"
    fi

    echo -e "\033[93m###############################################################################"
    echo -e "\033[93m#                                  FINISHED                                   #"
    echo -e "\033[93m#            You can now access the application by typing in                  #"
    echo -e "                            \033[33mhttp://$server_name"
    echo -e "\033[93m#                        in your favorite browser.                            #"
    echo -e "\033[93m#                   \033[92mLogin : diagnostic@cases.lu                               \033[93m#"
    echo -e "\033[93m#                   \033[92mPassword : Diagnostic1!                                   \033[93m#"
    echo -e "\033[93m###############################################################################"
}

main "$@"
