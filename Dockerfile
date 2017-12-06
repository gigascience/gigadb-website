FROM centos:6.7
MAINTAINER Peter Li <peter@gigasciencejournal.com>

# Install httpd
#RUN yum -y install httpd && echo "Apache HTTPD" >> /var/www/html/index.html

#EXPOSE 80

## Simple startup script to avoid some issues observed with container restart
#ADD run-httpd.sh /run-httpd.sh
#RUN chmod -v +x /run-httpd.sh
#CMD ["/run-httpd.sh"]

####

# wheel_tty.sh #
# Allows wheel group to run all commands without password and tty
#RUN sed -e "/^#/ {/%wheel/s/^# *//}" -i /etc/sudoers
#RUN sed -e "/^#/! {/requiretty/s/^/# /}" -i /etc/sudoers

# common.sh #
#RUN yum -y groupinstall \
#	base \
#	core

#RUN yum -y update && yum -y install \
#	autofs \
#	bind-utils \
#	bzip2 \
#	deltarpm \
#  epel-release \
#	mlocate \
#	ntp \
#	wget \
#	nfs-utils \
#	unzip \
#	yum-plugin-remove-with-leaves \
#	yum-utils

# Install dependencies
#RUN yum -y install $(cat /vagrant/packages.txt|xargs)

# Install chef
RUN curl -LO https://www.opscode.com/chef/install.sh && bash ./install.sh -v 12.18.31

COPY ./chef /chef
WORKDIR /chef

# Disable iptables on boot up
RUN yum -y install iptables && chkconfig iptables off

EXPOSE 80

#CMD ["/usr/bin/chef-solo -c /vagrant/chef/docker-chef-solo/solo.rb -j /vagrant/chef/docker-chef-solo/node.json"]

## Simple startup script to avoid some issues observed with container restart
ADD run-chef.sh /run-chef.sh
RUN chmod -v +x /run-chef.sh
CMD ["/run-chef.sh"]
#CMD ["/bin/bash"]
