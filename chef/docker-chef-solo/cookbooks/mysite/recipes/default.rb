user "mysiteuser" do
    comment "mysite user"
    home "/home/mysiteuser"
    shell "/bin/bash"
end
 
directory "/var/www/html/mysite" do
    owner "mysiteuser"
    group "mysiteuser"
    mode 0755
    action :create
end
 
template "/var/www/html/mysite/index.html" do
    source "message.erb"
    variables(
        :message => "Hello World!"
    )
    user "mysiteuser"
    group "mysiteuser"
    mode 0755
end
 
web_app "mysite" do
    server_name "example.com"
    server_aliases ["www.example.com","example.com"]
    docroot "/var/www/html/mysite"
    cookbook 'apache2'
end 
