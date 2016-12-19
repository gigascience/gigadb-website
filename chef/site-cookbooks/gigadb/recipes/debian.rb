#
# Cookbook Name:: gigadb
# Recipe:: debian
#
# Copyright 2014, Cogini
#
# All rights reserved - Do Not Redistribute
#

include_recipe "apt"
include_recipe "nginx"

# Defined in Vagrantfile
# Locates GigaDB in /vagrant directory
site_dir = node[:gigadb][:site_dir]

# Defined in gigadb attributes
python_env = node[:gigadb][:python][:virtualenv]
build_dir = node[:gigadb][:python][:build_dir]

#########################
#### Directory admin ####
#########################

yii_framework node[:yii][:version] do
    symlink "#{node[:gigadb][:site_dir]}/../yii"
end

# app_user should be nginx or www-data depending on platform
# build_dir is owned by www-data but /home/gigadb/.virtualenvs/gigadb
# is owned by gigadb user
[build_dir, python_env].each do |dir|
    directory dir do
        owner 'www-data'
        action :create
        recursive true
    end
end

################
#### Python ####
################

%w{libpq-dev python-software-properties}.each do |pkg|
    package pkg do
        action :install
    end
end

###############
#### PHP-5 ####
###############

apt_repository 'php5-fpm' do
  uri          'http://ppa.launchpad.net/l-mierzwa/lucid-php5/ubuntu/'
  distribution node['lsb']['codename']
  components   ['main']
  keyserver    'keyserver.ubuntu.com'
  key          '9EFEE11E2963CEB229DB0F939E51F82267E15F46'
end
