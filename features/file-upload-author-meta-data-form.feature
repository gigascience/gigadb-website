Feature:
	As an Author
	I want to add meta-data to the files I have uploaded
	So that the files associated with my manuscript's dataset can be queried precisely

Scenario:
	Given I am on the file upload wizard page
	And I add a set of files to the uploading queue
	And all the files have been uploaded
	When click the "Next" button
	Then I should see:
	| File name | Data type | Format | Size |
	| file1.txt | Text | Text | 1kb |
	| file2.csv | Text | Text | 1kb |
	| file3.jpg | Image | JPEG | 1kb |