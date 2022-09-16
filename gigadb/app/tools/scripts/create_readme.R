#library(ssh)
#library(RPostgreSQL)
require("RPostgreSQL")
require("ssh")
drv <- dbDriver("PostgreSQL")

# Define database credentials for dev deployment
dsn_database = "gigadb"
dsn_hostname = "localhost"
dsn_port = "54321"
dsn_uid = "gigadb"
dsn_pwd = "vagrant"

# Establish database connection
tryCatch({
  drv <- dbDriver("PostgreSQL")
  print("Connecting to Database…")
  connec <- dbConnect(drv, 
    dbname = dsn_database,
    host = dsn_hostname, 
    port = dsn_port,
    user = dsn_uid, 
    password = dsn_pwd)
  print("Database Connected!")
  },
  error=function(cond) {
    print("Unable to connect to Database.")
})

# Input DOIs
doi_list <- c(102272,102273,102274,102275,102276,102277,102278,102279,102280,102281,102282,102283,102284,102285,102286,102287,102288,102289,102290,102291,102292,102293,102294,102295,102296,102297,102298,102299,102300,102301,102302,102303,102304,102305,102306,102307,102308,102309,102310,102311,102312,102313,102314)
small_doi_list <- c(102272,102273)
doi <- "100006"

# [DOI]
get_doi <- function(doi) {
  return(paste("[DOI] 10.5524/", doi, "\n", sep = ""))
}

# [Title]
get_title <- function(doi) {
  title_sql <- paste("SELECT title FROM dataset WHERE identifier = '", doi, "'", sep = "")
  title_df <- dbGetQuery(connec, title_sql)
  title <- title_df$title
  return(title)
}
title <- paste("[Title] ", get_title(doi), "\n", sep = "")

# [Release Date]
get_reldate <- function(doi) {
  publication_date_sql <- paste("SELECT publication_date FROM dataset WHERE identifier = '", doi, "'", sep = "")
  publication_date_df <- dbGetQuery(connec, publication_date_sql)
  publication_date <- publication_date_df$publication_date
  release_date <- paste("[Release Date]", publication_date, "\n")
  return(release_date)
}

# [Citation]
get_citation <- function(doi) {
  # Get dataset id for doi
  dataset_id_sql <- paste("SELECT id FROM dataset WHERE identifier = '", doi, "'", sep = "")
  dataset_id_df <- dbGetQuery(connec, dataset_id_sql)
  dataset_id <- dataset_id_df$id
  
  author_ids_sql <- paste("SELECT author_id FROM dataset_author WHERE dataset_id = '", dataset_id, "' ORDER BY rank ASC", sep = "")
  #print(author_ids_sql)
  author_ids_df <- dbGetQuery(connec, author_ids_sql)
  author_ids <- author_ids_df$author_id
  
  # Create author list
  authors <- c()
  for (author_id in author_ids) {
    #print(author_id)
    authors_sql <- paste("SELECT surname, first_name, middle_name FROM author WHERE id = '", author_id, "'", sep = "")
    #print(authors_sql)
    authors_df <- dbGetQuery(connec, authors_sql)
    #print.data.frame(authors_df)
    name <- paste(authors_df$surname, substr(authors_df$first_name, 0, 1), sep=", ")
    authors <- append(authors, name)
    #author_list <- paste(author_list, name, sep = "; ")
  }
  author_list <- paste(authors, collapse = "; ")
  
  # Get dataset publication year
  publication_date_sql <- paste("SELECT publication_date FROM dataset WHERE identifier = '", doi, "'", sep = "")
  publication_date_df <- dbGetQuery(connec, publication_date_sql)
  publication_date <- publication_date_df$publication_date
  publication_year <- (substr(publication_date, 0, 4))
  publication_year <- paste("(", publication_year, "):", sep = "")
  
  #Get DOI URL
  doi_url <- paste("http://dx.doi.org/10.5524/", doi, sep = "")
  
  citation <- paste("[Citation]", author_list, publication_year, get_title(doi), "GigaScience Database.", doi_url, "\n")
  
}

# [Data Type]
get_datatype <- function(doi) {
  # Get dataset id for doi
  dataset_id_sql <- paste("SELECT id FROM dataset WHERE identifier = '", doi, "'", sep = "")
  dataset_id_df <- dbGetQuery(connec, dataset_id_sql)
  dataset_id <- dataset_id_df$id
  
  # Get dataset id for doi
  type_id_sql <- paste("SELECT type_id FROM dataset_type WHERE dataset_id = '", dataset_id, "'", sep = "")
  type_id_df <- dbGetQuery(connec, type_id_sql)
  type_id <- type_id_df$type_id
  
  # Get type name
  type_name_sql <- paste("SELECT name FROM type WHERE id = '", type_id, "'", sep = "")
  print(type_name_sql)
  type_name_df <- dbGetQuery(connec, type_name_sql)
  type_name <- type_name_df$name
  print(paste("Dataset Type name =", type_name))
  
  data_type <- paste("[Data Type]", type_name, "\n")
  return(data_type)
}

# [Dataset Summary]
get_summary <- function(doi) {
  description_sql <- paste("SELECT description FROM dataset WHERE identifier = '", doi, "'", sep = "")
  description_df <- dbGetQuery(connec, description_sql)
  #title <- paste("[Title] ", title_df$title, "\n\n", sep = "")
  description <- paste("[Dataset Summary]", description_df$description, "\n")
  return(description)
}

# [File Location]
get_location <- function(doi) {
  location_sql <- paste("SELECT ftp_site FROM dataset WHERE identifier = '", doi, "'", sep = "")
  location_df <- dbGetQuery(connec, location_sql)
  location <- paste("[File Location]", location_df$ftp_site, "\n")
  return(location)
}

# [File name] - [File Description]
get_files <- function(doi) {
  # Get dataset id for doi
  dataset_id_sql <- paste("SELECT id FROM dataset WHERE identifier = '", doi, "'", sep = "")
  dataset_id_df <- dbGetQuery(connec, dataset_id_sql)
  dataset_id <- dataset_id_df$id
  print(paste("Dataset id =", dataset_id))
  
  files_sql <- paste("SELECT name, description FROM file WHERE dataset_id = '", dataset_id, "'", sep = "")
  files_df <- dbGetQuery(connec, files_sql)
  files_df
  #print(paste("Dataset id =", dataset_id))
  
  files <- "[File name] - [File Description]"
  # Loop over files
  for (i in 1:nrow(files_df)) {
    file_name <- files_df[i, 1]
    file_description <- files_df[i, 2]
    file_name_description <- paste(file_name, file_description, sep = " - ")
    files <- append(files, file_name_description)
  }
  files <- paste(files, collapse = "\n")
  return(paste(files, "\n"))
}

# [License]
get_license <- function() {
  license <- paste("[License]", "\n", "All files and data are distributed under the Creative Commons Attribution-CC0 License unless specifically stated otherwise, see http://gigadb.org/site/term for more details.", "\n", sep = "")
  return(license)
}

# [Comments]
get_comments <- function() {
  return("[Comments]\n")
}

# [End]
get_end <- function() {
  return("[End]")
}

# Loop over list of DOIs
for (doi in doi_list) {
  out <- paste(get_doi(doi), title, get_reldate(doi), get_citation(doi), get_datatype(doi), get_summary(doi), get_location(doi), get_files(doi), get_license(), get_comments(), get_end(), sep = "\n")
  out_filename <- paste("~/Desktop/readme_", doi, ".txt", sep = "")
  cat(out, file = out_filename)
}
