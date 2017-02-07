# File server installation

A Chef cookbook is available to install a test GigaDB's FTP server on a
VirtualBox VM for development work. To deploy this VM, the
`DEPLOY_GIGADB_FTP='true'` environment variable needs to be defined. In
MacOSX, this variable can be declared in your `~/.bash_profile` file.
This will enable the Vagrantfile to instantiate a second VM which
replicates GigaDB's current FTP server when you `source ~/.bash_profile`
and `vagrant up`.

The fileserver VM has an internal IP address: `10.1.1.33`. This can be
used to test that the FTP server is working:

```bash
$ ftp 10.1.1.33
Connected to 10.1.1.33.
220 Welcome to the GigaDB FTP service
Name (10.1.1.33:peterli): anonymous
230 Login successful.
Remote system type is UNIX.
Using binary mode to transfer files.
ftp> ls
229 Entering Extended Passive Mode (|||56335|).
150 Here comes the directory listing.
-rw-r--r--    1 14       50              6 Feb 07 01:39 foo.txt
226 Directory send OK.
ftp> get foo.txt
local: foo.txt remote: foo.txt
229 Entering Extended Passive Mode (|||61927|).
150 Opening BINARY mode data connection for foo.txt (6 bytes).
100% |***************************************************************************************************************|     6       14.53 KiB/s    00:00 ETA
226 Transfer complete.
6 bytes received in 00:00 (11.31 KiB/s)
ftp> quit
221 Goodbye.
$ more foo.txt 
stuff
```
The anonymous login directory is `/var/ftp/pub`.

Four user drop boxes are also available on the VM. For example, the
user1 drop box can be accessed using `gigadb1` as its password:
as follows:

```bash
$ ftp 10.1.1.33
Connected to 10.1.1.33.
220 Welcome to the GigaDB FTP service
Name (10.1.1.33:peterli): user1
331 Please specify the password.
Password: 
230 Login successful.
Remote system type is UNIX.
Using binary mode to transfer files.
ftp> quit
221 Goodbye.
```

To gain SSH access to the VM:

```bash
$ vagrant ssh ftp-server
```


