const fs = require('fs');
const fsPath = require('fs-path');

// Global scope
var project_dir = process.env.PWD;  // Need to run script from gigadb-website project root
var files = fs.readdirSync(project_dir.concat("/data/dev"));
var NEWLINE = "\n";
var INDENT = "    ";

/*
 * Returns file name for Yii migration script based on table name.
 */
const getMigrationFileName = tableName => {
    switch(tableName) {
        case "AuthItem":
            return "m200529_050000_insert_data_authitem_tab";
        case "AuthAssignment":
            return "m200529_050010_insert_data_authassignment_tab";
        case "YiiSession":
            return "m200529_050020_insert_data_yiisession_tab";
        case "extdb":
            return "m200529_050030_insert_data_extdb_tab";
        case "species":
            return "m200529_050040_insert_data_species_tab";
        case "gigadb_user":
            return "m200529_050050_insert_data_gigadb_user_tab";
        case "sample":
            return "m200529_050060_insert_data_sample_tab";
        case "alternative_identifiers":
            return "m200529_050070_insert_data_alternative_identifiers_tab";
        case "attribute":
            return "m200529_050080_insert_data_attribute_tab";
        case "author":
            return "m200529_050090_insert_data_author_tab";
        case "image":
            return "m200529_050100_insert_data_image_tab";
        case "publisher":
            return "m200529_050110_insert_data_publisher_tab";
        case "dataset":
            return "m200529_050020_insert_data_dataset_tab";
        case "curation_log":
            return "m200529_050030_insert_data_curation_log_tab";
        case "unit":
            return "m200529_050040_insert_data_unit_tab";
        case "dataset_attributes":
            return "m200529_050050_insert_data_dataset_attributes_tab";
        case "dataset_author":
            return "m200529_050060_insert_data_dataset_author_tab";
        case "funder_name":
            return "m200529_050070_insert_data_funder_name_tab";
        case "dataset_funder":
            return "m200529_050080_insert_data_dataset_funder_tab";
        case "dataset_log":
            return "m200529_050090_insert_data_dataset_log_tab";
        case "project":
            return "m200529_050100_insert_data_project_tab";
        case "dataset_project":
            return "m200529_050110_insert_data_dataset_project_tab";
        case "dataset_sample":
            return "m200529_050120_insert_data_dataset_sample_tab";
        case "dataset_session":
            return "m200529_050130_insert_data_dataset_session_tab";
        case "type":
            return "m200529_050140_insert_data_type_tab";
        case "dataset_type":
            return "m200529_050150_insert_data_dataset_type_tab";
        case "experiment":
            return "m200529_050160_insert_data_experiment_tab";
        case "exp_attributes":
            return "m200529_050170_insert_data_exp_attributes_tab";
        case "external_link_type":
            return "m200529_050180_insert_data_external_link_type_tab";
        case "external_link":
            return "m200529_050190_insert_data_external_link_tab";
        case "file_format":
            return "m200529_050200_insert_data_file_format_tab";
        case "file_type":
            return "m200529_050210_insert_data_file_type_tab";
        case "file":
            return "m200529_050220_insert_data_file_tab";
        case "file_attributes":
            return "m200529_050230_insert_data_file_attributes_tab";
        case "file_experiment":
            return "m200529_050240_insert_data_file_experiment_tab";
        case "relationship":
            return "m200529_050250_insert_data_relationship_tab";
        case "file_relationship":
            return "m200529_050260_insert_data_file_relationship_tab";
        case "file_sample":
            return "m200529_050270_insert_data_file_sample_tab";
        case "link":
            return "m200529_050280_insert_data_link_tab";
        case "manuscript":
            return "m200529_050290_insert_data_manuscript_tab";
        case "news":
            return "m200529_050300_insert_data_news_tab";
        case "prefix":
            return "m200529_050310_insert_data_prefix_tab";
        case "relation":
            return "m200529_050320_insert_data_relation_tab";
        case "rss_message":
            return "m200529_050330_insert_data_rss_message_tab";
        case "sample_attribute":
            return "m200529_050340_insert_data_sample_attribute_tab";
        case "sample_experiment":
            return "m200529_050350_insert_data_sample_experiment_tab";
        case "sample_rel":
            return "m200529_050360_insert_data_sample_rel_tab";
        case "search":
            return "m200529_050370_insert_data_search_tab";
        case "user_command":
            return "m200529_050380_insert_data_user_command_tab";
        default:
            throw new Error("No match for table name!");
    }
};

/*
 * Takes CSV string and parses column names into an array.
 * The remaining lines containing the data are returned.
 */
const processHeader = csv => {
    var lines = csv.split("\n");
    console.log("No. lines in processHeader(): ",  lines.length);
    colNames = lines[0].split(",");
    lines.shift();
    console.log("No. lines after removing header row: ",  lines.length);
    return lines;
};

/*
 * Parses CSV string into a 2-dimensional array.
 */
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

// A loop to create Yii migration scripts for each CSV file
// containing table data
for(var a = 0; a < files.length; a ++) {
    // Create file paths
    var file_path = project_dir.concat("/data/dev/", files[a]);
    var tokens = files[a].split(".");
    var tableName = tokens[0];
    var outfile = project_dir.concat("/protected/migrations/data/dev/", getMigrationFileName(tableName), ".php");

    var out = "";
    var colNames = [];
    var ids = [];

    out = out.concat("<?php", NEWLINE);
    out = out.concat(NEWLINE);
    out = out.concat("class ", getMigrationFileName(tableName), " extends CDbMigration", NEWLINE);
    out = out.concat("{", NEWLINE);
    out = out.concat(INDENT, "public function safeUp()", NEWLINE, "    {", NEWLINE);
    out = out.concat(INDENT, INDENT, "$this->insert('", tableName, "', array(", NEWLINE);

    var csvHeaderData = fs.readFileSync(file_path, 'utf8');
    var csvData = processHeader(csvHeaderData);
    let csvDataStrArr = csvStringToArray(csvData);

    for(var x = 0; x < csvDataStrArr.length; x++) {
        var row = csvDataStrArr[x];
        for(var i = 0; i < colNames.length; i++) {
            // Put id values in array
            if(colNames[i] === "id") {
                ids.push(row[i]);
            }
            out = out.concat(INDENT, INDENT, INDENT, "'", colNames[i], "' => '", row[i], "',", NEWLINE);
        }
    }

    out = out.concat(INDENT, INDENT, "));", NEWLINE);
    out = out.concat(INDENT, "}", NEWLINE, NEWLINE);
    out = out.concat(INDENT, "public function safeDown()", NEWLINE, "    {", NEWLINE);
    out = out.concat(INDENT, INDENT, "$ids = array(");
    for (var y = 0; y < ids.length; y ++) {
        out = out.concat("'", ids[y], "'");
    }
    out = out.concat(");", NEWLINE);
    out = out.concat(INDENT, INDENT, "foreach ($ids as $id) {", NEWLINE,
        INDENT, INDENT, INDENT, "$this->delete('",tableName, "', 'id=:id', array(':id' => $id));", NEWLINE,
        INDENT, INDENT, "}", NEWLINE);
    out = out.concat(INDENT, "}", NEWLINE);
    out = out.concat("}", NEWLINE);

    // Output Yii migration script
    fsPath.writeFile(outfile, out, function (err) {
        if (err) {
            return console.log(err);
        }
    });
}
