#!/usr/bin/env bash
set -e

# Check if the correct number of arguments are provided
if [[ $# -ne 2 ]]; then
  echo "Usage: $0 <file_list> <search_directory>"
  exit 1
fi

# Assign command-line arguments to variables
file_list="$1"
search_directory="$2"

# Check if the file_list exists
if [[ ! -f "$file_list" ]]; then
  echo "File list $file_list not found!"
  exit 1
fi

# Check if the search_directory exists
if [[ ! -d "$search_directory" ]]; then
  echo "Search directory $search_directory not found!"
  exit 1
fi

# Initialize counters
found_count=0
not_found_count=0

# Create an associative array to store the files in the list
declare -A files_in_list

# Read the file list line by line and populate the associative array
while IFS= read -r relative_path; do
  [ -n "$relative_path" ] && files_in_list["$relative_path"]=1
done < "$file_list"

# Search for files in the search directory based on the file list
for relative_path in "${!files_in_list[@]}"; do
  found_files=$(find "$search_directory" -type f -path "$search_directory/$relative_path")

  if [[ -n "$found_files" ]]; then
    echo "Files found for $relative_path:"
    echo "$found_files"
    found_count=$((found_count + 1))
  else
    echo "No files found for $relative_path"
    not_found_count=$((not_found_count + 1))
  fi
done

# Initialize reciprocal search count
reciprocal_count=0

# Perform a reciprocal search to find files in the search directory not listed in the file list
echo
echo "Files in $search_directory not listed in $file_list:"
while IFS= read -r file_path; do
  # Get the relative path by stripping the search directory prefix
  relative_path="${file_path#$search_directory/}"
  if [[ -z "${files_in_list[$relative_path]}" ]]; then
    echo "$relative_path"
    reciprocal_count=$((reciprocal_count + 1))
  fi
done < <(find "$search_directory" -type f)

# Print summary
echo
echo "Summary:"
echo "Files found from the list: $found_count"
echo "Files not found from the list: $not_found_count"
echo "Files in $search_directory not listed in $file_list: $reciprocal_count"

