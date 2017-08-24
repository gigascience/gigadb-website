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

include_recipe "yum"
include_recipe "yum::remi"
include_recipe "yum::epel"
include_recipe "python"

# Install a Redis server used by the Preview functionality to track work stage
# and status and preview url between frontend and backend
package 'redis' do
    action :upgrade
end

cookbook_file "/etc/redis.conf" do
    source "redis.conf"
    mode "0644"
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




# iptables not required for default development environment
service 'iptables' do
    action [:disable, :stop]
end
