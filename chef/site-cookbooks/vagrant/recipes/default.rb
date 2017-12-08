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

['vim', 'tree'].each do |pkg|
    package pkg
end

# iptables not required for default development environment
service 'iptables' do
    action [:disable, :stop]
    ignore_failure true
end

# Allow php-fpm to run as root. This enables the GigaDB
# to be served from the shared directory by the Docker
# container
bash 're-configure php-fpm' do
  user 'root'
  code <<-EOH
    service php-fpm stop
    # Enable php-fpm to run as root so it can access the assets and protected/runtime dirs
    sed -i -e 's/nobody/root/g' /etc/php-fpm.d/www.conf
    # Allow php-fpm to start as root user
    sed -i -e 's/--daemonize/-R --daemonize/g' /etc/init.d/php-fpm
    # Restart php-fpm
    service php-fpm start
    EOH
end