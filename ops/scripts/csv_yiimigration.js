
// Global scope
var infile = "/Volumes/PLEXTOR/PhpstormProjects/gigadb-website/data/dev/dataset.csv";
var outfile = "/Volumes/PLEXTOR/PhpstormProjects/gigadb-website/tmp/dataset.php";

const fs = require('fs');

var NEWLINE = "\n";
var INDENT = "    ";
var OUT = "";
var COLNAMES = [];
var ids = [];

const processHeader = csv => {
    var lines = csv.split("\n");
    COLNAMES = lines[0].split(",");
    lines.shift();
    return lines;
};

const csvStringToArray = strData => {
    const objPattern = new RegExp(("(\\,|\\r?\\n|\\r|^)(?:\"([^\"]*(?:\"\"[^\"]*)*)\"|([^\\,\\r\\n]*))"),"gi");
    let arrMatches = null, arrData = [[]];
    while (arrMatches = objPattern.exec(strData)){
        if (arrMatches[1].length && arrMatches[1] !== ",") {
            arrData.push([]);
        }
        arrData[arrData.length - 1].push(arrMatches[2] ?
            arrMatches[2].replace(new RegExp( "\"\"", "g" ), "\"") :
            arrMatches[3]);
    }
    return arrData;
};

OUT = OUT.concat("<?php", NEWLINE);
OUT = OUT.concat(NEWLINE);
OUT = OUT.concat("class m200529_084657_insert_data_", "dataset", "_tab extends CDbMigration", NEWLINE);
OUT = OUT.concat("{", NEWLINE);
OUT = OUT.concat(INDENT, "public function safeUp()", NEWLINE, "    {", NEWLINE);
OUT = OUT.concat(INDENT, INDENT, "$this->insert('dataset', array(", NEWLINE);

var text = fs.readFileSync(infile, 'utf8');
var data = processHeader(text);
let dataStrArr = csvStringToArray(data);

for(var x = 0; x < dataStrArr.length; x++) {
    var row = dataStrArr[x];
    for(var i = 0; i < COLNAMES.length; i++) {
        OUT = OUT.concat(INDENT, INDENT, INDENT, "'", COLNAMES[i], "' => '", row[i], "',", NEWLINE);
    }
}
OUT = OUT.concat(INDENT, INDENT, "));", NEWLINE);
OUT = OUT.concat(INDENT, "}", NEWLINE);
OUT = OUT.concat("}", NEWLINE);
console.log(OUT);

