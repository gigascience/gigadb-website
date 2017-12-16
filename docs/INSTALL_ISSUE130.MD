The CAPTCHA requires a PHP library "php-gd", but this has been added to the Chef-Solo provisioning script.
For running on Vagrant, you also need to set permissions on the host system for a shared folder, see below.

TEMPCAPTCHA CHMOD RIGHTS
On the host system (assuming you are running vagrant, you need to allow file creation on your host system too)
1. cd /
2. cd vagrant/images/
3. sudo su
4. chmod -R 777 tempcaptcha/
5. check chmod rights: stat —-format ‘%a’ tempcaptcha/
6. Done





