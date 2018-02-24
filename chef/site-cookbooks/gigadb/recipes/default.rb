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
include_recipe "php::module_gd"
include_recipe "php::module_mbstring"
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



include_recipe "gigadb::google_analytics_setup"

########################################
#### Platform specific provisioning ####
########################################

case node[:platform_family]
when 'rhel'
    include_recipe 'gigadb::redhat'
when 'debian'
    include_recipe 'gigadb::debian'
end

#########################
#### Directory admin ####
#########################

yii_framework node[:yii][:version] do
    symlink "#{site_dir}/../yii"
end

####################################
#### Set up PostgreSQL database ####
####################################

# If provisioning by Chef-Solo, need to manually add SQL file
directory '/vagrant/sql' do
  owner 'root'
  group 'root'
  mode '0755'
  action :create
end

cookbook_file '/vagrant/sql/gigadb_testdata.sql' do
    not_if { ::File.exist?('/vagrant/sql/gigadb_testdata.sql') }
    source 'sql/gigadb_testdata.sql'
    owner 'root'
    group 'root'
    mode '0755'
    action :create
end

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
            # Might need to drop database first or foreign key constraints stop database restoration
            export PGPASSWORD='#{password}'; psql -U #{db_user} -h localhost gigadb < #{sql_script}
        EOH
    end

    # creating the test database to run the unit tests
    test_user = "test"
    postgresql_user test_user do
        password  "test"
    end

    postgresql_database "gigadb_test" do
        owner test_user
    end

    bash 'restore test database' do
        db_user = "test"
        password = "test"
        sql_script = db[:sql_script]

        code <<-EOH
            # Might need to drop database first or foreign key constraints stop database restoration
            export PGPASSWORD='#{password}'; psql -U #{db_user} -h localhost gigadb_test < "/vagrant/sql/gigadb_tables.sql"
        EOH
    end



end


###############################################################
#### Copy website files into node if not present in server ####
###############################################################

# For provisioning by Chef-Solo where website folders are not
# automatically synced
remote_directory '/vagrant/protected' do
  source 'protected'
  owner 'root'
  group 'root'
  mode '0755'
  action :create
  not_if do ::File.exists?('/vagrant/protected/models') end
end

remote_directory '/vagrant/css' do
  source 'css'
  owner 'root'
  group 'root'
  mode '0755'
  action :create
  not_if do ::File.exists?('/vagrant/css') end
end

remote_directory '/vagrant/docs' do
  source 'docs'
  owner 'root'
  group 'root'
  mode '0755'
  action :create
  not_if do ::File.exists?('/vagrant/docs') end
end

remote_directory '/vagrant/Elastica' do
  source 'docs'
  owner 'root'
  group 'root'
  mode '0755'
  action :create
  not_if do ::File.exists?('/vagrant/Elastica') end
end

remote_directory '/vagrant/files' do
  source 'files'
  owner 'root'
  group 'root'
  mode '0755'
  action :create
  not_if do ::File.exists?('/vagrant/files') end
end

remote_directory '/vagrant/google-api-php-client' do
  source 'google-api-php-client'
  owner 'root'
  group 'root'
  mode '0755'
  action :create
  not_if do ::File.exists?('/vagrant/google-api-php-client') end
end

remote_directory '/vagrant/images' do
  source 'images'
  owner 'root'
  group 'root'
  mode '0755'
  action :create
  not_if do ::File.exists?('/vagrant/images') end
end

remote_directory '/vagrant/js' do
  source 'js'
  owner 'root'
  group 'root'
  mode '0755'
  action :create
  not_if do ::File.exists?('/vagrant/js') end
end

remote_directory '/vagrant/less' do
  source 'less'
  owner 'root'
  group 'root'
  mode '0755'
  action :create
  not_if do ::File.exists?('/vagrant/less') end
end

remote_directory '/vagrant/sphinx' do
  source 'sphinx'
  owner 'root'
  group 'root'
  mode '0755'
  action :create
  not_if do ::File.exists?('/vagrant/sphinx') end
end

remote_directory '/vagrant/themes' do
  source 'themes'
  owner 'root'
  group 'root'
  mode '0755'
  action :create
  not_if do ::File.exists?('/vagrant/themes') end
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


######################
#### Python stuff ####
######################

# These Python packages are intended for file checking functionality
# but this website code is not currently used

# Install lxml as an external parser for beautifulsoup
# For some reason, installing via pip fails (some C compile error) so
# we're resorting to the distro-provided package...
#package 'python-lxml' do
#    action :install
#end

#python_virtualenv python_env do
#    owner app_user
#    action :create
    # TODO: redhat prod server uses 2.6 - let's uncomment the following
    # line if something blows up
    #interpreter 'python2.6'
#end

# Install biopython and beautifulsoup4 packages
#node[:gigadb][:python][:packages].each do |pkg|
#    python_pip pkg do
#        action :install
#    end
#end

#bash "install schemup" do
#    cwd build_dir
#    code <<-EOH
#        . #{python_env}/bin/activate
#        git clone https://github.com/brendonh/schemup.git
#        cd schemup
#        git fetch
#        git checkout #{node[:gigadb][:python][:schemup][:version]}
#        pip install .
#    EOH
#end

#bash 'install python packages' do
#    code <<-EOH
#        . #{python_env}/bin/activate
#        pip install -r #{site_dir}/protected/schema/requirements.txt
#    EOH
#end


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
file '/vagrant/protected/yiic' do
  mode '0755'
  action :touch
end


execute 'Build css' do
    command "/vagrant/protected/yiic lesscompiler"
    cwd "/vagrant/protected"
    group 'root'
    user 'root'
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
