#
# Cookbook Name:: gigadb
# Recipe:: default
#
# Copyright 2014, Cogini
#
# All rights reserved - Do Not Redistribute
#

include_recipe "php::fpm"
include_recipe "php::module_pgsql"
include_recipe "nginx"
include_recipe "python"
include_recipe 'nodejs'
include_recipe "elasticsearch"

# Defined in gigadb attributes
python_env = node[:gigadb][:python][:virtualenv]
build_dir = node[:gigadb][:python][:build_dir]

# Defined in Vagrantfile
log_dir = node[:gigadb][:log_dir]
# Locates GigaDB in /vagrant directory
site_dir = node[:gigadb][:site_dir]
# Defines gigadb as the app_user
app_user = node[:gigadb][:app_user]

##############################
#### User and group admin ####
##############################

# Create gigadb user
user app_user do
    home "/home/#{app_user}"
    shell '/bin/bash'
    supports :manage_home => true
    action :create
end

# Create group for GigaDB admins
group 'gigadb-admin' do
    action :create
end

#########################
#### Directory admin ####
#########################

yii_framework node[:yii][:version] do
    symlink "#{site_dir}/../yii"
end

########################################
#### Platform specific provisioning ####
########################################

case node[:platform_family]
when 'rhel'
    include_recipe 'gigadb::redhat'
when 'debian'
    include_recipe 'gigadb::debian'
end

####################################
#### Set up PostgreSQL database ####
####################################

# Defined in Vagrantfile - provides database access details
db = node[:gigadb][:db]
if db[:host] == 'localhost'

    include_recipe 'postgresql::server'
    db_user = db[:user]

    postgresql_user db_user do
        password db[:password]
    end

    postgresql_database db[:database] do
        owner db_user
    end

    bash 'restore gigadb database' do
        db_user = db[:user]
        password = db[:password]
        sql_script = db[:sql_script]

        code <<-EOH
            export PGPASSWORD='#{password}'; psql -U #{db_user} -h localhost gigadb < #{sql_script}
        EOH
    end
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

######################
#### Python stuff ####
######################

# These Python packages are intended for file checking functionality
# but this website code is not currently used

# Install lxml as an external parser for beautifulsoup
# For some reason, installing via pip fails (some C compile error) so
# we're resorting to the distro-provided package...
package 'python-lxml' do
    action :install
end

python_virtualenv python_env do
    owner app_user
    action :create
    # TODO: redhat prod server uses 2.6 - let's uncomment the following
    # line if something blows up
    #interpreter 'python2.6'
end

# Install biopython and beautifulsoup4 packages
node[:gigadb][:python][:packages].each do |pkg|
    python_pip pkg do
        action :install
    end
end

bash "install schemup" do
    cwd build_dir
    code <<-EOH
        . #{python_env}/bin/activate
        git clone https://github.com/brendonh/schemup.git
        cd schemup
        git fetch
        git checkout #{node[:gigadb][:python][:schemup][:version]}
        pip install .
    EOH
end

bash 'install python packages' do
    code <<-EOH
        . #{python_env}/bin/activate
        pip install -r #{site_dir}/protected/schema/requirements.txt
    EOH
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

execute 'Build css' do
    command "#{site_dir}/protected/yiic lesscompiler"
    cwd "#{site_dir}/protected"
    group css_user
    user css_user
end

###############
#### nginx ####
###############

template "/etc/nginx/sites-available/gigadb" do
    source "nginx-gigadb.erb"
    mode "0644"
end

# Remove default dummy nginx sites
['default.conf', 'example_ssl.conf'].each do |fname|
    file "/etc/nginx/conf.d/#{fname}" do
        action :delete
    end
end

# Delete default nginx conf file
file "/etc/nginx/sites-available/default" do
  action :delete
end

# Delete link to default nginx conf file
link '/etc/nginx/sites-enabled/default' do
  action :delete
end

# Enable gigadb as a nginx website
nginx_site "gigadb" do
    action :enable
end

# Reload nginx configuration
service 'nginx' do
    action :reload
end