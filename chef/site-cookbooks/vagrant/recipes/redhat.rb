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

# To do: exclude Centos 6 postgres packages
bash 'Add postgres package repositories' do
		user 'root'
		cwd '/tmp'
    code <<-EOH
        curl -O https://download.postgresql.org/pub/repos/yum/9.1/redhat/rhel-6-x86_64/pgdg-centos91-9.1-6.noarch.rpm
        rpm -ivh pgdg*
    EOH
end