base_dir        "/chef"
# file_cache_path base_dir + "cache/"
# verify_api_cert true

cookbook_path    ["chef-cookbooks", "site-cookbooks"]
node_path        "nodes"
role_path        "roles"
environment_path "environments"
data_bag_path    "data_bags"
encrypted_data_bag_secret "encrypted_data_bag_secret"