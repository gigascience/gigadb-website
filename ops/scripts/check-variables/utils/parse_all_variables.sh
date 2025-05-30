# Function: parse_all_variables
# Extracts **all** variable names from the variables documentation file
# number of white spaces after each field is arbitrary
parse_all_variables() {
  : "${VARIABLES_MD_PATH:="docs/variables.md"}"

  awk '/^\| [A-Za-z0-9_]+[ ]*\|/ { gsub(/^\| /, ""); gsub(/ .*/, ""); print $1 }' "$VARIABLES_MD_PATH" | grep -v '^Variable$'
}