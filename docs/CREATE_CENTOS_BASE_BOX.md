# Creating a Centos base box for Vagrant

The live version of [GigaDB](http://gigadb.org) is currently running
on a machine with Centos 6 as its operating system. Due to this
reason, testing of new GigaDB code should be done with a
Centos 6 development environment using Vagrant. This requires the use
of a Centos base box. The creation of Centos 6 base box is described
in this document.

## Requirements

Download and install a copy of [VirtualBox](https://www.virtualbox.org)
from its [official download page](https://www.virtualbox.org/wiki/Downloads).
In addition, download and install [Vagrant](https://www.vagrantup.com).
Since we will be creating a Centos 6 base box, download the minimal
x86_64 version of Centos 6.7, e.g. from [http://centos.uhost.hk/6.7/isos/x86_64/CentOS-6.7-x86_64-minimal.iso](http://centos.uhost.hk/6.7/isos/x86_64/CentOS-6.7-x86_64-minimal.iso).

## Creating the virtual machine

Create a virtual machine (VM) for Centos using the VirtualBox GUI.
Specify its name as vagrant-centos-6.7-x86_64, type as linux and
version as Red Hat (64 bit). Provide the VM with 512 MB base memory,
12 MB video memory and 40 GB hard drive storage which is dynamically
allocated. The iso file you downloaded from http://centos.uhost.hk/6
.7/isos/x86_64/CentOS-6.7-x86_64-minimal.iso should be specified as
its virtual CD/DVD disk file. Enable shared folders.

## Installing Centos 6.7 on the virtual machine

The installation process for Centos 6.7 should be straight forward
since most of the default options can be used. Remember the password
that you provide for root and use this to start a bash session as
this user.

Since the eth0 interface is disabled by default, start this up by:

```bash
$ ifup eth0
```

Install the following packages using the yum package manager:

```bash
$ yum install -y openssh-clients man git vim wget curl ntp
```

Set the time using hte time.nist.gov server:

```bash
$ service ntpd stop
$ ntpdate time.nist.gov
$ service ntpd start
```

Enable the ssh service to start on boot so that it is possible to ssh
into the machine as soon as it is ready:

```bash
$ chkconfig sshd on
```

Disable iptables and ip6tables services from starting on boot:

```bash
$ chkconfig iptables off
$ chkconfig ip6tables off
```

Set SELinux to be permissive using a text editor:

```bash
$ vi /etc/selinux/config
```

Create a new user called vagrant so we can use this in Vagrant. Also,
give it a password, 'vagrant':

```bash
$ useradd -m vagrant
$ passwd vagrant
```

Create vagrant user’s .ssh folder:

```bash
$ mkdir -m 0700 -p /home/vagrant/.ssh
```

Use the SSH public/private key provided by Vagrant:

```bash
$ curl https://raw.githubusercontent.com/mitchellh/vagrant/master/keys/vagrant.pub >> /home/vagrant/.ssh/authorized_keys
```

Change permissions on authorized_keys files to be more restrictive:

```bash
$ chmod 0700 .ssh
$ chmod 600 /home/vagrant/.ssh/authorized_keys
```

Make sure vagrant user and group owns the .ssh folder and its contents:

```bash
$ chown -R vagrant:vagrant /home/vagrant/.ssh
```

Allow the vagrant user to have passwordless sudo access:

```bash
$ echo "vagrant	ALL=(ALL) NOPASSWD:ALL" >> /etc/sudoers
$ echo "%wheel ALL=NOPASSWD: ALL" >> /etc/sudoers
$ echo 'Defaults env_keep="SSH_AUTH_SOCK"' >> /etc/sudoers
```

Comment out the line `Defaults requiretty` so that it looks like this
in /etc/sudoers using a text editor:

```bash
#Defaults requiretty
```

Using a text editor, change the contents of the
/etc/sysconfig/network-scripts/ifcfg-eth0 file so that it contains
the following variables:

```bash
DEVICE=eth0
TYPE=Ethernet
ONBOOT=yes
NM_CONTROLLED=no
BOOTPROTO=dhcp
```

Remove the udev persistent net rules file:

```bash
$ rm -f /etc/udev/rules.d/70-persistent-net.rules
```

Install chef omnibus which includes chef:

```bash
$ curl -L https://www.opscode.com/chef/install.sh | bash
```

The above provides a sandboxed version of ruby.

Install VirtualBox guest additions on the VM:

```bash
yum update kernel*
# Reboot VM
shutdown -r now
```

Then using the VM's GUI window, install guest additions using Devices
 -> Install Guest Additions. Go back to the terminal session:

```bash
$ mkdir /media/cdrom
$ mount /dev/cdrom /media/cdrom
mount: block device /dev/sr0 is write-protected, mounting read-only
```

Now that the image is mounted, we need to install a few tools and run
the installer:

```bash
$ export KERN_DIR=/usr/src/kernels/`uname -r`
$ yum install -y gcc make perl kernel-devel kernel-headers
$ /media/cdrom/VBoxLinuxAdditions.run --nox11
```

Note: Some of the steps like OpenGL will fail, but this is OK. you
can verify that everything is working correctly afterward by running:

```bash
$ VBoxControl --version
$ VBoxService --version
```

Change the hostname permanently using `vi /etc/sysconfig/network`:

```
NETWORKING=yes
HOSTNAME=vagrant-centos-67
```

If you have any applications that need to resolve the IP of the
hostname `vi /etc/hosts`:

```
127.0.0.1   vagrant-centos-67
127.0.0.1   localhost localhost.localdomain localhost4 localhost4.localdomain4
::1         localhost localhost.localdomain localhost6 localhost6.localdomain6
```

To resolve the `cannot change locale (UTF-8): No such file or directory`
problem `vi /etc/environment` and add these lines:

```
LANG=en_US.utf-8
LC_ALL=en_US.utf-8
```

Perform some housekeeping to clean up VM:

```bash
# Clean up yum
$ yum clean all
# Clean up the tmp directory:
$ rm -rf /tmp/*
# Clean up the last logged in users logs:
$ rm -f /var/log/wtmp /var/log/btmp
# Clean up history
$ history -c
```

Shutdown the virtual machine:

```bash
$ shutdown -h now
```

In the VM VirtualBox Manager GUI, disable the Enable Audio and Enable
USB Controller checkboxes. Also delete the CD Controller IDE in the
Storage tab.

In a terminal session in your local host, create the Vagrant base box:

```bash
$ vagrant package --output vagrant-centos-6.7-x86_64.box --base vagrant-centos-6.7-x86_64
```

You can check that the Vagrant you have created is working.

```bash
$ vagrant box add vagrant-centos-6.7-x86_64 vagrant-centos-6.7-x86_64.box --provider virtualbox
$ mkdir test
$ cd test
$ vagrant init
# Edit Vagrantfile by adding vagrant-centos-6.7-x86_64
$ vagrant up
```

