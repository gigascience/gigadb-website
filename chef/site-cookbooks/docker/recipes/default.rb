#
# Cookbook Name:: docker
# Recipe:: docker
#
# Copyright 2014, Cogini
#
# All rights reserved - Do Not Redistribute
#

# Locates GigaDB in /vagrant directory
vm_dir = node[:docker][:VM_dir]
yii_dir = node[:docker][:yii_dir]

# Create yii-1.1.6 directory
directory "#{yii_dir}" do
  owner "vagrant"
  group "vagrant"
  mode "0755"
  action :create
  recursive true
end

# Install Yii version into yii-1.1.16 directory
git "#{yii_dir}" do
  repository "git://github.com/yiisoft/yii.git"
  revision "master"
  action :sync
end


#####################################
#### Create files from templates ####
#####################################

template "#{vm_dir}/index.php" do
    source "yii-index.php.erb"
    mode "0644"
end

template "#{vm_dir}/protected/yiic.php" do
    source "yiic.php.erb"
    mode "0644"
end

template "#{vm_dir}/protected/config/local.php" do
    source "yii-local.php.erb"
    mode "0644"
end

template "#{vm_dir}/protected/config/main.php" do
    source "yii-main.php.erb"
    mode "0644"
end

template "#{vm_dir}/protected/config/db.json" do
    source 'yii-db.json.erb'
    mode '0644'
end

template "#{vm_dir}/protected/scripts/set_env.sh" do
    source 'set_env.sh.erb'
    mode '0644'
end

# For Elastic Search
template "#{vm_dir}/protected/config/es.json" do
    source "es.json.erb"
    mode 0644
end

template "#{vm_dir}/protected/scripts/update_links.sh" do
    source "update_links.sh.erb"
end

template "#{vm_dir}/files/html/help.html" do
    source "yii-help.html.erb"
    mode 0644
end


##############
#### Less ####
##############

# Compile less files
execute 'npm install -g less'
if node[:gigadb_box] == 'aws'
    css_user = 'centos'
else
    css_user = 'vagrant'
end

# Check yiic is executable
file "#{vm_dir}/protected/yiic" do
  mode '0755'
  action :touch
end


execute 'Build css' do
    command "#{vm_dir}/protected/yiic lesscompiler"
    cwd "#{vm_dir}/protected"
    group 'root'
    user 'root'
end
