name             'fileserver'
maintainer       'GigaScience'
maintainer_email 'peter@gigasciencejournal.com'
license          'CC0'
description      'Installs/Configures fileserver'
long_description IO.read(File.join(File.dirname(__FILE__), 'README.md'))
version          '0.1.0'

depends "postgresql"
depends "vsftpd"
depends "cron"