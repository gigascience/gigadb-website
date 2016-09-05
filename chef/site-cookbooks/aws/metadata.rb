maintainer       "GigaScience"
maintainer_email "peter@gigasciencejournal.com"
license          "All rights reserved"
description      "Installs/Configures main"
long_description IO.read(File.join(File.dirname(__FILE__), 'README.md'))
version          "0.0.1"
name			 "aws"

depends "user"
depends "iptables"
depends "fail2ban"
depends "selinux"

depends "gigadb"