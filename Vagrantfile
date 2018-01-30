Vagrant.configure("2") do |config|
  config.vm.box = "gigasci/ubuntu-16.04-amd64.box"
  config.vm.box_version = "2018.01.29"
  # Sync gigadb-website folder to /vagrant dir on VM
  config.vm.synced_folder ".", "/vagrant"
  # Allow host to access docker daemon
  config.vm.network "forwarded_port", guest: 2376, host: 9172
  # Allocate IP address to Ubuntu VM
  config.vm.network "private_network", ip: "192.168.42.10"

  # Required Yii folders
  FileUtils.mkpath("./protected/runtime")
  FileUtils.chmod_R 0777, ["./protected/runtime"]
  FileUtils.mkpath("./assets")
  FileUtils.chmod_R 0777, ["./assets"]

  # Access docker daemon from host OS via port 9172 forwarded to port 2376 in
  # container
  config.vm.provision :shell, inline: <<-EOT
    sed -i 's|^ExecStart=/usr/bin/dockerd -H fd://|ExecStart=/usr/bin/dockerd -H fd:// -H tcp://0.0.0.0:2376|' /lib/systemd/system/docker.service
    systemctl daemon-reload
    systemctl restart docker.service
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
    chef.environment = "development"

    # Add Chef recipes
    chef.add_recipe "docker"

    # Additional chef settings to put in solo.rb
    chef.custom_config_path = "Vagrantfile.chef"
  end
end