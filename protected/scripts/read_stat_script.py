from Bio import SeqIO
from Bio.Blast import NCBIWWW
from Bio.Blast import NCBIXML
import sys

dna = set("ATGC-actg")
def validate(seq, alphabet=dna):
    "Checks that a sequence only contains values from an alphabet"
    leftover = set(seq.upper()) - alphabet
    return not leftover

file = open('result.txt', 'w')

format= sys.argv[1].split(".")
if(format[-1] == 'fasta' or format[-1] == 'fa' or format[-1] == 'fastq'):
	records = list(SeqIO.parse(sys.argv[1], "fasta"))
	print("Found %i reads" % len(records))
        file.write("Found," + str(len(records)) + "\n")
	total = 0
	for t in records:
        	total=total+len(t)
	if(validate(records[1].seq[:10])):
		print "Number of nucleotides: ", total
                file.write("nucleotides," + str(total) + "\n")
	else:
		print "Number of amino acids: ", total
                file.write("acids," + str(total) + "\n")
if(format[-1] == 'gbk' or format[-1] == 'gb'):
        records = list(SeqIO.parse(sys.argv[1], "genbank"))
        print("Found %i reads" % len(records))
        file.write("Found," + str(len(records)) + "\n")
        total = 0
        for t in records:
                total=total+len(t)
        if(validate(records[1].seq[:10])):
                print "Number of nucleotides: ", total
                file.write("nucleotides," + str(total) + "\n")
        else:
                print "Number of amino acids: ", total
                file.write("acids," + str(total) + "\n")
if(format[-1] == 'sff'):
        records = list(SeqIO.parse(sys.argv[1], "sff"))
        print("Found %i reads" % len(records))
        file.write("Found," + str(len(records)) + "\n")
        total = 0
        for t in records:
                total=total+len(t)
        if(validate(records[1].seq[:10])):
                print "Number of nucleotides: ", total
                file.write("nucleotides," + str(total) + "\n")
        else:
                print "Number of amino acids: ", total
                file.write("acids," + str(total) + "\n")
if(format[-1] == 'sam'):
        records = list(SeqIO.parse(sys.argv[1], "sam"))
        print("Found %i reads" % len(records))
        file.write("Found," + str(len(records)) + "\n")
        total = 0
        for t in records:
                total=total+len(t)
        if(validate(records[1].seq[:10])):
                print "Number of nucleotides: ", total
                file.write("nucleotides," + str(total) + "\n")
        else:
                print "Number of amino acids: ", total
                file.write("acids," + str(total) + "\n")
if(format[-1] == 'bam'):
        records = list(SeqIO.parse(sys.argv[1], "bam"))
        print("Found %i reads" % len(records))
        file.write("Found," + str(len(records)) + "\n")
        total = 0
        for t in records:
                total=total+len(t)
        if(validate(records[1].seq[:10])):
                print "Number of nucleotides: ", total
                file.write("nucleotides," + str(total) + "\n")
        else:
                print "Number of amino acids: ", total
                file.write("acids," + str(total) + "\n")
else:
    print "unknown format"
file.close()