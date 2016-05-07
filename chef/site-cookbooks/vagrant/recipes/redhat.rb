#
# Cookbook Name:: vagrant
# Recipe:: redhat
#
# Copyright 2016, GigaScience
#
# All rights reserved - Do Not Redistribute
#

assets_dir = "/vagrant/assets"

directory assets_dir do
    action :create
    recursive true
    mode '0777'
end