#! /usr/bin/env bash
# Files for provisioning a vagrant installation

DBHOST=localhost
DBNAME=magento2
DBUSER=magento2
DBPASSWD=P@55word123

echo -e "\n--- Ubuntu Image Bug Fix ---\n"
sed -i '1s/^/127.0.1.1 ubuntu-xenial \n/' /etc/hosts

echo -e "\n--- Updating packages list ---\n"
apt-get -qq update

echo -e "\n--- Add some repos to update our distro ---\n"
add-apt-repository ppa:ondrej/php

echo -e "\n--- Updating packages list ---\n"
apt-get -qq update

echo -e "\n--- Install Mysql specific packages and settings ---\n"
echo "mysql-server mysql-server/root_password password $DBPASSWD" | debconf-set-selections
echo "mysql-server mysql-server/root_password_again password $DBPASSWD" | debconf-set-selections
apt-get -y install mysql-server > /dev/null 2>&1

echo -e "\n--- Setting up our MySQL user and db ---\n"
mysql -uroot -p$DBPASSWD -e "CREATE DATABASE $DBNAME"
mysql -uroot -p$DBPASSWD -e "grant all privileges on $DBNAME.* to '$DBUSER'@'localhost' identified by '$DBPASSWD'"

echo -e "\n--- Install PHP specific packages and settings ---\n"
apt-get -y install nginx php7.0-fpm php7.0-mysql php7.0-curl php7.0-dom php7.0-mcrypt php7.0-intl php7.0-mbstring php7.0-zip php7.0-bcmath php7.0-gd

echo -e "\n--- Setup Nginx ---\n"
cp /var/www/html/limitless/vagrant/magento2.conf /etc/nginx/sites-available/
rm /etc/nginx/sites-enabled/default
rm /etc/nginx/sites-avalible/default
ln -s /etc/nginx/sites-available/magento2.conf /etc/nginx/sites-enabled/
service nginx restart