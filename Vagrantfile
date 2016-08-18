# -*- mode: ruby -*-
# vi: set ft=ruby :

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

Vagrant.configure(2) do |config|

  # Vagrant virtual environment box to build off of.
  config.vm.box = box

  # The url from where the 'config.vm.box' box will be fetched if it
  # doesn't already exist on the user's system.
  config.vm.box_url = box_url

  # Cache packages to reduce provisioning time
  if Vagrant.has_plugin?("vagrant-cachier")
    #Configure cached packages to be shared between instances of the same base box
    config.cache.scope = :box
  end

  # Forward ports from guest to host, which allows for outside computers
  # to access VM, whereas host only networking does not.va
  config.vm.network "forwarded_port", guest: 80, host: 9170
  config.vm.network "forwarded_port", guest: 5432, host: 9171
  config.vm.network "forwarded_port", guest: 22, host: 2224

  config.vm.synced_folder ".", "/vagrant"

  FileUtils.mkpath("./protected/runtime")
  FileUtils.chmod_R 0777, ["./protected/runtime"]

  FileUtils.mkpath("./giga_cache")
  FileUtils.chmod_R 0777, ["./giga_cache"]

  # CentOS-specific Vagrant configuration to allow Yii assets folder
  # to be world-readable.
  if ENV['GIGADB_BOX'] != 'ubuntu' # For CentOS VM and AWS instance
    FileUtils.mkpath("./assets")
    config.vm.synced_folder "./assets/", "/vagrant/assets",
      :mount_options => ["dmode=777,fmode=777"]
  end

  ####################
  #### VirtualBox ####
  ####################
  config.vm.provider :virtualbox do |vb|
    vb.customize ["setextradata", :id, "VBoxInternal2/SharedFoldersEnableSymlinksCreate//vagrant","1"]

    # Share an additional folder to the guest VM. The first argument is
    # an identifier, the second is the path on the guest to mount the
    # folder, and the third is the path on the host to the actual folder.
    # config.vm.share_folder "v-data", "/vagrant_data", "../data"
    apt_cache = "./apt-cache"
    if File.directory?(apt_cache)
      config.vm.share_folder "apt_cache", "/var/cache/apt/archives", apt_cache
    end
  end

  #############
  #### AWS ####
  #############
  config.vm.provider :aws do |aws, override|
    aws.access_key_id = ENV['AWS_ACCESS_KEY_ID']
    aws.secret_access_key = ENV['AWS_SECRET_ACCESS_KEY']
    aws.keypair_name = ENV['AWS_KEYPAIR_NAME']
    aws.ami = "ami-1bfa2b78"
    aws.region = ENV['AWS_DEFAULT_REGION']
    aws.instance_type = "t2.micro"
    aws.tags = {
      'Name' => 'gigadb-website',
      'Deployment' => 'test',
    }
    aws.security_groups = ENV['AWS_SECURITY_GROUPS']

    override.ssh.username = "centos"
    override.ssh.private_key_path = ENV['AWS_SSH_PRIVATE_KEY_PATH']
  end

  # Enable provisioning with chef solo, specifying a cookbooks path, roles
  # path, and data_bags path (all relative to this Vagrantfile), and adding
  # some recipes and/or roles.
  config.vm.provision :chef_solo do |chef|
    chef.cookbooks_path = [
      "chef/site-cookbooks",
      "chef/chef-cookbooks",
    ]
    chef.environments_path = 'chef/environments'

    ############################################################
    #### Need to set server environment: development or aws ####
    ############################################################
    chef.environment = "aws_test"

    chef.add_recipe "vagrant"
    if ENV['GIGADB_BOX'] == 'aws'
        chef.add_recipe "aws"
    end

    # You may also specify custom JSON attributes:
    chef.json = {
      :gigadb_box => ENV['GIGADB_BOX'],
      :environment => "vagrant",
      :gigadb => {
        :server_names => ["localhost"],
        :root_dir => "/vagrant",
        :site_dir => "/vagrant",
        :log_dir => "/vagrant/logs",
        :yii_path => "/opt/yii-1.1.10/framework/yii.php",
        :db => {
          :user => "gigadb",
          :password => "vagrant",
          :database => "gigadb",
          :host => "localhost",
        }
      },
      :nginx => {
        :version => :latest,
      },
      :postgresql => {
        :version => '9.1',
        #:dir => '/var/lib/pgsql/9.1/data',
      },
      :elasticsearch => {
        :version => '1.3.4',
      },
      :java => {
        #:install_flavor => 'oracle',
        :jdk_version => '7',
        :oracle => {
           "accept_oracle_download_terms" => true,
        },
      },
    }

    # Additional chef settings to put in solo.rb
    chef.custom_config_path = "Vagrantfile.chef"

  end

end
