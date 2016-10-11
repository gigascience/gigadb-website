#
# Cookbook Name:: fileserver
# Recipe:: default
#
# Copyright 2016, GigaScience
#
# All rights reserved - Do Not Redistribute
#

include_recipe 'user'
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

##############################
#### User and group admin ####
##############################

# Create user accounts
user1 = node[:fileserver][:user1]
user1_name = node[:fileserver][:user1_name]
user1_public_key = node[:fileserver][:user1_public_key]

user_account node[:fileserver][:user1] do
    comment   node[:fileserver][:user1_name]
    ssh_keys  node[:fileserver][:user1_public_key]
    home      "/home/#{node[:fileserver][:user1]}"
end

user2 = node[:fileserver][:user2]
user2_name = node[:fileserver][:user2_name]
user2_public_key = node[:fileserver][:user2_public_key]

user_account node[:fileserver][:user2] do
    comment   node[:fileserver][:user2_name]
    ssh_keys  node[:fileserver][:user2_public_key]
    home      "/home/#{node[:fileserver][:user2]}"
end

admin_user = node[:fileserver][:admin_user]
admin_user_name = node[:fileserver][:admin_user_name]
admin_user_public_key = node[:fileserver][:admin_user_public_key]

user_account node[:fileserver][:admin_user] do
    comment   node[:fileserver][:admin_user_name]
    ssh_keys  node[:fileserver][:admin_user_public_key]
    home      "/home/#{node[:fileserver][:admin_user]}"
end

# Create group for GigaDB admins
group 'gigadb-admin' do
  action    :create
  members   [user1, user2, admin_user]
  append    true
end

group 'wheel' do
    action  :modify
    members [user1, user2, admin_user]
    append  true
end


####################################
#### Set up PostgreSQL database ####
####################################

cookbook_file '/tmp/ftpusers_testdata.sql' do
  source 'ftpusers_testdata.sql'
  owner node[:fileserver][:admin_user]
  group node[:fileserver][:admin_user]
  mode '0755'
  action :create_if_missing
end

# Defined in Vagrantfile - provides database access details
db = node[:fileserver][:db]
host = node[:fileserver][:db][:host]
if host == 'localhost'

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

directory '/usr/local/fileserver/bin' do
  owner 'root'
  group 'root'
  mode '0755'
  recursive true
  action :create
end

temp_upload_dir = "#{mount_point}/temporary_upload"
directory temp_upload_dir do
  owner 'ftp'
  group 'ftp'
  mode '0755'
  action :create
end

template "/usr/local/fileserver/bin/update_ftpusers.sh" do
    source "update_ftpusers.sh.erb"
    owner 'root'
    group 'root'
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
    command '/usr/local/fileserver/bin/update_ftpusers.sh > /dev/null'
end

bash 'restart cron service' do
    code <<-EOH
        service crond restart
    EOH
end


