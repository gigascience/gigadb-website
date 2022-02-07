Feature: main search function
  As a website user
  I want to be able to search GigaDB
  So that I can find the information I need

  @wip
  Scenario: basic search
    Given I am on "/"
    When I fill in the field of "id" "keyword" with "penguin"
    And I press the button "Search"
    Then I should see a link "Genomic data from Adelie penguin (<em>Pygoscelis adeliae</em>)." to "/dataset/100006"
    And I should see a link "Pygoscelis_adeliae" to "/dataset/100006"
    And I should see the files:
    | download link title | download link url| file type | size |
    | Pygoscelis_adeliae.s... | https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100006/Pygoscelis_adeliae.scaf.fa.gz | Sequence assembly | 350.61 MiB |
    | Pygoscelis_adeliae.f... | https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100006/phylogeny_study_update/Pygoscelis_adeliae.fa.gz | Sequence assembly | 350.48 MiB |
    | Pygoscelis_adeliae.g... | https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100006/phylogeny_study_update/Pygoscelis_adeliae.gff.gz | Annotation       | 1.59 MiB   |
    | Pygoscelis_adeliae.R... | https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100006/phylogeny_study_update/Pygoscelis_adeliae.RepeatMasker.out.gz | Repeat sequence | 7.49 MiB |
    | Pygoscelis_adeliae.c... | https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100006/phylogeny_study_update/Pygoscelis_adeliae.cds.gz              | Coding sequence | 6.43 MiB |
    | Pygoscelis_adeliae.p... | https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100006/phylogeny_study_update/Pygoscelis_adeliae.pep.gz              | Protein sequence | 4.17 MiB|
    | readme.txt | https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100006/readme.txt | Readme | 138 B |
