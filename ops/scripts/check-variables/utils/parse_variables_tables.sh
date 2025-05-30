# Usage: parse_variables_tables "## HEADING_STRING" [path/to/variables.md]
# Parses a markdown file (defaults to docs/variables.md) and extracts variable names
# from the first column of a markdown table found under the specified HEADING_STRING.
parse_variables_tables() {
  local heading_pattern="$1"
  local variables_md_path="${2:-"docs/variables.md"}" # Default to docs/variables.md if not provided

  awk -v heading="$heading_pattern" '
  # Switch to target section processing mode
  $0 == heading {
    in_section = 1
    next # Skip the heading line itself
  }

  # If we encounter another heading (##, ###, etc.) while in target section mode, stop.
  # This signifies the end of the current variable table.
  /^## / && in_section && $0 != heading {
    exit
  }

  # If in target section mode and the line looks like a markdown table row
  in_section && /^\|/ {
    var_candidate = $0 # Work on a copy

    # 1. Remove leading pipe and any initial whitespace: e.g., "| MY_VAR   |..." -> "MY_VAR   |..."
    sub(/^\|[ \t]*/, "", var_candidate)

    # 2. Isolate the first column content by removing from the next pipe onwards:
    #    e.g., "MY_VAR   |..." -> "MY_VAR   "
    sub(/[ \t]*\|.*/, "", var_candidate)

    # 3. Trim trailing whitespace from the isolated first column: "MY_VAR   " -> "MY_VAR"
    gsub(/[ \t]+$/, "", var_candidate)

    # Ensure it is not the header "Variable", not empty, and contains typical variable characters (alphanumeric or underscore)
    if (var_candidate != "" && var_candidate != "Variable" && var_candidate ~ /[a-zA-Z0-9_]/) {
      print var_candidate
    }
  }
' "$variables_md_path"
}

# Call the function if the script is executed directly
if [[ "${BASH_SOURCE[0]}" == "$0" ]]; then
  if [[ $# -eq 0 ]]; then
    echo "Usage: $0 \"## HEADING_STRING\" [path/to/variables.md]" >&2
    exit 1
  fi
  parse_variables_tables "$@"
fi