default[:gigadb][:app_user] = 'gigadb'
default[:gigadb][:admin_email] = 'database@gigasciencejournal.com'
default[:gigadb][:site_dir] = '/vagrant'
default[:gigadb][:db][:database] = 'gigadb'
default[:gigadb][:db][:user] = 'gigadb'
default[:gigadb][:db][:password] = 'vagrant'
default[:gigadb][:db][:host] = 'localhost'
default[:gigadb][:db][:port] = '3306'
default[:gigadb][:python][:build_dir] = '/home/gigadb/build'
default[:gigadb][:python][:virtualenv] = '/home/gigadb/.virtualenvs/gigadb'
default[:gigadb][:python][:packages] = ['biopython', 'beautifulsoup4']
default[:gigadb][:python][:schemup][:version] = '07905e6948273fc4393f5155999bce2e012dcfa8'

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

default[:sphinx][:version] = '2.0.6-release'
default[:sphinx][:url] = "http://sphinxsearch.com/files/sphinx-#{sphinx[:version]}.tar.gz"

default[:yii][:version] = '1.1.16'
default[:yii][:path] = '/opt/yii-1.1.16'
