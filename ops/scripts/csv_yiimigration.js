const fs = require('fs');

// Global scope
var infile = "/Volumes/PLEXTOR/PhpstormProjects/gigadb-website/data/dev/dataset.csv";
var outfile = "/Volumes/PLEXTOR/PhpstormProjects/gigadb-website/tmp/output.txt";

var NEWLINE = "\n";
var INDENT = "    ";
var OUT = "";
var COLNAMES = [];
var ids = [];

function eachWithIdx(iterable, f) {
    var i = iterable.iterator();
    var idx = 0;
    while (i.hasNext()) f(i.next(), idx++);
}

function processFile(content) {
    OUT = OUT.concat("<?php", NEWLINE);
    OUT = OUT.concat(NEWLINE);
    OUT = OUT.concat("class m200529_084657_insert_data_", "dataset", "_tab extends CDbMigration", NEWLINE);
    OUT = OUT.concat("{", NEWLINE);
    OUT = OUT.concat(INDENT, "public function safeUp()", NEWLINE, "    {", NEWLINE);
    OUT = OUT.concat(INDENT, INDENT, "$this->insert('dataset', array(", NEWLINE);

    var rows = content.split("\n");
    for(var i = 0; i < rows.length; i++) {
        var cols = rows[i].split(",");
        for(var x = 0; x < cols.length; x++) {
            // Deal with column names
            if (i === 0) {
                COLNAMES.push(cols[x]);
            }
            else {
                OUT = OUT.concat(INDENT, INDENT, INDENT, "'", COLNAMES[x], "' => '", cols[x], "',", NEWLINE);
            }
        }
    }
    console.log(OUT);

    // eachWithIdx(ROWS, function (row, i) {  // work through each row
    //     if(row.first()) {  // Output column names into colNames array
    //         console.log("First row");
    //         eachWithIdx(COLUMNS, function (col, j) {
    //             var value = FORMATTER.format(row, col);
    //             colNames.push(JSON.stringify(value));
    //         });
    //     }
        // else {
        //     eachWithIdx(COLUMNS, function (col, j) {  // then work through cols
        //         var value = FORMATTER.format(row, col);
        //         switch (true) {
        //             case value.toUpperCase() == 'NULL':
        //                 value = null;
        //                 break;
        //             case parseInt(value).toString() == value:
        //                 value = parseInt(value);
        //                 value = value.toString();
        //                 break;
        //             case parseFloat(value).toString() == value:
        //                 value = parseFloat(value);
        //                 value = value.toString();
        //                 break;
        //         }
        //
        //         // Store ids in array
        //         if(colNames[j] == "\"id\"") {
        //             ids.push(value);
        //         }
        //
        //         output(j ? ',' : '', NEWLINE, INDENT, INDENT, INDENT, colNames[j], ' => ', JSON.stringify(value));
        //     });
        //     output(NEWLINE, INDENT, INDENT, "));");
        //     output(NEWLINE, INDENT, '}');
        // }
    // });



    // Write data
    fs.writeFile(outfile, content, (err) => {
        // In case of a error
        if (err) throw err;
    });
}

var text = fs.readFileSync(infile,'utf8');
processFile(text);   // Or put the next step in a function and invoke it







