#
# Cookbook Name:: aws
# Recipe:: default
#
# Copyright 2016, GigaScience
#

include_recipe 'user'

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
