FROM centos:6
MAINTAINER Peter Li <peter@gigasciencejournal.com>

# Install httpd
RUN yum -y install httpd && echo "Apache HTTPD" >> /var/www/html/index.html

# wheel_tty.sh #
# Allows wheel group to run all commands without password and tty
#RUN sed -e "/^#/ {/%wheel/s/^# *//}" -i /etc/sudoers
#RUN sed -e "/^#/! {/requiretty/s/^/# /}" -i /etc/sudoers

# common.sh #
#RUN yum -y groupinstall \
#	base \
#	core
#
#RUN yum -y update && yum -y install \
#	autofs \
#	bind-utils \
#	bzip2 \
#	deltarpm \
#  epel-release \
#  groupinstall \
#	mlocate \
#	ntp \
#	wget \
#	nfs-utils \
#	unzip \
#	yum-plugin-remove-with-leaves \
#	yum-utils

# Disable iptables on boot up
RUN chkconfig iptables off

EXPOSE 80

# Simple startup script to avoid some issues observed with container restart
ADD run-httpd.sh /run-httpd.sh
RUN chmod -v +x /run-httpd.sh
CMD ["/run-httpd.sh"]
