#
# Cookbook Name:: vagrant
# Recipe:: debian
#
# Copyright 2016, GigaScience
#
# All rights reserved - Do Not Redistribute
#

assets_dir = "/home/vagrant/assets"

directory assets_dir do
    action :create
    recursive true
    mode '0777'
end

link '/vagrant/assets' do
    action :create
    to assets_dir
end