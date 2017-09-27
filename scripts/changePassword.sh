#!/usr/bin/env bash
#
# Change user password for Diagnostic's application user

sqlPassword=diagnosticpass
user="${1:-}"

echo -e "\033[93m###############################################################################"
echo -e "\033[93m#                             Password Changer                                #"
echo -e "\033[93m###############################################################################"

echo -e "\033[94m Connecting to database..."
mysql -u diagnostic -p"${sqlPassword}" -e "USE diagnostic;"
if [ "$?" -eq 0 ]; then
    echo -e "\033[94m Connection established"

    echo -e "\033[94m"
    read -s -p "Please, set a new password for ${user} > " password
    echo -e "\033[0m"

    passwordLength=${#password}

    # Testing the password
    if [[ $password =~ .*\d+.* ]]; then
        echo "We got a digit"
    else
        echo "No digit in $password"
    fi


    echo -e "\033[94m"
    read -s -p "Repeat password > " password_repeat
    echo -e "\033[0m"

    while [ $password != $password_repeat ]
    do
    echo -e "\033[94mThe passwords do not match. Try again please"
    echo -e "\033[94m"
    read -s -p "Enter ${user} password > " password
    echo -e "\033[0m"

    echo -e "\033[94m"
    read -s -p "Repeat password > " password_repeat
    echo -e "\033[0m"
    done


    hash_password=$(php -r 'echo password_hash(${password}, PASSWORD_DEFAULT);')
    echo "The password hash is : ${hash_password}"
    # mysql -h localhost -u diagnostic -p"${sqlPassword}" --database=diagnostic -e "UPDATE users SET password='${hash_password}' WHERE email='${user}';"
else
    echo "Mysql connection failed"
fi




echo -e "\033[93m###############################################################################"
echo -e "\033[93m#                            Password Changer done                            #"
echo -e "\033[93m###############################################################################"
