Diagnostic Technical guide
==========================

**Diagnostic** is an open source web-browser based application designed
to help organizations assessing their level of security regarding to
**information security most common threats and vulnerabilities**.

The purpose of this document is to describe each step you have to go
through in order to get an operational Diagnostic application running.

Components
----------

### PHP

Install **php7.1** and following extensions :

    sudo apt-get install libapache2-mod-php7.1 php7.1-mcrypt php7.1-mysql php7.1-zip php-xml

### APACHE

Install **apache2** and enable following modules :

    sudo apt-get install apache2
    sudo a2enmod rewrite

### COMPOSER

Install **composer** and use it in the root directory to download all
dependencies listed in the **composer.json** file. You could get it by
go to the following link : [https://getcomposer.org/download/](https://getcomposer.org/download/)

And use the command in the root directory:

    php composer.phar install

### MYSQL

Install **mysql-server** and execute **db\_initialization.sql** script
on database, which can be found in **ROOT\_DIRECTORY/scripts** folder.

    sudo apt-get install mysql-server

Duplicate **ROOT\_DIRECTORY/config/autoload/global.php.dist** as
**global.php** and fill in **DB\_NAME** & **DB\_HOST** fields in the following line :

    'dsn'  => 'mysql:dbname=%%DB_NAME%%;host=%%DB_HOST%%'

Duplicate **ROOT\_DIRECTORY/config/autoload/local.php.dist** as
**local.php** and fill in **DB\_USER** & **DB\_PASSWORD** fields in the following line :

    'username' => '%%DB_USER%%',
    'password' => '%%DB_PASSWORD%%'

*\_*


### Change language

Diagnostic application is available in both
french and english. Find the **%%LANG%%** field in

    /ROOT_DIRECTORY/module/Diagnostic/config/module.config.php

and replace by either **"en"** or **"fr"**

*\_*

### Setting up

Set up a virtual host that will point to the **public/index.php**
application document root.

Your configuration file should look like this :

    <VirtualHost *:80>
        ServerName %SERVER_NAME%
        DocumentRoot %ROOT_DIRECTORY%/public
        <Directory %ROOT_DIRECTORY%/public>
            DirectoryIndex index.php
            AllowOverride All
            Order allow,deny
            Allow from all
            <IfModule mod_authz_core.c>
            Require all granted
            </IfModule>
        </Directory>
    </VirtualHost>

In addition to that, you may need to edit your **hosts** file. You will
find further informations about virtual hosts
[here](https://www.digitalocean.com/community/tutorials/how-to-set-up-apache-virtual-hosts-on-ubuntu-14-04-lts)

### Accessing the application

You can access the application by opening your favorite web browser (we
recommend using **Chrome** though) and type `http://%SERVER_NAME%`

Default credentials are :

    `Login : "diagnostic@cases.lu"`

    `Password : "Diagnostic1!"`

*\_*

Troubleshoot
------------

### 403 Forbidden

You may have cloned the repository outside the casual `/var/www/`
directory and/or changed group owner of the directory. Set up adequate
access rights by using :

    sudo chgrp -R www-data ../ROOT_DIRECTORY
    sudo chmod 2750 ../ROOT_DIRECTORY
    sudo chmod 2770 ../ROOT_DIRECTORY/data

### Accessing the application from host

If you try to access the application over the network, just set up your
virtual host accordingly and don’t forget to disable default web
application by using :

    sudo a2dissite 000-default.conf
    
Help us
------------

### Access the test branch

We have a test branch where we are testing new features. If you want,
you can help us creating these new features by cloning the test branch.
