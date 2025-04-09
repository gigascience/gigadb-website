import sys

input_filename = 'bioregistries.txt'  # Input text file name
output_filename = 'insert_bioregistry_prefixes.sql' # Output SQL file name
source_value = 'bioregistry'

sql_lines = []

try:
    with open(input_filename, 'r', encoding='utf-8') as f_in:
        for line in f_in:
            line = line.strip()
            if not line or ':' not in line:
                continue  # Skip empty or invalid lines

            try:
                prefix, url = line.split(': ', 1) # Split only on the first ': '

                # Basic SQL escaping for single quotes
                prefix_sql = prefix.replace("'", "''")
                url_sql = url.replace("'", "''")
                source_sql = source_value.replace("'", "''")

                sql_lines.append(f"('{prefix_sql}', '{url_sql}', '{source_sql}')")

            except ValueError:
                print(f"Warning: Skipping malformed line: {line}", file=sys.stderr)


    if not sql_lines:
        print("No valid data found in input file.")
    else:
        with open(output_filename, 'w', encoding='utf-8') as f_out:
            # Write the sequence reset command (optional, but good practice)
            f_out.write("-- Reset sequence to max id + 1\n")
            f_out.write("SELECT setval('public.link_prefix_id_seq', COALESCE((SELECT MAX(id) FROM prefix), 0) + 1, false);\n\n")

            # Write the INSERT statement
            f_out.write("-- Insert data generated from text file\n")
            f_out.write("INSERT INTO prefix (prefix, url, source) VALUES\n")
            f_out.write(',\n'.join(sql_lines)) # Join lines with comma and newline
            f_out.write(';\n') # Add semicolon at the end

        print(f"SQL script generated successfully: {output_filename}")

except FileNotFoundError:
    print(f"Error: Input file not found: {input_filename}", file=sys.stderr)
except Exception as e:
    print(f"An error occurred: {e}", file=sys.stderr)