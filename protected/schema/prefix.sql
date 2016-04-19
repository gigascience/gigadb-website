-- prefix data
DELETE FROM prefix WHERE LOWER(prefix) IN ('submission', 'study', 'sample', 'bioproject', 'biosample', 'genbank');

INSERT INTO prefix(source, url, prefix) VALUES 
	('EBI', 'https://www.ebi.ac.uk/ena/data/view/', 'Submission'),
	('NCBI', 'http://www.ncbi.nlm.nih.gov/sra/?term=', 'Submission'),
	('DDBJ', 'http://trace.ddbj.nig.ac.jp/DRASearch/submission?acc=', 'Submission'),

	('NCBI','http://www.ncbi.nlm.nih.gov/sra?term=', 'Study'), 
	('EBI', 'https://www.ebi.ac.uk/ena/data/view/', 'Study'), 
	('DDBJ', 'http://trace.ddbj.nig.ac.jp/DRASearch/study?acc=', 'Study'),

	('NCBI', 'http://www.ncbi.nlm.nih.gov/sra/?term=', 'Sample'),
	('EBI', 'https://www.ebi.ac.uk/ena/data/view/', 'Sample'), 
	('DDBJ', 'http://trace.ddbj.nig.ac.jp/DRASearch/sample?acc=', 'Sample'),

	('NCBI', 'http://www.ncbi.nlm.nih.gov/bioproject/', 'BioProject'),
	('EBI', 'https://www.ebi.ac.uk/ena/data/view/', 'BioProject'),
	('DDBJ', 'http://trace.ddbj.nig.ac.jp/BPSearch/bioproject?acc=', 'BioProject'), 

	('NCBI', 'http://www.ncbi.nlm.nih.gov/biosample/', 'BioSample'),
	('EBI', 'https://www.ebi.ac.uk/ena/data/view/', 'BioSample'),
	('DDBJ', 'http://trace.ddbj.nig.ac.jp/BSSearch/biosample?acc=', 'BioSample'),

	('NCBI', 'http://www.ncbi.nlm.nih.gov/nuccore/?term=', 'Genbank'),
	('EBI', 'http://www.ebi.ac.uk/ena/data/view/', 'Genbank'),
	('DDBJ', 'http://getentry.ddbj.nig.ac.jp/getentry/na/', 'Genbank');