FROM centos
MAINTAINER Peter Li <peter@gigasciencejournal.com>

# Install httpd and php
RUN yum -y install httpd
RUN yum -y install php

# Run Apache web server
CMD ["/usr/sbin/apachectl", "-DFOREGROUND"]