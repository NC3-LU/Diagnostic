#!/usr/bin/env bash
#
# Install apache2 web server
set -e

function main(){
    echo -e "\033[93m###############################################################################"
    echo -e "\033[93m#                             Apache2 installer                               #"
    echo -e "\033[93m#                                                                             #"
    echo -e "\033[93m###############################################################################"
    echo -e "\033[93mapache_install script initialization"

    echo -e "\033[93mInstallation of apache2\033[0m"
    sudo apt-get -qq -y install apache2
    echo -e "\033[32m###############################################################################"
    echo -e "\033[32mInstallation of apache2 done"
    echo -e "\033[32m###############################################################################"

    echo -e "\033[93mEnabling rewrite module\033[0m"
    sudo a2enmod rewrite
    sudo service apache2 reload
    echo -e "\033[32m###############################################################################"
    echo -e "\033[32mRewrite module enabled"
    echo -e "\033[32m###############################################################################"

    echo -e "\033[93m###############################################################################"
    echo -e "\033[93m#                             Apache2 installer done                          #"
    echo -e "\033[93m#                                                                             #"
    echo -e "\033[93m###############################################################################\033[0m"
}
main "$@"
