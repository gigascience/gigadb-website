directory '/var/ftp/pub/user_bundles/' do
  owner 'ftp'
  group 'ftp'
  mode '0555'
  recursive true
  action :create
end
