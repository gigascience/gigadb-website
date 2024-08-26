#!/usr/bin/env bats

setup () {
  echo '# Executing setup code' >&3

  TOOLS_DIR="${BATS_TEST_DIRNAME}/../../.."
  
  # Download Excel file for dataset 102498
  echo '# Downloading Excel file for dataset 102498' >&3
  filename="GigaDBUpload_v18_102498_TRR_202311_02_Cell_Clustering_Spatial_Transcriptomics.xls"
  fileid="1sLabqRPkhF61nRocLmumjjCxxjzmODH5"
  curl -L -o "${TOOLS_DIR}/excel-spreadsheet-uploader/uploadDir/${filename}" "https://drive.google.com/uc?export=download&id=${fileid}"
  if [ ! -f "${TOOLS_DIR}/excel-spreadsheet-uploader/uploadDir/${filename}" ] ; then
    echo '# Error - Dataset excel file not downloaded' >&3
    exit 1
  fi
  
  # Run Excel upload tool
  cd ../../../excel-spreadsheet-uploader || exit
  echo '# Ingesting Excel file for dataset 102498' >&3
  run ./execute.sh
}

teardown () {
  echo '# Executing teardown code' >&3

  FILES="${TOOLS_DIR}/excel-spreadsheet-uploader/logs/java.log
  ${TOOLS_DIR}/excel-spreadsheet-uploader/logs/javac.log
  ${TOOLS_DIR}/readme-generator/runtime/curators/readme_102498.txt
  ${TOOLS_DIR}/files-metadata-console/tests/_data/dropbox/user5/readme_102498.txt
  ${TOOLS_DIR}/files-metadata-console/tests/_data/dropbox/user5/102498.filesizes
  ${TOOLS_DIR}/files-metadata-console/tests/_data/dropbox/user5/102498.md5"

  for file in $FILES
  do
    # echo "# Deleting $file" >&3
    if [ -f "$file" ] ; then
        rm "$file"
    fi
  done
}

@test "Test postUpload.sh" {
  echo '# Executing test postUpload.sh --doi 102498 --dropbox user5' >&3
  cd ../files-metadata-console/scripts
  run ./postUpload.sh --doi 102498 --dropbox user5
  # Uncomment to display output of postUpload script
  # echo "${output}" >&3
  # Check readme file was created
  [ -f "${TOOLS_DIR}/readme-generator/runtime/curators/readme_102498.txt" ]
  # Display contents of readme file
  # cat "${TOOLS_DIR}/readme-generator/runtime/curators/readme_102498.txt" >&3
  # Check readme file content
  run sed '2q;d' "${TOOLS_DIR}/readme-generator/runtime/curators/readme_102498.txt"
  [ "${lines[0]}" = "10.5524/102498" ]
  # Check readme file was copied into user dropbox
  [ -f "${TOOLS_DIR}/files-metadata-console/tests/_data/dropbox/user5/readme_102498.txt" ]
  # Check dataset metadata files were created in user dropbox
  [ -f "${TOOLS_DIR}/files-metadata-console/tests/_data/dropbox/user5/102498.filesizes" ]
  [ -f "${TOOLS_DIR}/files-metadata-console/tests/_data/dropbox/user5/102498.md5" ]
}