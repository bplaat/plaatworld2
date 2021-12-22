[&laquo; Back to the README.md](../README.md)

# Installation Documentation

## Windows
- Install [XAMPP](https://www.apachefriends.org/download.html) Apache web server, PHP and MySQL database
- Install [Composer](https://getcomposer.org/download/) PHP package manager
- Clone repo in the `C:/xampp/htdocs` folder

    ```
    cd C:/xampp/htdocs
    git clone https://github.com/bplaat/plaatworld2.git
    cd plaatworld2
    ```
- Install deps via Composer

    ```
    cd server
    composer install
    ```
- Copy `server/.env.example` to `server/.env`
- Generate Laravel security key

    ```
    php artisan key:generate
    ```
- Link the storage and public folder together

    ```
    php artisan storage:link
    ```
- Add following lines to `C:/xampp/apache/conf/extra/httpd-vhosts.conf` file

    ```
    # Plaatworld2 vhosts

    <VirtualHost *:80>
        ServerName plaatworld2.test
        DocumentRoot "C:/xampp/htdocs/plaatworld2/server/public"
    </VirtualHost>

    <VirtualHost *:80>
        ServerName www.plaatworld2.test
        Redirect permanent / http://plaatworld2.test/
    </VirtualHost>
    ```
- Add the following lines to `C:/Windows/System32/drivers/etc/hosts` file **with administrator rights**

    ```
    # Plaatworld2 local domains
    127.0.0.1 plaatworld2.test
    127.0.0.1 www.plaatworld2.test
    ```
- Start Apache and MySQL via XAMPP control panel
- Create MySQL user and database (may be via [phpmyadmin](http://localhost/phpmyadmin/))
- Fill in MySQL user, password and database information in `server/.env`
- Create database tables

    ```
    php artisan migrate --seed
    ```
- Goto http://plaatworld2.test/ and you're done! 🎉

## macOS
TODO

## Linux

### Ubuntu based distro's
- Install LAMP stack

    ```
    sudo apt install apache2 php php-dom mysql-server composer
    ```
-  Fix `/var/www/html` Unix rights hell

    ```
    # Allow Apache access to the folders and the files
    sudo chgrp -R www-data /var/www/html
    sudo find /var/www/html -type d -exec chmod g+rx {} +
    sudo find /var/www/html -type f -exec chmod g+r {} +

    # Give your owner read/write privileges to the folders and the files, and permit folder access to traverse the directory structure
    sudo chown -R $USER /var/www/html/
    sudo find /var/www/html -type d -exec chmod u+rwx {} +
    sudo find /var/www/html -type f -exec chmod u+rw {} +

    # Make sure every new file after this is created with www-data as the 'access' user.
    sudo find /var/www/html -type d -exec chmod g+s {} +
    ```
- Clone repo in the `/var/www/html` folder

    ```
    cd /var/www/html
    git clone https://github.com/bplaat/plaatworld2.git
    cd plaatworld2
    ```
- Install deps via Composer

    ```
    cd server
    composer install
    ```
- Copy `server/.env.example` to `server/.env`
- Generate Laravel security key

    ```
    php artisan key:generate
    ```
- Link the storage and public folder together

    ```
    php artisan storage:link
    ```
- Create the file `/etc/apache2/sites-available/plaatworld2.conf` **as root**

    ```
    # Plaatworld2 vhosts

    <VirtualHost *:80>
        ServerName plaatworld2.test
        DocumentRoot "/var/www/html/plaatworld2/server/public"
    </VirtualHost>

    <VirtualHost *:80>
        ServerName www.plaatworld2.test
        Redirect permanent / http://plaatworld2.test/
    </VirtualHost>
    ```
- Enable the site

    ```
    sudo a2ensite plaatworld2
    ```
- Edit this line in `/etc/apache2/apache2.conf` at `AllowOverride` from `None` to `All` **as root**

    ```
    <Directory /var/www/>
        ...
        AllowOverride All
        ...
    </Directory>
    ```
- Enable the Apache rewrite module

    ```
    sudo a2enmod rewrite
    ```
- Restart apache

    ```
    sudo service apache2 restart
    ```
- Add following lines to `/etc/hosts` file **as root**

    ```
    # Plaatworld2 local domains
    127.0.0.1 plaatworld2.test
    127.0.0.1 www.plaatworld2.test
    ```
- Create MySQL user and database
- Fill in MySQL user, password and database information in `server/.env`
- Create database tables

    ```
    php artisan migrate --seed
    ```
- Goto http://plaatworld2.test/ and you're done! 🎉
