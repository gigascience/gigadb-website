The CAPTCHA requires a PHP library. For running on Vagrant, you also need to set permissions on the host system for a shared folder, see below.

1. GD library - It can be used to create and manipulate image files in a variety of different image formats, including GIF, PNG, JPEG, WBMP, and XPM.

2. ‘tempcaptcha’ folder




1) INSTALL GD LIBRARY

1. vagrant ssh
2. 'sudo su' - to enter root
3. yum install php-gd
4. 'Is this ok [y/N]' - choose y
5. Done

3) TEMPCAPTCHA CHMOD RIGHTS
On the host system (assuming you are running vagrant, you need to allow file creation on your host system too)
1. cd /
2. cd vagrant/images/
3. sudo su
4. chmod -R 777 tempcaptcha/
5. check chmod rights: stat —-format ‘%a’ tempcaptcha/
6. Done





