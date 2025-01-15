# TROUBLESHOOTING EXCEL SPREADSHEET UPLOADS

## File Attributes

Files annotated with two or more file attributes can cause problems with upload
process. You may find that the key value pairs of the second file attribute and
those thereafter will be concatenated into the value of the first file
attribute. Resolution of this problem will involve manual creation of file
attributes using the GigaDB admin interface.

## Dataset Types

A dataset needs to be annotated with one or more of the following valid dataset
types: Genomic, Imaging, Software, Transcriptomic, Bioinformatics, Workflow,
Metagenomic, Neuroscience, Epigenomic, Proteomic, Metadata, Genome-Mapping,
Metabolomic, Phenotyping, Network-Analysis, Ecology, Metabarcoding,
Electrophysiology, Virtual-Machine, ElectroEncephaloGraphy(EEG), Data-Mining,
Lipidomic, Hardware, Climate. If a dataset has been annotated with another term
then the upload will fail.

If one of these valid dataset types does not exist in the `type` database table
then the upload tool will create it.

## Sample Tab in Spreadsheet

There are comment and description rows in the Sample tab in the Excel
spreadsheet. These rows need to be deleted for a successful upload of the Excel
file.

## Funding information

Funding information needs to be formatted as follows:
```
University of Hawaii Cancer Center and V Foundation,V Scholar Award, ,L Wu;
National Cancer Institute, ,R01CA263494,C Wu;
National Cancer Institute, ,R01CA263494,L Wu;
```

If a column value is empty then this needs to be represented as a single space
character.

N.B. Funding information needs to be provided as a single line. Therefore, the
above funding information should look like this as the value of the
`funding_information` field in the Study tab:
```
University of Hawaii Cancer Center and V Foundation,V Scholar Award, ,L Wu;National Cancer Institute, ,R01CA263494,C Wu;National Cancer Institute, ,R01CA263494,L Wu;
```
