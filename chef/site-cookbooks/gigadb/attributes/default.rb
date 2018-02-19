default[:gigadb][:es_port] = 9200
default[:nodejs][:version] = '0.10.33'

case node[:environment]
when 'vagrant'
    default[:nginx][:expires] = '-1'
    default[:nginx][:sendfile] = 'off'
else
    default[:nginx][:expires] = 'max'
    default[:nginx][:sendfile] = 'on'
end

default[:nginx][:version] = 'on'

default[:postgresql][:version] = '9.1'
default[:postgresql][:repo_version] = '8.4'
default[:postgresql][:config][:listen_addresses] = ["*"]

default[:postgresql][:client_auth] = [
     {
        "type" => "host",
        "database" => "all",
        "user" => "all",
        "address" => "10.0.2.2/32",
        "auth_method" => "trust"
    }
]

default[:sphinx][:version] = '2.0.6-release'
default[:sphinx][:url] = "http://sphinxsearch.com/files/sphinx-#{sphinx[:version]}.tar.gz"

default[:yii][:version] = '1.1.16'
default[:yii][:path] = '/opt/yii-1.1.16'
default[:yii][:ip_address] = 'http://127.0.0.1:9170'

default['java']['jdk_version'] = '7'
default["java"]["install_flavor"] = 'openjdk'

default['fail2ban']['ignoreip'] = '127.0.0.1/8 59.148.193.108/32'
default['fail2ban']['services'] = {
  'ssh' => {
        "enabled" => "true",
        "port" => "ssh",
        "filter" => "sshd",
        "logpath" => node['fail2ban']['auth_log'],
        "maxretry" => "3"
     }
}
