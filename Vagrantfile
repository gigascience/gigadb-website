# -*- mode: ruby -*-
# vi: set ft=ruby :

FTP_SERVER_SCRIPT = <<EOF.freeze
echo "Preparing FTP server..."
EOF

if ENV['GIGADB_BOX'] == 'ubuntu'
  # Use trusty32 box which is Ubuntu-14.04
  box = "trusty32"
  box_url = "https://atlas.hashicorp.com/ubuntu/boxes/trusty64/versions/14.04/providers/virtualbox.box"
elsif ENV['GIGADB_BOX'] == 'aws'
  box = "dummy"
  box_url = "https://github.com/mitchellh/vagrant-aws/raw/master/dummy.box"
else
  box = "nrel/CentOS-6.7-x86_64"
  box_url = "https://atlas.hashicorp.com/nrel/boxes/CentOS-6.7-x86_64/versions/1.0.0/providers/virtualbox.box"
end

def set_hostname(server)
  server.vm.provision 'shell', inline: "hostname #{server.vm.hostname}"
end

Vagrant.configure(2) do |config|
  # Cache packages to reduce provisioning time
  if Vagrant.has_plugin?("vagrant-cachier")
    # Share cached packages between instances of the same base box
    config.cache.scope = :box
  end

  config.vm.define 'gigadb-website' do |gigadb|
  	gigadb.vm.box = box
  	gigadb.vm.box_url = box_url
  	gigadb.vm.hostname = 'gigadb-server.test'
  	set_hostname(gigadb)

    # Forward ports from guest to host to allow outside computers to access VM
  	gigadb.vm.network "forwarded_port", guest: 80, host: 9170
  	gigadb.vm.network "forwarded_port", guest: 5432, host: 9171
  	# CentOS-specific Vagrant configuration to allow Yii assets folder
    # to be world-readable
    if ENV['GIGADB_BOX'] == 'aws' # For AWS instance
      gigadb.vm.synced_folder ".", "/vagrant", rsync__args: ["--verbose", "--archive", "--delete", "-z"]
      FileUtils.mkpath("./assets")
      FileUtils.chmod_R 0777, ["./assets"]
    else
      gigadb.vm.synced_folder ".", "/vagrant"
      FileUtils.mkpath("./assets")
      gigadb.vm.synced_folder "./assets/", "/vagrant/assets",
        :mount_options => ["dmode=777,fmode=777"]
    end
  	FileUtils.mkpath("./protected/runtime")
  	FileUtils.chmod_R 0777, ["./protected/runtime"]
    FileUtils.mkpath("./images/tempcaptcha")
    FileUtils.chmod_R 0777, ["./images/tempcaptcha"]    
  	FileUtils.mkpath("./giga_cache")
  	FileUtils.chmod_R 0777, ["./giga_cache"]
  	FileUtils.mkpath("./logs")
  	FileUtils.chmod_R 0777, ["./logs"]

    ####################
    #### VirtualBox ####
    ####################
    gigadb.vm.provider :virtualbox do |vb|
	  vb.customize ["setextradata", :id, "VBoxInternal2/SharedFoldersEnableSymlinksCreate//vagrant","1"]

	  # Share an additional folder to the guest VM
	  apt_cache = "./apt-cache"
	  if File.directory?(apt_cache)
	    config.vm.share_folder "apt_cache", "/var/cache/apt/archives", apt_cache
	  end
    end

    #############
    #### AWS ####
    #############
    gigadb.vm.provider :aws do |aws, override|
      aws.access_key_id = ENV['AWS_ACCESS_KEY_ID']
      aws.secret_access_key = ENV['AWS_SECRET_ACCESS_KEY']
      aws.keypair_name = ENV['AWS_KEYPAIR_NAME']
      # aws.ami = "ami-1bfa2b78" # selinux disabled
      aws.ami = "ami-b85e86db" # selinux on
      aws.region = ENV['AWS_DEFAULT_REGION']
      aws.instance_type = "t2.micro"
      aws.tags = {
        'Name' => 'gigadb-website-vagrant-test',
        'Deployment' => 'test',
      }
      aws.security_groups = ENV['AWS_SECURITY_GROUPS']

      override.ssh.username = "centos"
      override.ssh.private_key_path = ENV['AWS_SSH_PRIVATE_KEY_PATH']
      override.nfs.functional = false
    end

    # Enable provisioning with chef solo
    gigadb.vm.provision :chef_solo do |chef|
      chef.cookbooks_path = [
        "chef/site-cookbooks",
        "chef/chef-cookbooks",
      ]
      chef.environments_path = 'chef/environments'
      ############################################################
      #### Set server environment: development, aws or docker ####
      ############################################################
      chef.environment = "development"

      chef.data_bags_path = 'chef/data_bags'
      if File.exist?('chef/.chef/encrypted_data_bag_secret')
	    chef.encrypted_data_bag_secret_key_path = 'chef/environments/encrypted_data_bag_secret'
	  end

      # Chef recipes to be used for provisioning
      if ENV['GIGADB_BOX'] == 'aws'
        chef.add_recipe "aws"
      else
        chef.add_recipe "vagrant"
      end

      # Additional chef settings to put in solo.rb
      chef.custom_config_path = "Vagrantfile.chef"
    end
  end

  # GigaDB's FTP-server
  if ENV['DEPLOY_GIGADB_FTP'] == 'true'
    config.vm.define 'ftp-server' do |ftp|
	  ftp.vm.box = 'nrel/CentOS-6.7-x86_64'
	  # ftp.vm.box_version = '2.2.9'
	  ftp.vm.hostname = 'ftp-server.test'
	  ftp.vm.network 'private_network', ip: '10.1.1.33'
	  ftp.vm.provision 'shell', inline: FTP_SERVER_SCRIPT.dup
	  set_hostname(ftp)

	  ftp.vm.provision :chef_solo do |ftp_chef|
	    ftp_chef.cookbooks_path = [
	      "chef/site-cookbooks",
	      "chef/chef-cookbooks",
	    ]
	    ftp_chef.environments_path = 'chef/environments'

	    # Set server environment: development
	    ftp_chef.environment = "development"
	    ftp_chef.add_recipe "fileserver"
	  end

	  ftp.vm.provider 'virtualbox' do |v|
	    v.memory = 2048
	    v.cpus = 2
	  end
    end
  end
end
