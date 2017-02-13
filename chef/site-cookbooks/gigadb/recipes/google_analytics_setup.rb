#
# Cookbook Name:: google_analytics_setup
# Recipe:: default
#
# Copyright 2017, GigaScience
#
# All rights reserved - Do Not Redistribute
#

require 'rubygems'
require 'chef/encrypted_data_bag_item'
require 'json'
require "base64"

# Locates GigaDB in /vagrant directory
site_dir = node[:gigadb][:site_dir]
# Location of certs folder
certs_dir = "#{site_dir}/files/certs"
puts "certs_dir: " + certs_dir
directory certs_dir do
  owner 'root'
  group 'root'
  mode '0755'
  action :create
end

# Vagrant will copy encrypted_data_bag_secret from the host to
# /tmp/vagrant-chef/encrypted_data_bag_secret_key during vagrant up
if File.exist?('/tmp/vagrant-chef/encrypted_data_bag_secret_key')
	secret = Chef::EncryptedDataBagItem.load_secret("/tmp/vagrant-chef/encrypted_data_bag_secret_key")

	# Decrypt secrets->analytics_p12 data bag using secret key
	p12 = Chef::EncryptedDataBagItem.load("secrets", "analytics_p12", secret)

	p12_filename = p12['filename']
	puts "p12_filename: " + p12_filename

	# Decode base 64 content
	decoded = Base64.strict_decode64(p12['base64'])

	template "#{certs_dir}/#{p12_filename}" do
	  owner 'root'
	  mode  '0644'
	  source 'google_p12.erb'
	  variables(
		:p12_file => decoded,
	  )
	end
else
	puts "Secret key for encrypted data bag does not exist - not creating Google P12 key file!!!"
end
