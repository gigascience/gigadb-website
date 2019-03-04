Feature:
	As an Author
	I want to upload files for my manuscript's dataset using FTP
	So that the dataset can be reviewed and made available online despite web access restriction

Scenario:
	Given I have a file to upload
	And An ftp server is associated with dataset "100006"
	When I upload the file to the ftp server
	Then the file is transfered to the file drop box for dataset "100006"

