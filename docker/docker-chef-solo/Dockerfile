FROM centos:6.7
MAINTAINER Peter Li <peter@gigasciencejournal.com>

# Install chef
RUN curl -LO https://www.opscode.com/chef/install.sh && bash ./install.sh -v 12.18.31

EXPOSE 80

CMD ["/bin/bash"]
