#
# Cookbook Name:: fileserver
# Recipe:: default
#
# Copyright 2016, GigaScience
#
# All rights reserved - Do Not Redistribute
#

include_recipe 'postgresql::server'
include_recipe 'vsftpd'
include_recipe 'cron'
include_recipe 'nfs::client4'

['vim', 'tree'].each do |pkg|
    package pkg
end

# iptables not required for default development environment
service 'iptables' do
    action [:disable, :stop]
end

####################################
#### Set up PostgreSQL database ####
####################################

# Defined in Vagrantfile - provides database access details
db = node[:fileserver][:db]
if db[:host] == 'localhost'

    db_user = db[:user]

    postgresql_user db_user do
        password db[:password]
    end

    postgresql_database db[:database] do
        owner db_user
    end

    bash 'Restore ftpusers database' do
        db_user = db[:user]
        password = db[:password]
        database = db[:database]
        sql_script = db[:sql_script]

        code <<-EOH
            export PGPASSWORD='#{password}'; psql -U #{db_user} -h localhost #{database} < #{sql_script}
        EOH
    end
end

###############################
#### Install VSFTPD server ####
###############################

# For testing
#bash 'Install ftp client' do
#    code <<-EOH
#        sudo yum -y install ftp
#    EOH
#end

['ftp'].each do |pkg|
    package pkg
end

local_root = node[:vsftpd][:config][:local_root]
bash 'Create local root directory' do
    code <<-EOH
        mkdir #{local_root}
        chown -R ftp:ftp #{local_root}
        chmod -R u-w #{local_root}
    EOH
end

bash 'Create test data' do
    code <<-EOH
        echo "stuff" >#{local_root}/foo.txt
        chown ftp:ftp #{local_root}/foo.txt
    EOH
end

######################################################
#### Install update_ftpusers script from template ####
######################################################

directory '/home/vagrant/bin' do
  owner 'vagrant'
  group 'vagrant'
  mode '0755'
  action :create
end

directory '/var/ftp/temporary_upload' do
  owner 'ftp'
  group 'ftp'
  mode '0755'
  action :create
end

template "/home/vagrant/bin/update_ftpusers.sh" do
    source "update_ftpusers.sh.erb"
    owner 'vagrant'
    group 'vagrant'
    mode 0700
end

#########################################
#### Set up ftp user synchronisation ####
#########################################

cron 'FTP users synchronisation cron job' do
    minute '*'
    hour '*'
    day '*'
    month '*'
    shell '/bin/bash'
    path '/sbin:/bin:/usr/sbin:/usr/bin:/usr/local/bin'
    user 'root'
    command '/home/vagrant/bin/update_ftpusers.sh > /dev/null'
end

bash 'restart cron service' do
    code <<-EOH
        service crond restart
    EOH
end

############################
#### Set up NFS folders ####
############################

mount_point = node[:fileserver][:mount_point]
directory mount_point do
  action :create
end

# Test mount resource in Chef by mounting /opt/chef onto /mnt/chef
remote_folder = node[:fileserver][:device]
mount mount_point do
  device remote_folder
  fstype 'none'
  options 'bind,rw'
  action [:mount, :enable]
end
