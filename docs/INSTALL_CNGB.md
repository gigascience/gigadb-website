# Installation of GigaDB for CNGB

To install GigaDB on a RedHat-based CNGB Aliyun server, the website is 
installed locally on the same machine where the source code for GigaDB 
has been downloaded on. Start this process by logging into the Aliyun 
server:
```bash
# Replace user and server.ip.address
$ ssh user@server.ip.address
```

Install [chef-solo](https://docs.chef.io/ctl_chef_solo.html). This 
command line tool executes chef-client in a way that does not require 
the Chef server in order to converge cookbooks. chef-solo uses 
chef-client’s Chef local mode, and does not support the following 
functionality present in chef-client/server configurations.
```bash
$ wget https://www.opscode.com/chef/install.sh
--2017-02-08 03:41:23--  https://www.opscode.com/chef/install.sh
Resolving www.opscode.com... 54.186.31.111, 54.200.190.77, 54.244.32.246
Connecting to www.opscode.com|54.186.31.111|:443... connected.
HTTP request sent, awaiting response... 200 OK
Length: 20507 (20K) [application/x-sh]
Saving to: “install.sh”

100%[===========================================================================================================================>] 20,507      --.-K/s   in 0s      

2017-02-08 03:41:24 (294 MB/s) - “install.sh” saved [20507/20507]
# Enable install.sh to be executable
$ chmod a+x install.sh
$ sudo ./install.sh 
el 6 x86_64
Getting information for chef stable  for el...
downloading https://omnitruck-direct.chef.io/stable/chef/metadata?v=&p=el&pv=6&m=x86_64
  to file /tmp/install.sh.1631/metadata.txt
trying wget...
sha1    bf54e7f486c2b0077db62bfa48adecd7110df332
sha256  d97c3a2279366816cfbdb22916d0952b9da1627a1653b42d3ef71022619473e4
url     https://packages.chef.io/files/stable/chef/12.18.31/el/6/chef-12.18.31-1.el6.x86_64.rpm
version 12.18.31
downloaded metadata file looks valid...
downloading https://packages.chef.io/files/stable/chef/12.18.31/el/6/chef-12.18.31-1.el6.x86_64.rpm
  to file /tmp/install.sh.1631/chef-12.18.31-1.el6.x86_64.rpm
trying wget...
Comparing checksum with sha256sum...

WARNING WARNING WARNING WARNING WARNING WARNING WARNING WARNING WARNING

You are installing an omnibus package without a version pin.  If you are installing
on production servers via an automated process this is DANGEROUS and you will
be upgraded without warning on new releases, even to new major releases.
Letting the version float is only appropriate in desktop, test, development or
CI/CD environments.

WARNING WARNING WARNING WARNING WARNING WARNING WARNING WARNING WARNING

Installing chef 
installing with rpm...
warning: /tmp/install.sh.1631/chef-12.18.31-1.el6.x86_64.rpm: Header V4 DSA/SHA1 Signature, key ID 83ef826a: NOKEY
Preparing...                ########################################### [100%]
   1:chef                   ########################################### [100%]
Thank you for installing Chef!
```

Install git:
```bash
$ sudo yum install git
```

Download/clone the gigadb-website github source code repository into
your user directory:
```bash
$ git clone https://github.com/gigascience/gigadb-website.git
```

Change to the cngb branch in the gigadb-website and download Chef 
cookbooks:
```bash
$ cd gigadb-website
$ git checkout cngb
# Download chef cookbooks
$ git submodule init
$ git submodule update
```

Create a /vagrant directory and copy the contents of the
gigadb-website github source code repository into there:
```bash
$ sudo mkdir /vagrant
$ cd /vagrant
$ sudo cp -R $HOME/gigadb-website/* /vagrant
```

Add a production.json file into the `/vagrant/chef/environments`
directory. This file contains a number of variables required by the 
GigaDB website to function. The technical staff at GigaScience can
provide you with a production.json file.

Create a `solo.rb` file in the `/vagrant/chef` directory using
the content below.
```bash
add_formatter :min
checksum_path '/vagrant/chef/checksums'
cookbook_path ['/vagrant/chef/chef-cookbooks','/vagrant/chef/site-cookbooks']
data_bag_path '/home/centos/chef/data_bags'
environment 'production'
environment_path '/vagrant/chef/environments' 
file_backup_path '/vagrant/chef/backup' 
file_cache_path '/vagrant/chef/cache' 
json_attribs nil
lockfile '/vagrant/chef/chef.pid' 
log_level :debug
log_location STDOUT
node_name 'gigadb.genomics.cn'
rest_timeout 300
role_path '/vagrant/chef/roles' 
sandbox_path 'path_to_folder'
solo false
syntax_check_cache_path
umask 0022
verbose_logging nil
```

Create a `node.json` file in the `/vagrant/chef/nodes` directory 
which contains the following:
```json
{
  "run_list": [
    "recipe[cngb]",
    "recipe[gigadb]" 
  ],
  "environment": "production" 
}
```

Use chef-solo to install the GigaDB website on the server:
```bash
$ sudo chef-solo -c /vagrant/chef/solo.rb -j /vagrant/chef/nodes/node.json
```


