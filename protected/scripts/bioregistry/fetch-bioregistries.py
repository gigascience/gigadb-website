import requests

# Fetch all prefixes
response = requests.get('https://bioregistry.io/api/registry')
data = response.json()

prefixes = list(data.keys())

# attempt to retrieve a uri for each prefix
uri_formats = {}
for prefix in prefixes:
    prefix_response = requests.get(f'https://bioregistry.io/api/registry/{prefix}')
    prefix_data = prefix_response.json()
    # assuming the optimal uri_format is the "root" uri_format, but some registries have multiple providers
    uri_format = prefix_data.get('uri_format', None)
    uri_formats[prefix] = uri_format

for prefix, uri_format in uri_formats.items():
    print(f'{prefix}: {uri_format}')