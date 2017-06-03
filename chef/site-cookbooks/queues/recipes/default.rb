#
# Cookbook Name:: queues
# Recipe:: default
#
# install job management servers
#
# Copyright 2016, GigaScience
#
# All rights reserved - Do Not Redistribute
#

# Defined in gigadb attributes
python_env = node[:gigadb][:python][:virtualenv]
build_dir = node[:gigadb][:python][:build_dir]


# Install a Redis server used by the Preview functionality to track work stage
# and status and preview url between frontend and backend
package 'redis' do
    action :upgrade
end

service 'redis' do
  start_command '/etc/init.d/redis start'
  stop_command '/etc/init.d/redis stop'
  status_command '/etc/init.d/redis status'
  supports [:start, :stop, :status]
  # starts the service if it's not running and enables it to start at system boot time
  action [:enable, :start]
end

# install the job queue system Beanstalkd that allows the web site to create
# and forward software tasks to the backbround workers scripts in the backend
package 'beanstalkd' do
    action :upgrade
end

service 'beanstalkd' do
  start_command '/etc/init.d/beanstalkd start'
  stop_command '/etc/init.d/beanstalkd stop'
  status_command '/etc/init.d/beanstalkd status'
  supports [:start, :stop, :status]
  # starts the service if it's not running and enables it to start at system boot time
  action [:enable, :start]
end


# install and configure supervisord that manages background workers scripts in the backend

# install the rpm to get the init.d scripts
package 'supervisor' do
    action :install
end

# install dependency for latest version of supervisord
python_pip 'meld3' do
    action :install
end

# install a non-buggy version of supervisord
python_pip 'supervisor' do
    version "3.3.1"
end


template "/etc/supervisord.conf" do
    source "supervisord.conf.erb"
    mode "0644"
end

service 'supervisord' do
  start_command '/etc/init.d/supervisord start'
  stop_command '/etc/init.d/supervisord stop'
  status_command '/etc/init.d/supervisord status'
  supports [:start, :stop, :status]
  # starts the service if it's not running and enables it to start at system boot time
  action [:enable, :start]
end
