FROM centos:6
MAINTAINER Peter Li <peter@gigasciencejournal.com>

# Install httpd and php
RUN yum -y install httpd
RUN yum -y install php
#RUN yum -y install sed

# wheel_tty.sh #
# Allows wheel group to run all commands without password and tty
#RUN sed -e "/^#/ {/%wheel/s/^# *//}" -i /etc/sudoers
#RUN sed -e "/^#/! {/requiretty/s/^/# /}" -i /etc/sudoers

# common.sh #
# Update installed packages
RUN yum -y update

# Install base and core groups
RUN yum -y groupinstall base core

# Used to install common packages on all vm images
RUN PACKAGES="ntp bind-utils wget nfs-utils autofs bzip2 unzip mlocate yum-utils yum-plugin-remove-with-leaves deltarpm epel-release"
RUN yum install -y $PACKAGES

# Disable SElinux
# sudo sed -i 's/enforcing/disabled/' /etc/selinux/config /etc/selinux/config

# Disable iptables on boot up
RUN sudo chkconfig iptables off


#################
# Run Apache web server
CMD ["/usr/sbin/apachectl", "-DFOREGROUND"]