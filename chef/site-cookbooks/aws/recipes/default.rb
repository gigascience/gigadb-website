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
include_recipe 'cron'

include_recipe 'postgresql'

# Locates GigaDB in /vagrant directory
site_dir = node[:gigadb][:site_dir]

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

group 'wheel' do
    action  :modify
    members [user1, user2, user3]
    append  true
end

#########################
#### Directory admin ####
#########################

# These folders are auto created by Vagrant. If not using Vagrant e.g.
# when using Chef-Solo for provisioning these folders are explicitly
# created.
dirs = %w{
  assets
  protected/runtime
  giga_cache
  logs
}

dirs.each do |component|
    the_dir = "/vagrant/#{component}"

    bash 'setup permissions' do
        code <<-EOH
        	# Check if directory exists
            if [ -d #{the_dir} ]
            then
                # Will enter here if the_dir exists,
                echo "#{the_dir} directory exists"
                chmod -R ugo+rwx #{the_dir}
            else
                mkdir -p #{the_dir}
                # chown -R nginx:gigadb-admin #{the_dir}
                chmod -R ugo+rwx #{the_dir}
            fi
        EOH
    end
end

# Change files in /vagrant to gigadb-admin group
bash 'gigadb-admin group permissions' do
    code <<-EOH
        chgrp -R gigadb-admin /vagrant/*
    EOH
end


#######################
#### Configure SSH ####
#######################

# Disable root logins and password authentication
bash 'Configure SSH' do
    code <<-EOH
        sed -i -- 's/#PermitRootLogin yes/PermitRootLogin no/g' /etc/ssh/sshd_config
        sed -i -- 's/PasswordAuthentication yes/PasswordAuthentication no/g' /etc/ssh/sshd_config
    EOH
end



########################
#### Install GigaDB ####
########################

include_recipe 'gigadb'

# Create gigadb_users postgres group role
bash 'Create postgres database roles' do
    code <<-EOH
        sudo -u postgres psql -U postgres -d postgres -c "CREATE ROLE gigadb_users;"
        sudo -u postgres psql -U postgres -d postgres -c "GRANT SELECT, INSERT, UPDATE, DELETE ON ALL TABLES IN SCHEMA public TO gigadb_users;"
    EOH
end

# Add postgresql users
users = node[:gigadb][:users]
users.each do |item|
    the_user = "#{item}"

    bash 'postgres stuff' do
        code <<-EOH
            sudo -u postgres psql -U postgres -d postgres -c "CREATE USER #{the_user} WITH LOGIN SUPERUSER;"
            sudo -u postgres psql -U postgres -d postgres -c "GRANT ALL PRIVILEGES ON DATABASE gigadb to #{the_user};"
            sudo -u postgres psql -U postgres -d postgres -c "GRANT gigadb_users TO #{the_user};"
        EOH
    end
end


###########################################
#### Set up automated database backups ####
###########################################

aws_access_key = node[:aws][:aws_access_key_id]
aws_secret_access_key = node[:aws][:aws_secret_access_key]
aws_default_region = node[:aws][:aws_default_region]

# Install AWS CLI
bash 'Install AWS CLI' do
    code <<-EOH
        curl "https://s3.amazonaws.com/aws-cli/awscli-bundle.zip" -o "awscli-bundle.zip"
        unzip awscli-bundle.zip
        sudo ./awscli-bundle/install -i /usr/local/aws -b /usr/local/bin/aws
        if [ -d /root/.aws ]
        then
            # Will enter here if .aws exists, even if it contains spaces
            echo ".aws folder exists"
        else
            mkdir -p /root/.aws
        fi
    EOH
end

template "/root/.aws/credentials" do
    source 'aws_credentials.erb'
    mode '0644'
    ignore_failure true
    action :create_if_missing
end

template "root/.aws/config" do
    source 'aws_config.erb'
    mode '0644'
    ignore_failure true
    action :create_if_missing
end

template "#{site_dir}/protected/scripts/db_backup.sh" do
    source 'db_backup.sh.erb'
    mode '0644'
end

bash 'make db_backup.sh executable' do
    code <<-EOH
        chown centos:gigadb-admin #{site_dir}/protected/scripts/db_backup.sh
        chmod ugo+x #{site_dir}/protected/scripts/db_backup.sh
    EOH
end

cron 'database backup cron job' do
    minute '59'
    hour '23'
    day '*'
    month '*'
    shell '/bin/bash'
    path '/sbin:/bin:/usr/sbin:/usr/bin:/usr/local/bin'
    user 'root'
    command '/vagrant/protected/scripts/db_backup.sh'
end

bash 'restart cron service' do
    code <<-EOH
        service crond restart
    EOH
end