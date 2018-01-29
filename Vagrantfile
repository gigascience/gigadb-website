#
# latest version available at:
# https://github.com/shyiko/docker-vm
#
Vagrant.configure("2") do |config|
  config.vm.box = "gigasci/ubuntu-16.04-amd64.box"
  config.vm.box_version = "2018.01.29"
  config.vm.synced_folder ".", "/vagrant"
  # Allocate IP address to Ubuntu VM
  config.vm.network "private_network", ip: "192.168.42.10"

  FileUtils.mkpath("./protected/runtime")
  FileUtils.chmod_R 0777, ["./protected/runtime"]
  FileUtils.mkpath("./assets")
  FileUtils.chmod_R 0777, ["./assets"]

  # Make docker daemon accessible from the host OS (port 2376)
  config.vm.provision :shell, inline: <<-EOT
    echo 'DOCKER_OPTS="-H unix:// -H tcp://0.0.0.0:2376 ${DOCKER_OPTS}"' >> /etc/default/docker
    service docker restart
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

  # Enable provisioning with chef solo
  config.vm.provision :chef_solo do |chef|
    # Specify cookbooks path
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