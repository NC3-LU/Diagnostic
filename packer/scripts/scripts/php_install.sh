#!/usr/bin/env bash
#
# Install php 7.1 and other required modules/extensions
set -e

function main(){
    echo -e "\033[93m###############################################################################"
    echo -e "\033[93m#                                 PHP installer                               #"
    echo -e "\033[93m#                                                                             #"
    echo -e "\033[93m###############################################################################"

    echo -e "\033[93mphp_install script initialization"

    echo -e "\033[93mInstallation of php 7.1\033[0m"
    sudo apt-get -qq install php7.1
    echo -e "\033[32m###############################################################################"
    echo -e "\033[32mInstallation of php 7.1 done"
    echo -e "\033[32m###############################################################################"

    echo -e "\033[93mInstallation of libapache2-mod-php7.1\033[0m"
    sudo apt-get -qq install libapache2-mod-php7.1
    echo -e "\033[32m###############################################################################"
    echo -e "\033[32mInstallation of libapache2-mod-php7.1 done"
    echo -e "\033[32m###############################################################################"

    echo -e "\033[93mInstallation of php7.0-mcrypt\033[0m"
    sudo apt-get -qq install php7.1-mcrypt
    echo -e "\033[32m###############################################################################"
    echo -e "\033[32mInstallation of php7.1-mcrypt done"
    echo -e "\033[32m###############################################################################"

    echo -e "\033[93mInstallation of php7.1-mysql\033[0m"
    sudo apt-get -qq install php7.1-mysql
    echo -e "\033[32m###############################################################################"
    echo -e "\033[32mInstallation of php7.1-mysql done"
    echo -e "\033[32m###############################################################################"

    echo -e "\033[93mInstallation of php7.1-zip\033[0m"
    sudo apt-get -qq install php7.1-zip
    echo -e "\033[32m###############################################################################"
    echo -e "\033[32mInstallation of php7.1-zip done"
    echo -e "\033[32m###############################################################################"

    echo -e "\033[93mInstallation of php-xml\033[0m"
    sudo apt-get -qq install php-xml
    echo -e "\033[32m###############################################################################"
    echo -e "\033[32mInstallation of php-xml done"
    echo -e "\033[32m###############################################################################"
    echo ""

    echo -e "\033[93m###############################################################################"
    echo -e "\033[93m#                            PHP installer done                               #"
    echo -e "\033[93m#                                                                             #"
    echo -e "\033[93m###############################################################################\033[0m"
}
main "$@"
