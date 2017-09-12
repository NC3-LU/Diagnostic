#!/usr/bin/env bash
#
# Setup apache2 server in order to run the diagnostic application under chosen domain
set -e


function main(){
    echo -e "\033[93m###############################################################################"
    echo -e "\033[93m#                                 Virtual host setup                          #"
    echo -e "\033[93m#                                                                             #"
    echo -e "\033[93m###############################################################################"
    readonly path=$(pwd)
#    echo ""
#    read -p "Choose the address to which you will find the application in your browser > " server_name
#    echo ""
#    echo -e "\033[94mThe application will be accessed by typing \033[92mhttp://$server_name\033[0m in your browser"

    declare -A virtual_host_configurationArray
    virtual_host_configurationArray=(
#        ["%%SERVER_NAME%%"]=$server_name
        ["%%ROOT_DIRECTORY%%"]="$path/public"
    )

    echo -e "\033[94m $(pwd)\033[93mis now set as the root directory for the newly created virtual host\033[0m"

#    sudo cp ../$applicationDir/scripts/virtual_host.conf /etc/apache2/sites-available/$server_name.conf
	sudo cp ../$applicationDir/scripts/virtual_host.conf /etc/apache2/sites-available/diagnostic.conf

    virtual_host_configure() {
        # Loop the config array
        for i in "${!virtual_host_configurationArray[@]}"
        do
            search=$i
            replace=${virtual_host_configurationArray[$i]}
            #sudo sed -i "s@${search}@${replace}@g" /etc/apache2/sites-available/$server_name.conf
			sudo sed -i "s@${search}@${replace}@g" /etc/apache2/sites-available/diagnostic.conf
        done
    }
    virtual_host_configure

    echo -e "\033[32m###############################################################################"
#    echo -e "\033[32m /etc/apache2/sites-available/$server_name.conf edited"
    echo -e "\033[32m /etc/apache2/sites-available/diagnostic.conf edited"
    echo -e "\033[32m###############################################################################"
    echo ""
    echo ""
#    sudo sed -i "1s/^/127.0.0.1       $server_name\n/" /etc/hosts
    sudo sed -i "1s/^/127.0.0.1       diagnostic\n/" /etc/hosts

    echo -e "\033[32m###############################################################################"
    echo -e "\033[32m/etc/hosts edited"
    echo -e "\033[32m###############################################################################"
    echo ""

    echo -e "\033[94m"
    echo "If you plan to access the application from another machine through network you may need to disable default apache's site"
    read -p "Would you like this script to disable it for you ? (y/n) " choice
    echo -e "\033[0m"

    case "$choice" in
        [yY][eE][sS]|[yY])
            sudo a2dissite 000-default.conf
            ;;
    esac

#    sudo a2ensite $server_name.conf
	sudo a2ensite diagnostic.conf
    export server_name

    echo -e "\033[93m###############################################################################"
    echo -e "\033[93m#                            Virtual host set                                 #"
    echo -e "\033[93m#                                                                             #"
    echo -e "\033[93m###############################################################################\033[0m"
}
main "$@"
