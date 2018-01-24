#
# latest version available at:
# https://github.com/shyiko/docker-vm
#
Vagrant.configure("2") do |config|
  # For box definition see https://github.com/phusion/open-vagrant-boxes
  config.vm.box = "phusion-open-ubuntu-14.04-amd64"
  config.vm.box_url = "https://oss-binaries.phusionpassenger.com/vagrant/boxes/latest/ubuntu-14.04-amd64-vbox.box"
  config.vm.synced_folder ".", "/vagrant"
  # Allocate IP address to Ubuntu VM
  config.vm.network "private_network", ip: "192.168.42.10"

  FileUtils.mkpath("./protected/runtime")
  FileUtils.chmod_R 0777, ["./protected/runtime"]
  FileUtils.mkpath("./assets")
  FileUtils.chmod_R 0777, ["./assets"]

  # Install & start docker daemon
  config.vm.provision "docker"
  # Install docker-compose
  config.vm.provision :shell, inline: <<-EOT
    sudo curl -L https://github.com/docker/compose/releases/download/1.17.0/docker-compose-`uname -s`-`uname -m` -o /usr/local/bin/docker-compose
    sudo chmod +x /usr/local/bin/docker-compose
  EOT
  # Make docker daemon accessible from the host OS (port 2376)
  config.vm.provision :shell, inline: <<-EOT
    echo 'DOCKER_OPTS="-H unix:// -H tcp://0.0.0.0:2376 ${DOCKER_OPTS}"' >> /etc/default/docker
    service docker restart
  EOT
  # Install git and nodejs which provides npm
  config.vm.provision :shell, inline: <<-EOT
    echo "Import new PuppetLabs GPG key"
    gpg --keyserver pgp.mit.edu --recv-key 7F438280EF8D349F
    echo "Add PuppetLabs GPG key"
    gpg -a --export EF8D349F | sudo apt-key add -
    echo "Add NodeSource Personal Package Archive and install nodejs"
    curl -sL https://deb.nodesource.com/setup_6.x | sudo -E bash - && sudo apt-get update && sudo apt-get -y install nodejs
    echo "Install git"
    apt-get -y install git
    echo "Install PHP"
    apt-get -y install php5-fpm php5 php5-cli
  EOT

  config.vm.provider "virtualbox" do |v|
    # Unless synced_folder's nfs_udp is set to false (which slows things
    # down considerably - up to 50%) DO NOT change --nictype2 to virtio
    # (otherwise writes may freeze)
    v.customize ["modifyvm", :id, "--nictype1", "virtio" ]
    v.customize ["modifyvm", :id, "--natdnshostresolver1", "on"]
    v.customize ["modifyvm", :id, "--natdnsproxy1", "on"]
    v.customize ["modifyvm", :id, "--memory", "2048"]
  end

  # Install Chef-Solo
    config.vm.provision :shell, inline: <<-EOT
      curl -LO https://omnitruck.chef.io/install.sh && sudo bash ./install.sh -v 12.5.1 && rm install.sh
    EOT

  # Enable provisioning with chef solo
  config.vm.provision :chef_solo do |chef|
    # Specifying cookbooks path
    chef.cookbooks_path = [
      "chef/site-cookbooks",
      "chef/chef-cookbooks",
    ]
    chef.environments_path = 'chef/environments'

    ####################################################
    #### Set server environment: development or aws ####
    ####################################################
    chef.environment = "docker"

    # Add Chef recipes
    chef.add_recipe "docker"

    # You may also specify custom JSON attributes:
#     chef.json = {
#       :gigadb_box => ENV['GIGADB_BOX'],
#       :environment => "development",
#       :docker => {
#         :server_names => ["localhost"],
#         :root_dir => "/vagrant",
#         :site_dir => "/vagrant",
#         :log_dir => "/vagrant/logs",
#         :yii_path => "./yii-1.1.10/framework/yii.php",
#       },
#       :nginx => {
#         :version => :latest,
#       },
#       :postgresql => {
#         :version => '9.1',
#         :repo_version => '9.1',
#         #:dir => '/var/lib/pgsql/9.1/data',
#       },
#       :elasticsearch => {
#         :version => '1.3.4',
#       },
#       :java => {
#         #:install_flavor => 'oracle',
#         :jdk_version => '7',
#         :oracle => {
#           "accept_oracle_download_terms" => true,
#         },
#       },
#     }

    # Additional chef settings to put in solo.rb
    chef.custom_config_path = "Vagrantfile.chef"
  end
end