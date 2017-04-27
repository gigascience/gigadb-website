# install sample data on the ftp server for testing

remote_file '/tmp/ftpexamples.tar.gz' do
  source "http://gdbws-ftp-sample-data.s3.amazonaws.com/ftpexamples.tar.gz"
end

bash 'extract_examples' do
  cwd "/"
  code <<-EOH
    tar xzf /tmp/ftpexamples.tar.gz
    EOH
end
