#
# Cookbook Name:: vagrant
# Recipe:: default
#
# Copyright 2012, Cogini
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

include_recipe "gigadb"

['vim', 'tree'].each do |pkg|
    package pkg
end

service 'iptables' do
    action [:disable, :stop]
end
