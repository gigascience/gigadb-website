FROM centos:6
MAINTAINER Peter Li <peter@gigasciencejournal.com>

# Install httpd and php
RUN yum -y install httpd

# wheel_tty.sh #
# Allows wheel group to run all commands without password and tty
#RUN sed -e "/^#/ {/%wheel/s/^# *//}" -i /etc/sudoers
#RUN sed -e "/^#/! {/requiretty/s/^/# /}" -i /etc/sudoers

# common.sh #
# Update installed packages
# RUN yum -y update

# Install base and core groups
#RUN yum -y groupinstall base core

# Used to install common packages on all vm images
#RUN PACKAGES="ntp bind-utils wget nfs-utils autofs bzip2 unzip mlocate yum-utils yum-plugin-remove-with-leaves deltarpm epel-release"
#RUN yum install -y $PACKAGES

#RUN yum -y install ntp bind-utils wget nfs-utils autofs bzip2 unzip mlocate yum-utils yum-plugin-remove-with-leaves deltarpm epel-release

# Disable iptables on boot up
#RUN chkconfig iptables off

# Add file in web directory
RUN echo "Apache HTTPD" >> /var/www/html/index.html

EXPOSE 80

# Simple startup script to avoid some issues observed with container restart
ADD run-httpd.sh /run-httpd.sh
RUN chmod -v +x /run-httpd.sh
CMD ["/run-httpd.sh"]
