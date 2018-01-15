#
# Cookbook Name:: vagrant
# Recipe:: default
#
# Copyright 2012, Cogini
#
# All rights reserved - Do Not Redistribute
#

case node[:platform_family]
when 'rhel'
    include_recipe 'vagrant::redhat'
when 'debian'
    include_recipe 'vagrant::debian'
end

include_recipe "gigadb"

['vim', 'tree', 'php-pecl-xdebug'].each do |pkg|
    package pkg
end

# iptables not required for default development environment
service 'iptables' do
    action [:disable, :stop]
end
