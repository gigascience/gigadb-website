<?php

use common\models\Upload;

return [
    [
        'id' => 1,
        'doi' => '200000',
        'name' => '083.fq',
        'size' => '122703',
        'status' => Upload::STATUS_UPLOADING,
        'location' => 'ftp://downloader-200000:35khksdf@localhost:9021/083.fq',
        'extension' => 'FASTQ',
        'datatype' => 'Protein sequence',
        'created_at' => '2019-08-22 13:02:12',
        'updated_at' => '2019-08-22 13:02:12',
    ],
    [
        'id' => 2,
        'doi' => '200001',
        'name' => '084.fq',
        'size' => '122703',
        'status' => Upload::STATUS_UPLOADING,
        'location' => 'ftp://downloader-200001:27h34tn@localhost:9021/084.fq',
        'extension' => 'FASTQ',
        'datatype' => 'Protein sequence',
        'sample_ids' => 'sample-1,sample-2,sample-3',
        'created_at' => '2019-08-22 13:02:12',
        'updated_at' => '2019-08-22 13:02:12',
    ],
    [
        'id' => 3,
        'doi' => '200001',
        'name' => '085.fq',
        'size' => '122703',
        'status' => Upload::STATUS_UPLOADING,
        'location' => 'ftp://downloader-200001:27h34tn@localhost:9021/085.fq',
        'extension' => 'FASTQ',
        'datatype' => 'Protein sequence',
        'created_at' => '2019-08-22 13:02:12',
        'updated_at' => '2019-08-22 13:02:12',
    ],
];
