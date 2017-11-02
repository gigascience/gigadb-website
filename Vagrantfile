# -*- mode: ruby -*-
# vi: set ft=ruby :

# Vagrantfile API/syntax version. Don't touch unless you know what you're doing!
VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.box = "opscode-centos65"
  config.vm.box_url = "http://opscode-vm-bento.s3.amazonaws.com/vagrant/virtualbox/opscode_centos-6.5_chef-provisionerless.box"

  config.vm.provider :virtualbox do |vb|
    vb.name = "docker"
    vb.customize ["modifyvm", :id, "--memory", 1024]
  end

  config.vm.network :private_network, ip: "192.168.33.10"
#   config.vm.network :forwarded_port, guest: 4243, host: 4243
	config.vm.network :forwarded_port, guest: 2123, host: 2123

  config.vm.synced_folder ".", "/vagrant"

  config.vm.provision :shell, :inline => <<-EOT
    #
    # Turn iptables off
    #
    /sbin/iptables -F
    /sbin/service iptables stop
    /sbin/chkconfig iptables off
    #
    # Upgrade device-mapper-libs
    #
    yum upgrade -y device-mapper-libs
    #
    # Install packages from yum repository
    #
    rpm -ivh http://ftp.riken.jp/Linux/fedora/epel/6/i386/epel-release-6-8.noarch.rpm
    yum install -y vim-enhanced telnet
    #
    # Install Docker
    #
    yum install -y docker-io
    sed -i 's,^other_args=.*$,other_args="-H tcp://0.0.0.0:4243 -H unix:// --dns 8.8.8.8",g' /etc/sysconfig/docker
    chkconfig docker on
    service docker restart
  EOT
end