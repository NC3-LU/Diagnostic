#!/usr/bin/env bash
#
# Setup root account on a fresh install of mysql-server
# Add a diagnostic database user and prompt the user for a password
# Edit some application's configuration files according to what the user typed
# in order to establish a proper connection to the db
set -e

function main(){
    echo -e "\033[93m###############################################################################"
    echo -e "\033[93m#                             MySQL Server installer                          #"
    echo -e "\033[93m#                                                                             #"
    echo -e "\033[93m###############################################################################"

    readonly applicationDir=${PWD##*/}

    echo -e "\033[94m"
    read -s -p "Root user of the database has no password yet. Please, set one > " password
    echo -e "\033[0m"

    echo -e "\033[94m"
    read -s -p "Repeat password > " password_repeat
    echo -e "\033[0m"

    while [ $password != $password_repeat ]
    do
    echo -e "\033[94mThe passwords do not match. Try again please"
    echo -e "\033[94m"
    read -s -p "Enter root user password > " password
    echo -e "\033[0m"

    echo -e "\033[94m"
    read -s -p "Repeat password > " password_repeat
    echo -e "\033[0m"
    done

    echo "mysql-server mysql-server/root_password password $password" | sudo debconf-set-selections
    echo "mysql-server mysql-server/root_password_again password $password" | sudo debconf-set-selections

    echo -e "\033[93mMySQL Server installation\033[0m"
    sudo apt-get -qq install mysql-server
    echo -e "\033[32m###############################################################################"
    echo -e "\033[32mMySQL Server installation done"
    echo -e "\033[32m###############################################################################"

    echo -e "\033[93mMySQL Server secure configuration\033[0m"

    sudo mysql --defaults-file=/etc/mysql/debian.cnf -e "UPDATE mysql.user SET authentication_string=PASSWORD('$password') WHERE User='root'"
    sudo mysql --defaults-file=/etc/mysql/debian.cnf -e "flush privileges"
    mysql -u root -p"$password" -e "DELETE FROM mysql.user WHERE User='root' AND Host NOT IN ('localhost', '127.0.0.1', '::1')"
    mysql -u root -p"$password" -e "DELETE FROM mysql.user WHERE User=''"
    mysql -u root -p"$password" -e "DELETE FROM mysql.db WHERE Db='test' OR Db='test\_%'"
    mysql -u root -p"$password" -e "FLUSH PRIVILEGES"
    echo -e "\033[32m###############################################################################"
    echo -e "\033[32mMySQL Server secured"
    echo -e "\033[32m###############################################################################"

    echo -e "\033[93mDiagnostic database creation\033[0m"
    mysql -u root -p"$password" -e "source ./scripts/db_initialization.sql"

    echo -e "\033[93mDiagnostic db user & role creation\033[0m"
    echo -e "\033[94m"
    read -s -p "A specific database user for the diagnostic application will be created. Choose the password for this user >" dbuser_password
    echo -e "\033[0m"
    mysql -u root -p"$password" -e "CREATE USER 'diagnostic'@'localhost' IDENTIFIED BY '$dbuser_password'"
    mysql -u root -p"$password" -e "GRANT SELECT, UPDATE, INSERT, DELETE, EXECUTE on diagnostic.* to 'diagnostic'@'localhost'"

    declare -A global_configurationArray
    global_configurationArray=(
        ["%%DB_NAME%%"]="diagnostic"
        ["%%DB_HOST%%"]="localhost"
    )

    declare -A local_configurationArray
    local_configurationArray=(
        ["%%DB_USER%%"]="diagnostic"
        ["%%DB_PASSWORD%%"]=$dbuser_password
    )

    cp ../$applicationDir/config/autoload/global.php.dist ../$applicationDir/config/autoload/global.php

    global_configure() {
        # Loop the global config array
        for i in "${!global_configurationArray[@]}"
        do
            search=$i
            replace=${global_configurationArray[$i]}
            sudo sed -i "s/${search}/${replace}/g" ../$applicationDir/config/autoload/global.php
        done
    }
    global_configure

    cp ../$applicationDir/config/autoload/local.php.dist ../$applicationDir/config/autoload/local.php

    local_configure() {
        # Loop the local config array
        for i in "${!local_configurationArray[@]}"
        do
            search=$i
            replace=${local_configurationArray[$i]}
            sudo sed -i "s/${search}/${replace}/g" ../$applicationDir/config/autoload/local.php
        done
    }
    local_configure

    echo -e "\033[32m###############################################################################"
    echo -e "\033[32mConfiguration files edited"
    echo -e "\033[32m###############################################################################"
    echo ""
    echo ""
    echo -e "\033[93m###############################################################################"
    echo -e "\033[93m#                          MySQL installer done                               #"
    echo -e "\033[93m#                                                                             #"
    echo -e "\033[93m###############################################################################\033[0m"
}
main "$@"
