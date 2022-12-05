use strict;

my %hash; my $read_id; my $score; my $len;
my $in_sequencing_summary_file=$ARGV[0];
my $in_fastq=$ARGV[1];


if ($in_sequencing_summary_file=~/\.gz$/){
open (FA, "gzip -dc $in_sequencing_summary_file | " ) || die "$!";
}else {
	open FA, $in_sequencing_summary_file||die $!;}

$/="\n";
my $head = <FA>;

if ($head =~ /mean_qscore_template/) {
}else{
	print "wrong format !!!!\n\nPlease CHECK THIS FORMAT!!";
}

my @info1 = split /\s+/, $head;
my $num = @info1;
for ($a = 0; $a < $num; $a = $a + 1) {
	if ($info1[$a] =~ m/mean_qscore_template/) {
		$score = $a;
        }elsif($info1[$a] =~ m/read_id/){
		$read_id=$a;
	}elsif($info1[$a] =~ m/sequence_length_template/){
		$len=$a;
	}
}

#print "$read_id\t$score\t$len\n";


while (<FA>) {
        chomp;
        my @info = split /\s+/;
        #my $score = $info[$want];
        if ($score >= 50) {
        print "wrong score value!!!\n\nPlease CHECK THIS SCORE FORMAT!!";exit;
        }
        if (($info[$score] >= 7) && ($info[$len] >= 1000)) {
        $hash{$info[$read_id]}=1;
        }
}


if ($in_fastq=~/\.gz$/){
open (FB, "gzip -dc $in_fastq | " ) || die "$!";
}else {
	open FB, $ARGV[1] || die "$!";}

$/="\@";
<FB>;
while (<FB>) {
        chomp;
        my @info1 = split "\n";
        my $name= $info1[0];
        my $seq = $info1[1];
        my @info2 = split /\s+/, $name;
        
	if (exists $hash{$info2[0]}) {
	       print ">$info2[0]\n$seq\n";
        }
}
