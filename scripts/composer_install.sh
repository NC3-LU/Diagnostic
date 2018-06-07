#!/usr/bin/env bash
#
# Install composer software and execute the php archive in order to
# get all components specified by the application in composer.json
set -e

function main(){
    echo -e "\033[93m###############################################################################"
    echo -e "\033[93m#                              Composer installer                             #"
    echo -e "\033[93m#                                                                             #"
    echo -e "\033[93m###############################################################################"
    echo -e "\033[93mComposer installation script initialization"

    echo -e "\033[93mDownloading composer setup\033[0m"
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"

    echo -e "\033[93mInstalling composer\033[0m"
    php composer-setup.php

    echo -e "\033[93mDeleting composer installation file\033[0m"
    php -r "unlink('composer-setup.php');"

    echo -e "\033[32m###############################################################################"
    echo -e "\033[32mComposer installed successfully"
    echo -e "\033[32m###############################################################################"

    echo -e "\033[93mDiagnostic dependencies installation\033[0m"
    php composer.phar install

    echo -e "\033[93m###############################################################################"
    echo -e "\033[93m#                              Composer installer done                        #"
    echo -e "\033[93m#                                                                             #"
    echo -e "\033[93m###############################################################################\033[0m"
}

main "$@"
