#
# Cookbook Name:: gigadb
# Recipe:: redhat
# Copyright 2014, Cogini
#
# All rights reserved - Do Not Redistribute
#

include_recipe "nginx"

# Defined in Vagrantfile
site_dir = node[:gigadb][:site_dir]

# Defined in gigadb attributes
python_env = node[:gigadb][:python][:virtualenv]
build_dir = node[:gigadb][:python][:build_dir]

#########################
#### Directory admin ####
#########################

# app_user should be nginx or www-data depending on platform
# build_dir is owned by nginx but /home/gigadb/.virtualenvs/gigadb is
# owned by gigadb user
[build_dir, python_env].each do |dir|
    directory dir do
        owner 'nginx'
        action :create
        recursive true
    end
end

###############
#### nginx ####
###############

case node['gigadb_box']
when 'aws'
  # Create access log for nginx in /vagrant
  file "/vagrant/logs/access.log" do
    owner 'root'
    group 'gigadb-admin'
    mode 0666
    action :create
  end
when 'centos', 'ubuntu'
  # Create access log for nginx in /vagrant
  file "/vagrant/logs/access.log" do
    owner 'vagrant'
    group 'vagrant'
    mode 0666
    action :create
  end
end




# update openssl and curl to make sure TLS v1.2 is supported when connecting the web from within the box
# because the default version in Centos 6.7 doesn't support TLS v1.2 and github.com just removed support for pre-TLS v1.2 
# see: https://github.com/blog/2507-weak-cryptographic-standards-removed
['openssl', 'curl'].each do |pkg|
    yum_package pkg do
        action :upgrade
        flush_cache [ :before ]
    end
end
