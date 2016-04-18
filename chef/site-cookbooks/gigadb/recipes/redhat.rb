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
