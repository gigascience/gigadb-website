# Definition of constants found in Ingest table

| Constant                       | Value | Definition                                        |
|--------------------------------|-------|---------------------------------------------------|
| REPORT_TYPES                   | 1     | EM manuscripts report                             |
| REPORT_TYPES                   | 2     | EM authors report                                 |
| REPORT_TYPES                   | 3     | EM reviewers report                               |
| REPORT_TYPES                   | 4     | EM questions report                               |
| REPORT_TYPES                   | 5     | EM reviews report                                 |
| FETCH_STATUS_FOUND             | 1     | Report is found in sftp server                    |
| FETCH_STATUS_DOWNLOADED        | 2     | Report is downloaded                              |
| FETCH_STATUS_DISPATCHED        | 3     | Report content has been put to queue              |
| FETCH_STATUS_ERROR             | 0     | Report cannot be read or corrupted                |
| PARSE_STATUS_YES               | 1     | Report has been parsed and content saved to table |
| PARSE_STATUS_NO                | 0     | Report has not been parsed                        |
| REMOTE_FILES_STATUS_EXISTS     | 1     | Report contains real data                         | 
| REMOTE_FILES_STATUS_NO_RESULTS | 0     | Report contains no data                           |
| STORE_STATUS_YES               | 1     | Report content is stored                          |
| STORE_STATUS_NO                | 0     | Report content is not stored                      |