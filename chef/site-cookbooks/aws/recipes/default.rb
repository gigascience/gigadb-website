#
# Cookbook Name:: aws
# Recipe:: default
#
# Copyright 2016, GigaScience
#

include_recipe 'user'

# Create www-data user and group
user_account 'www-data' do
    comment 'www-data'
end

# Add user accounts to AWS instance
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

group 'www-data' do
    action  :modify
    members [user1, user2, user3]
    append  true
end

dirs = %w{
  assets
  protected/runtime
  giga_cache
}

dirs.each do |component|
    the_dir = "/vagrant/#{component}"

    bash 'setup permissions' do
        code <<-EOH
            mkdir -p #{the_dir}
            chown -R www-data:gigadb-admin #{the_dir}
            chmod -R ug+rwx #{the_dir}
        EOH
    end
end

bash 'gigadb-admin group permissions' do
    code <<-EOH
        chgrp -R gigadb-admin /vagrant/*
    EOH
end
