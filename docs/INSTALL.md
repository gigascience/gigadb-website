# Installing GigaDB on a virtual machine

## Preparation

A test instance of GigaDB can be automatically installed in a virtual
machine (VM) using [Vagrant](https://www.vagrantup.com) and [Chef Solo](https://docs.chef.io/chef_solo.html).

Vagrant is a command line utility for creating VMs. To get started,
download and install Vagrant using the appropriate installer or
package for your platform which is available from the
[Vagrant download page](https://www.vagrantup.com/downloads.html).
There is no need to install any software for Chef-Solo since the base
Vagrant VMs that we will be using to deploy GigaDB on will come with
Chef pre-installed.

The virtual machine we will use to host a test version of GigaDB is
provided by [VirtualBox](https://www.virtualbox.org) which is free
software. Download and install the appropriate version of [VirtualBox](https://www.virtualbox.org/wiki/Downloads)
for your platform.

The GigaDB code base is available from [GitHub](https://github.com/gigascience/gigadb-website).
Since you will be committing code you will have written, please use
git to do this.

### Linux

If you want to install the basic Git tools on Linux via a binary
installer, you can generally do so through the basic package
management tool that comes with your distribution. If you’re on
Fedora and Centos for example, open a commandline terminal and use yum:

```bash
$ sudo yum install git-all
```

If you’re on a Debian-based distribution like Ubuntu, try apt-get:

```bash
$ sudo apt-get install git-all
```

### MacOSX

There are several ways to install Git on a Mac. The easiest is
probably to install the Xcode Command Line Tools. On Mavericks (10.9)
or above, you can do this by trying to run git from the Terminal the
very first time. If you don’t have it installed already, it will
prompt you to install it.

If you want a more up to date version, you can also install git via a
binary installer. An OSX Git installer is maintained and available
for download at the [Git website](http://git-scm.com/download/mac).

### Windows

We suggest that you install [Babun](http://babun.github.io) which
provides a Linux-like console on Windows platforms. Babun will provide
`git` as well as other develop tools.

## Downloading the GigaDB code repository

After you have git installed, you can now use it to download the
GigaDB source code from Github:

`$ git clone https://github.com/gigascience/gigadb-website.git`

The GigaDB source code repository relies on other Github projects
which are incorporated into its code base as Github submodules.
However, the code for these projects are missing. For example,
chef-cookbooks is a submodule and its folder is present at
`chef/chef-cookbooks` but the code is missing:

```bash
$ git clone https://github.com/gigascience/gigadb-website.git
Cloning into 'gigadb-website'...
remote: Counting objects: 1657, done.
remote: Compressing objects: 100% (68/68), done.
remote: Total 1657 (delta 25), reused 0 (delta 0), pack-reused 1581
Receiving objects: 100% (1657/1657), 2.33 MiB | 785.00 KiB/s, done.
Resolving deltas: 100% (516/516), done.
Checking connectivity... done.
$ cd chef/chef-cookbooks/
$ ls
total 0
drwxr-xr-x  2 peterli  staff    68B Apr 27 09:57 ./
drwxr-xr-x  4 peterli  staff   136B Apr 27 09:57 ../
```

The code for these other Github projects need to be downloaded:

```bash
$ git submodule init
Submodule 'chef/chef-cookbooks' (https://github.com/pli888/chef-cookbooks.git) registered for path '../chef-cookbooks'
$ git submodule update
Cloning into 'chef/chef-cookbooks'...
remote: Counting objects: 8148, done.
remote: Total 8148 (delta 0), reused 0 (delta 0), pack-reused 8148
Receiving objects: 100% (8148/8148), 2.42 MiB | 449.00 KiB/s, done.
Resolving deltas: 100% (2874/2874), done.
Checking connectivity... done.
Submodule path '../chef-cookbooks': checked out '1cf3e93cb1f7ef481269751a55df4bf7af458462'
```

If you are developing GigaDB for GigaScience, you might be informed
to write new code for a particular [branch](https://git-scm.com/book/en/v1/Git-Branching-What-a-Branch-Is)
in the code repository. For example, if you are asked to use the
`develop` branch then you need to checkout this branch from Github:

```bash
$ git fetch
$ git checkout develop
Branch develop set up to track remote branch develop from origin.
Switched to a new branch 'develop'
$ git branch
* develop
  master
```

## Configuring the provisioning of the GigaDB virtual machine

There are attribute variables in GigaDB which require values to be set.
These variables are listed in
`gigadb-website/chef/site-cookbooks/gigadb/attributes/default.rb.sample`.
Your technical manager will be able to provide the values that these
variables should be configured with. Once you have filled in the
required values for the attributes, the file must then be saved as
`default.rb` in the same folder. One or two key files may also be
required in the files/certs directory.

## Creating and provisioning the virtual machine

Vagrant can now be used to create the virtual machine:

```bash
$ vagrant up
```

It will now take up to 10 minutes for the VM to be created and
installed with GigaDB and its software dependencies in a process
called provisioning which is performed by Chef Solo. During the
provisioning process, you will see many log messages in your terminal
which will keep you up to date with the deployment of GigaDB in your
local VM.

Once the `vagrant up` command has finished, you will not see anything
since Vagrant runs the VM without a user interface (UI). However, you
can SSH into the machine:

```bash
$ vagrant ssh
```

This command will log you into a SSH session in the VM created by
Vagrant.

For further evidence that GigaDB is running on the VM, open a web
browser and point it to [http://127.0.0.1:9170]. You should see the
GigaDB web site that is deployed on your local VM.

[http://127.0.0.1:9170]: http://127.0.0.1:9170

To leave the SSH session:

```bash
$ logout
Connection to 127.0.0.1 closed.
```

## Shared folder

There is a folder `/vagrant` in the VM created Vagrant which is
shared with the directory that contains the local gigadb-website Github
repository on your computer which is hosting the Vagrant-VM. If you
change the code in your gigadb-website repository, this means that
the code is also changed in `/vagrant` on your guest VM. Use
[http://127.0.0.1:9170] in your web browser to check how your code
may have affect the behvaiour of GigaDB.

## Shutting down the VM

The Vagrant VM can be completely removed by running `vagrant destroy`.
This command will terminate the use of any resources by the VM.


