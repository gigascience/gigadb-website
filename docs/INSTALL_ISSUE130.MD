The CAPTCHA in order to work you have to install two PHP libraries/extensions, which should be usually installed and enabled but since all the versions of the framework and PHP are not updated, we have to do this manually.

1. GD library - It can be used to create and manipulate image files in a variety of different image formats, including GIF, PNG, JPEG, WBMP, and XPM.

2. ImageMagick extension - Imagick is a native PHP extension to create and modify images using the ImageMagick API.

3. ‘tempcaptcha’ folder




1) INSTALL GD LIBRARY

1. vagrant ssh
2. 'sudo su' - to enter root
3. yum install php-gd
4. 'Is this ok [y/N]' - choose y
5. Done


2) INSTALL IMAGEMAGICK EXTENSION

1. yum install ImageMagick ImageMagick-devel
2. 'Is this ok [y/N]' - choose y
3. pecl install imagick
4. Press Enter
4. vi /etc/php.ini
5. add at the bottom of the php.ini 'extension=imagick.so' (without '')
6. Click the 'esc' button
7. type ':wq' (without '') and hit enter
8. /etc/init.d/nginx restart
9. /etc/init.d/php-fpm restart
10. Create a phpinfo() file to check for GD Support 'enabled', and Imagick 'enabled'
11. Done


3) TEMPCAPTCHA CHMOD RIGHTS
On the host system (assuming you are running vagrant, you need to allow file creation on your host system too)
1. cd /
2. cd vagrant/images/
3. sudo su
4. chmod -R 777 tempcaptcha/
5. check chmod rights: stat —-format ‘%a’ tempcaptcha/
6. Done





