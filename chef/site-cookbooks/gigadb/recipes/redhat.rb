#
# Cookbook Name:: gigadb
# Recipe:: redhat
#
# Copyright 2014, Cogini
#
# All rights reserved - Do Not Redistribute
#

%w{ postgresql-devel }.each do |pkg|
    package pkg do
        action :install
    end
end

# Remove PostgreSQL version 8.4 packages which have been installed as
# part of the @base packages
package "postgresql" do
  action :remove
end

package "postgresql-devel" do
  action :remove
end

package "postgresql-libs" do
  action :remove
end