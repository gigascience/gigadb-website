#
# Cookbook Name:: gigadb
# Recipe:: docker
#
# Copyright 2014, Cogini
#
# All rights reserved - Do Not Redistribute
#

# Create yii-1.1.6 directory
directory '#{site_dir}/yii-1.1.16' do
  owner 'root'
  group 'root'
  mode '0755'
  action :create
end

# Install Yii version into yii-1.1.16 directory
git '#{site_dir}/yii-1.1.16' do
  repository 'git@github.com:yiisoft/yii.git'
  revision 'master'
  action :sync
end


#####################################
#### Create files from templates ####
#####################################

template "#{site_dir}/index.php" do
    source "yii-index.php.erb"
    mode "0644"
end

template "#{site_dir}/protected/yiic.php" do
    source "yiic.php.erb"
    mode "0644"
end

template "#{site_dir}/protected/config/local.php" do
    source "yii-local.php.erb"
    mode "0644"
end

template "#{site_dir}/protected/config/main.php" do
    source "yii-main.php.erb"
    mode "0644"
end

template "#{site_dir}/protected/config/db.json" do
    source 'yii-db.json.erb'
    mode '0644'
end

template "#{site_dir}/protected/scripts/set_env.sh" do
    source 'set_env.sh.erb'
    mode '0644'
end

# For Elastic Search
template "#{site_dir}/protected/config/es.json" do
    source "es.json.erb"
    mode 0644
end

template "#{site_dir}/protected/scripts/update_links.sh" do
    source "update_links.sh.erb"
end

template "#{site_dir}/files/html/help.html" do
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
file '#{site_dir}//protected/yiic' do
  mode '0755'
  action :touch
end


execute 'Build css' do
    command "#{site_dir}/protected/yiic lesscompiler"
    cwd "#{site_dir}/protected"
    group 'root'
    user 'root'
end
