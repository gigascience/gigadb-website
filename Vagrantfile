#
# latest version available at:
# https://github.com/shyiko/docker-vm
#
Vagrant.configure("2") do |config|
  # For box definition see https://github.com/phusion/open-vagrant-boxes
  config.vm.box = "phusion-open-ubuntu-14.04-amd64"
  config.vm.box_url = "https://oss-binaries.phusionpassenger.com/vagrant/boxes/latest/ubuntu-14.04-amd64-vbox.box"
  # Ubuntu VM will have IP address below
  config.vm.network "private_network", ip: "192.168.42.10"
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

  config.vm.synced_folder ".", "/vagrant"
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
end