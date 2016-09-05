#
# Cookbook Name:: aws
# Recipe:: default
#
# Copyright 2016, GigaScience
#

include_recipe 'user'
include_recipe 'iptables'
include_recipe 'fail2ban'
include_recipe 'selinux'

############################
#### Configure iptables ####
############################

service "iptables" do
    action :start
end

iptables_rule 'prefix'
iptables_rule 'http'
iptables_rule 'ssh'
iptables_rule 'postgres'
iptables_rule 'postfix'

############################
#### Configure fail2ban ####
############################

# Protect against DDoS attacks
file "/etc/fail2ban/jail.local" do
    content <<-EOS
[ssh-ddos]

enabled  = true
port     = ssh
filter   = sshd-ddos
logpath  = /var/log/secure
maxretry = 6
    EOS
    owner "root"
    group "root"
    mode 0644
    notifies :restart, "service[fail2ban]"
end

###########################
#### Configure SELinux ####
###########################

selinux_state 'permissive' do
    action :permissive
end

# Add SELinux policies for GigaDB
bash 'gigadb-admin group permissions' do
    code <<-EOH
        semanage fcontext -a -t httpd_sys_rw_content_t "/vagrant/logs(/.*)?"
        restorecon -Rv "/vagrant/logs"

        semanage fcontext -a -t httpd_sys_content_t "/vagrant/index.php"
        restorecon -Rv "/vagrant/index.php"

        semanage fcontext -a -t httpd_sys_content_t "/vagrant/protected(/.*)?"
        restorecon -Rv "/vagrant/protected"

        semanage fcontext -a -t httpd_sys_rw_content_t "/vagrant/protected/runtime(/.*)?"
        restorecon -Rv "/vagrant/protected/runtime"

        semanage fcontext -a -t httpd_sys_rw_content_t "/vagrant/assets(/.*)?"
        restorecon -Rv "/vagrant/assets"

        semanage fcontext -a -t httpd_sys_content_t '/vagrant/css/site.css'
        restorecon -Rv '/vagrant/css/site.css'

        semanage fcontext -a -t httpd_sys_content_t '/vagrant/images(/.*)?'
        restorecon -Rv '/vagrant/images'

        setsebool -P httpd_can_network_connect 1
        setsebool -P httpd_can_network_connect_db 1
    EOH
end

selinux_state 'enforcing' do
    action :enforcing
end

##############################
#### User and group admin ####
##############################

# Create user accounts
user1 = node[:gigadb][:user1]
user1_name = node[:gigadb][:user1_name]
user1_public_key = node[:gigadb][:user1_public_key]

user_account node[:gigadb][:user1] do
    comment   node[:gigadb][:user1_name]
    ssh_keys  node[:gigadb][:user1_public_key]
    home      "/home/#{node[:gigadb][:user1]}"
end

user2 = node[:gigadb][:user2]
user2_name = node[:gigadb][:user2_name]
user2_public_key = node[:gigadb][:user2_public_key]

user_account node[:gigadb][:user2] do
    comment   node[:gigadb][:user2_name]
    ssh_keys  node[:gigadb][:user2_public_key]
    home      "/home/#{node[:gigadb][:user2]}"
end

user3 = node[:gigadb][:user3]
user3_name = node[:gigadb][:user3_name]
user3_public_key = node[:gigadb][:user3_public_key]

user_account node[:gigadb][:user3] do
    comment   node[:gigadb][:user3_name]
    ssh_keys  node[:gigadb][:user3_public_key]
    home      "/home/#{node[:gigadb][:user3]}"
end

# Create group for GigaDB admins
group 'gigadb-admin' do
  action    :create
  members   [user1, user2, user3]
  append    true
end

# Create www-data user and group
user_account 'www-data' do
    comment 'www-data'
end

group 'www-data' do
    action  :modify
    members [user1, user2, user3]
    append  true
end

#########################
#### Directory admin ####
#########################

dirs = %w{
  assets
  protected/runtime
  giga_cache
}

dirs.each do |component|
    the_dir = "/vagrant/#{component}"

    bash 'setup permissions' do
        code <<-EOH
            # mkdir -p #{the_dir}
            # chown -R nginx:gigadb-admin #{the_dir}
            chmod -R ug+rwx #{the_dir}
        EOH
    end
end

# Change files in /vagrant to gigadb-admin group
bash 'gigadb-admin group permissions' do
    code <<-EOH
        chgrp -R gigadb-admin /vagrant/*
    EOH
end

########################
#### Install GigaDB ####
########################

include_recipe 'gigadb'