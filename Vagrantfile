# -*- mode: ruby -*-
# vi: set ft=ruby :

if ENV['GIGADB_BOX'] == 'centos'
  box = "centos6-64"
  box_url = "http://boxes.cogini.com/centos6-64.box"
else
  box = "lucid32"
  box_url = "http://files.vagrantup.com/lucid32.box"
end

Vagrant.configure(2) do |config|

  # Every Vagrant virtual environment requires a box to build off of.
  config.vm.box = box

  # The url from where the 'config.vm.box' box will be fetched if it
  # doesn't already exist on the user's system.
  config.vm.box_url = box_url

  # Forward a port from the guest to the host, which allows for outside
  # computers to access the VM, whereas host only networking does not.
  config.vm.network "forwarded_port", guest: 80, host: 9170
  config.vm.network "forwarded_port", guest: 5432, host: 9171
  config.vm.network "forwarded_port", guest: 22, host: 2224

  # Share an additional folder to the guest VM. The first argument is
  # an identifier, the second is the path on the guest to mount the
  # folder, and the third is the path on the host to the actual folder.
  # config.vm.share_folder "v-data", "/vagrant_data", "../data"
  apt_cache = "./apt-cache"
  if File.directory?(apt_cache)
    config.vm.share_folder "apt_cache", "/var/cache/apt/archives", apt_cache
  end

  config.vm.provider :virtualbox do |vb|
      vb.customize ["setextradata", :id, "VBoxInternal2/SharedFoldersEnableSymlinksCreate//vagrant","1"]
  end

  FileUtils.mkpath("./protected/runtime")
  FileUtils.chmod_R 0777, ["./protected/runtime"]

  # Enable provisioning with chef solo, specifying a cookbooks path, roles
  # path, and data_bags path (all relative to this Vagrantfile), and adding
  # some recipes and/or roles.
  #
  config.vm.provision :chef_solo do |chef|
    chef.cookbooks_path = [
      "chef/site-cookbooks",
      "chef/chef-cookbooks",
    ]
    chef.add_recipe "vagrant"

    # You may also specify custom JSON attributes:
    chef.json = {
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
        :dir => '/var/lib/pgsql/9.1/data',
      },
      :elasticsearch => {
        :version => '1.3.4',
      },
      :java => {
        :install_flavor => 'oracle',
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
