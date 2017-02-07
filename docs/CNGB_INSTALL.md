# Installation of GigaDB for CNGB

To install GigaDB on a RedHat-based CNGB Aliyun server, the website is 
installed locally on the same machine where the source code for GigaDB 
has been downloaded on. Start this process by logging into the Aliyun 
server:
```bash
$ ssh user@server.ip.address
```

Install [chef-solo](https://docs.chef.io/ctl_chef_solo.html). This 
command line tool executes chef-client in a way that does not require 
the Chef server in order to converge cookbooks. chef-solo uses 
chef-client’s Chef local mode, and does not support the following 
functionality present in chef-client / server configurations.
```bash
$ sudo curl -L https://www.opscode.com/chef/install.sh | bash
```

Install git:
```bash
$ sudo yum install git
```

Change directory to your home directory and download/clone the 
gigadb-website github source code repository:
```bash
$ cd ~
$ git clone https://github.com/gigascience/gigadb-website.git
# Change to cngb branch
$ cd gigadb-website
$ git checkout cngb
# Download chef cookbooks
$ git submodule init
$ git submodule update
```

Add a production.json file into the `~/gigadb-website/chef/environments`
directory which will be provided by the technical staff at GigaScience.

Create a `solo.rb` file in the `~/gigadb-website/chef` directory using
the content below:
```bash
add_formatter :min
checksum_path '/home/centos/gigadb-website/chef/checksums'
cookbook_path ['/home/centos/gigadb-website/chef/chef-cookbooks', '/home/centos/gigadb-website/chef/site-cookbooks']
data_bag_path '/home/centos/gigadb-website/chef/data_bags'
environment 'production'
environment_path '/home/centos/gigadb-website/chef/environments' 
file_backup_path '/home/centos/gigadb-website/chef/backup' 
file_cache_path '/home/centos/gigadb-website/chef/cache' 
json_attribs nil
lockfile '/home/centos/gigadb-website/chef/chef.pid' 
log_level :debug
log_location STDOUT
node_name 'gigadb.genomics.cn'
rest_timeout 300
role_path '/home/centos/gigadb-website/chef/roles' 
sandbox_path 'path_to_folder'
solo false
syntax_check_cache_path
umask 0022
verbose_logging nil
```

Create a node.json in the `~/gigadb-website/chef/nodes` directory which
contains the following:
```bash
{
  "run_list": [
    "recipe[cngb]",
    "recipe[gigadb]" 
  ],
  "environment": "production" 
}
```

Change directory to where your gigadb-website repo folder is and use 
chef-solo to install the GigaDB website:
```bash
$ cd ~
$ sudo chef-solo -c ~/gigadb-website/chef/solo.rb -j ~/gigadb-website/chef/nodes/node.json
```


