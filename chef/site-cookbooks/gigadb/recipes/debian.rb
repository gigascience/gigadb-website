#
# Cookbook Name:: gigadb
# Recipe:: debian
#
# Copyright 2014, Cogini
#
# All rights reserved - Do Not Redistribute
#

include_recipe "apt"

yii_framework node[:yii][:version] do
    symlink "#{node[:gigadb][:site_dir]}/../yii"
end

%w{libpq-dev python-software-properties}.each do |pkg|
    package pkg do
        action :install
    end
end

apt_repository 'php5-fpm' do
  uri          'http://ppa.launchpad.net/l-mierzwa/lucid-php5/ubuntu/'
  distribution node['lsb']['codename']
  components   ['main']
  keyserver    'keyserver.ubuntu.com'
  key          '9EFEE11E2963CEB229DB0F939E51F82267E15F46'
end
