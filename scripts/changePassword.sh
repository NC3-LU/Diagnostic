#!/usr/bin/env bash
#
# Change user password for Diagnostic's application user

sqlPassword=diagnostic
user="${1:-}"
strongPass=0
password="something"
password_repeat="else"

if [ $# -ne 1]; then
	echo "You need to specify the user of the diagnostic which password should be used. \nExample : ./changePassword.sh \"diagnostic@cases.lu\""
	exit 0
fi
	
echo -e "\033[93m###############################################################################\033[0m"
echo -e "\033[93m#                             Password Changer                                #\033[0m"
echo -e "\033[93m###############################################################################\033[0m"

#Connexion test to the database
echo -e "\033[94mConnecting to database and verify user...\033[0m"
mysql -u diagnostic -p"${sqlPassword}" -e "USE diagnostic;" > /dev/null 2>&1
if [ "$?" -ne 0 ]; then
	echo -e "\033[31mConnection to the diagnostic database has failed.\033[0m\n\033[32mDoes this script have the right sql password for the diagnostic user ?\033[0m"
	exit 0
fi

#Testing if the user exists
user_exists=$(mysql -ss -N -u diagnostic -p"${sqlPassword}" -e "SELECT id FROM diagnostic.users WHERE email='$user';")
if [ ! -z $user_exists ]; then
    echo -e "\033[94m Connection established, user exists\033[0m"
	#Check strength and repetition of the password
	while [ "$password" != "$password_repeat" ] || [ $strongPass -eq 0 ]; do
		strongPass=1
		password=''
		echo -e -n "\033[94mPlease, set a new password for ${user} > \033[0m"
		read -s password
		passwordLength=${#password}

		# Testing the password
		if ! [[ $password =~ .*[0-9]+.* ]]
		then
			strongPass=0
			echo "\033[31mPassword need to contain at least one digit to be a valid password\033[0m"
		fi
	
		if ! [[ $password =~ .*[A-Z]+.* ]]
		then
			strongPass=0
			echo "\033[31mPassword need to contain at least one upper char to be a valid password\033[0m"
		fi
		
		if ! [[ $password =~ .*[a-z]+.* ]]
		then
			strongPass=0
			echo "\033[31mPassword need to contain at least one lower char to be a valid password\033[0m"
		fi
		
		if ! [ $passwordLength -gt 7 ]
		then
			strongPass=0
			echo "\033[31mPassword need to contain at least 8 chars to be a valid password\033[0m"
		fi
		
		if ! [[ $password =~ .*[^[:alnum:]]+.* ]]
		then
			strongPass=0
			echo "\033[31mPassword need to contain at least one special char to be a valid password\033[0m"
		fi

		#ask to repeat password if the password is strong enough
		if [[ $strongPass -eq 1 ]]; then
		
			echo -e "\033[94mRepeat password > \033[0m"
			read -s password_repeat
			if [[ "$password" != "$password_repeat" ]]; then
				echo -e "\033[94mThe passwords do not match. Try again please\033[0m"
			fi
		fi
	done

	#hashing the password
	hash_password=`/usr/bin/php <<EOF
	<?php echo password_hash(("$password"), PASSWORD_DEFAULT); ?>
EOF`

	#Send the new password to the database
	printf -v hash_password $hash_password
    mysql -h localhost -u diagnostic -p"${sqlPassword}" -e "UPDATE diagnostic.users SET password='$hash_password' WHERE email='$user';" > /dev/null 2>&1
else
    echo "\033[35mMysql connection failed. Do this script have the right sql password ?\033[0m"
	exit 0
fi

echo -e "\033[93m###############################################################################\033[0m"
echo -e "\033[93m#                            Password Changer done                            #\033[0m"
echo -e "\033[93m###############################################################################\033[0m"
