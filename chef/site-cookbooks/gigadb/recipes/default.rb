#
# Cookbook Name:: gigadb
# Recipe:: default
#
# Copyright 2014, Cogini
#
# All rights reserved - Do Not Redistribute
#

case node[:platform_family]
when 'rhel'
    include_recipe 'gigadb::redhat'
when 'debian'
    include_recipe 'gigadb::debian'
end

# Install lxml as an external parser for beautifulsoup
# For some reason, installing via pip fails (some C compile error) so we're
# resorting to the distro-provided package...
package 'python-lxml' do
    action :install
end

include_recipe "php::fpm"
include_recipe "php::module_pgsql"
include_recipe "nginx"
include_recipe "python"
include_recipe 'nodejs'
include_recipe "elasticsearch"

python_env = node[:gigadb][:python][:virtualenv]
build_dir = node[:gigadb][:python][:build_dir]
log_dir = node[:gigadb][:log_dir]
site_dir = node[:gigadb][:site_dir]
app_user = node[:gigadb][:app_user]


yii_framework node[:yii][:version] do
    symlink "#{node[:gigadb][:site_dir]}/../yii"
end

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
end

user app_user do
    home "/home/#{app_user}"
    shell '/bin/bash'
    supports :manage_home => true
    action :create
end


template "/etc/nginx/sites-available/gigadb" do
    source "nginx-gigadb.erb"
    mode "0644"
end

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

template "#{site_dir}/protected/config/db.json" do
    source 'yii-db.json.erb'
    mode '0644'
end

template "#{site_dir}/protected/scripts/set_env.sh" do
    source 'set_env.sh.erb'
    mode '0644'
end

execute "#{site_dir}/protected/scripts/init_perms.sh"

nginx_site "gigadb" do
    action :enable
end

[build_dir, python_env, log_dir].each do |dir|
    directory dir do
        owner app_user
        action :create
        recursive true
    end
end

python_virtualenv python_env do
    owner app_user
    action :create
    # TODO: redhat prod server uses 2.6 - let's uncomment the following line if
    # something blows up
    #interpreter 'python2.6'
end

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

## Elastic Search Setup
template "#{site_dir}/protected/config/es.json" do
    source "es.json.erb"
    mode 0644
end

bash 'install python packages' do
    code <<-EOH
        . #{python_env}/bin/activate
        pip install -r #{site_dir}/protected/schema/requirements.txt
    EOH
end

# Compile less files
execute 'npm install -g less'
if node[:environment] != 'vagrant'
    css_user = app_user
else
    css_user = 'vagrant'
end
execute 'Build css' do
    command "#{site_dir}/protected/yiic lesscompiler"
    cwd "#{site_dir}/protected"
    group css_user
    user css_user
end

# Remove default dummy nginx sites
['default.conf', 'example_ssl.conf'].each do |fname|
    file "/etc/nginx/conf.d/#{fname}" do
        action :delete
    end
end
service 'nginx' do
    action :restart
end

dirs = %w{
  assets
  protected/runtime
  giga_cache
}

dirs.each do |component|
    the_dir = "#{site_dir}/#{component}"

    bash 'setup permissions' do
        code <<-EOH
            mkdir -p #{the_dir}
            chown -R www-data:#{app_user} #{the_dir}
            chmod -R ug+rwX #{the_dir}
            find #{the_dir} -type d | xargs chmod g+x
        EOH
    end
end
