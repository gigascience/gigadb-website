const fs = require('fs');
var fsPath = require('fs-path');

// Global scope
var project_dir = process.env.PWD;  // Need to run script from gigadb-website project root
var files = fs.readdirSync(project_dir.concat("/data/dev"));
var NEWLINE = "\n";
var INDENT = "    ";

const processHeader = csv => {
    var lines = csv.split("\n");
    COLNAMES = lines[0].split(",");
    lines.shift();
    return lines;
};

const csvStringToArray = strData => {
    const objPattern = new RegExp(("(\\,|\\r?\\n|\\r|^)(?:\"([^\"]*(?:\"\"[^\"]*)*)\"|([^\\,\\r\\n]*))"),"gi");
    let arrMatches = null, arrData = [[]];
    while (arrMatches = objPattern.exec(strData)) {
        if (arrMatches[1].length && arrMatches[1] !== ",") {
            arrData.push([]);
        }
        arrData[arrData.length - 1].push(arrMatches[2] ?
            arrMatches[2].replace(new RegExp( "\"\"", "g" ), "\"") :
            arrMatches[3]);
    }
    return arrData;
};

for(var a = 0; a < files.length; a ++) {
    // Create file paths
    console.log("project_dir: ", project_dir);
    var file_path = project_dir.concat("/data/dev/", files[a]);
    console.log("file_path: ", file_path);
    var tokens = files[a].split(".");
    var table_name = tokens[0];
    console.log("table_name: ", table_name);
    var outfile = project_dir.concat("/protected/migrations/data/dev/", table_name, ".php");
    console.log("outfile: ", outfile);

    var OUT = "";
    var COLNAMES = [];
    var ids = [];

    OUT = OUT.concat("<?php", NEWLINE);
    OUT = OUT.concat(NEWLINE);
    OUT = OUT.concat("class m200529_084657_insert_data_", table_name, "_tab extends CDbMigration", NEWLINE);
    OUT = OUT.concat("{", NEWLINE);
    OUT = OUT.concat(INDENT, "public function safeUp()", NEWLINE, "    {", NEWLINE);
    OUT = OUT.concat(INDENT, INDENT, "$this->insert('dataset', array(", NEWLINE);

    var text = fs.readFileSync(file_path, 'utf8');
    var data = processHeader(text);
    let dataStrArr = csvStringToArray(data);

    for(var x = 0; x < dataStrArr.length; x++) {
        var row = dataStrArr[x];
        for(var i = 0; i < COLNAMES.length; i++) {
            // Put id values for datasets in array
            if(COLNAMES[i] === "id") {
                ids.push(row[i]);
            }
            OUT = OUT.concat(INDENT, INDENT, INDENT, "'", COLNAMES[i], "' => '", row[i], "',", NEWLINE);
        }
    }

    OUT = OUT.concat(INDENT, INDENT, "));", NEWLINE);
    OUT = OUT.concat(INDENT, "}", NEWLINE, NEWLINE);
    OUT = OUT.concat(INDENT, "public function safeDown()", NEWLINE, "    {", NEWLINE);
    OUT = OUT.concat(INDENT, INDENT, "$ids = array(");
    for (var y = 0; y < ids.length; y ++) {
        OUT = OUT.concat("'", ids[y], "'");
    }
    OUT = OUT.concat(");", NEWLINE);
    OUT = OUT.concat(INDENT, INDENT, "foreach ($ids as $id) {", NEWLINE,
        INDENT, INDENT, INDENT, "$this->delete('sample', 'id=:id', array(':id' => $id));", NEWLINE,
        INDENT, INDENT, "}", NEWLINE);

    OUT = OUT.concat(INDENT, "}", NEWLINE);
    OUT = OUT.concat("}", NEWLINE);

    // Output Yii migration script
    fsPath.writeFile(outfile, OUT, function (err) {
        if (err) {
            return console.log(err);
        }
    });
}
