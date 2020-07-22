const fs = require('fs');
const fsPath = require('fs-path');
const papa = require('papaparse');

// Global scope
var PROJECT_DIR = "/var/www";
var NEWLINE = "\n";
var INDENT = "    ";

// Sort out command line argument to this script
var CMD_ARGS = process.argv.slice(2);
console.log('CSV directory: ', CMD_ARGS[0]);
var csv_dir = CMD_ARGS[0];
var csv_dir_path = PROJECT_DIR.concat("/data/", csv_dir);

/*
 * Returns file name for Yii migration script based on table name.
 */
const getMigrationFileName = tableName => {
    switch(tableName) {
        case "publisher":
            return "m200529_050000_insert_data_publisher_tab";
        case "image":
            return "m200529_050010_insert_data_image_tab";
        case "gigadb_user":
            return "m200529_050020_insert_data_gigadb_user_tab";
        case "search":
            return "m200529_050030_insert_data_search_tab";
        case "extdb":
            return "m200529_050040_insert_data_extdb_tab";
        case "type":
            return "m200529_050050_insert_data_type_tab";
        case "dataset":
            return "m200529_050060_insert_data_dataset_tab";
        case "AuthItem":
            return "m200529_050070_insert_data_authitem_tab";
        case "AuthAssignment":
            return "m200529_050080_insert_data_authassignment_tab";
        case "attribute":
            return "m200529_050090_insert_data_attribute_tab";
        case "author":
            return "m200529_050100_insert_data_author_tab";
        case "curation_log":
            return "m200529_050110_insert_data_curation_log_tab";
        case "unit":
            return "m200529_050120_insert_data_unit_tab";
        case "dataset_attributes":
            return "m200529_050130_insert_data_dataset_attributes_tab";
        case "dataset_author":
            return "m200529_050140_insert_data_dataset_author_tab";
        case "funder_name":
            return "m200529_050150_insert_data_funder_name_tab";
        case "dataset_funder":
            return "m200529_050160_insert_data_dataset_funder_tab";
        case "dataset_log":
            return "m200529_050170_insert_data_dataset_log_tab";
        case "species":
            return "m200529_050180_insert_data_species_tab";
        case "sample":
            return "m200529_050190_insert_data_sample_tab";
        case "project":
            return "m200529_050200_insert_data_project_tab";
        case "dataset_project":
            return "m200529_050210_insert_data_dataset_project_tab";
        case "dataset_sample":
            return "m200529_050220_insert_data_dataset_sample_tab";
        case "dataset_session":
            return "m200529_050230_insert_data_dataset_session_tab";
        case "dataset_type":
            return "m200529_050240_insert_data_dataset_type_tab";
        case "experiment":
            return "m200529_050250_insert_data_experiment_tab";
        case "exp_attributes":
            return "m200529_050260_insert_data_exp_attributes_tab";
        case "external_link_type":
            return "m200529_050270_insert_data_external_link_type_tab";
        case "external_link":
            return "m200529_050280_insert_data_external_link_tab";
        case "file_format":
            return "m200529_050290_insert_data_file_format_tab";
        case "file_type":
            return "m200529_050300_insert_data_file_type_tab";
        case "file":
            return "m200529_050310_insert_data_file_tab";
        case "file_attributes":
            return "m200529_050320_insert_data_file_attributes_tab";
        case "file_experiment":
            return "m200529_050330_insert_data_file_experiment_tab";
        case "relationship":
            return "m200529_050340_insert_data_relationship_tab";
        case "file_relationship":
            return "m200529_050350_insert_data_file_relationship_tab";
        case "file_sample":
            return "m200529_050360_insert_data_file_sample_tab";
        case "link":
            return "m200529_050370_insert_data_link_tab";
        case "manuscript":
            return "m200529_050380_insert_data_manuscript_tab";
        case "news":
            return "m200529_050390_insert_data_news_tab";
        case "prefix":
            return "m200529_050400_insert_data_prefix_tab";
        case "relation":
            return "m200529_050410_insert_data_relation_tab";
        case "rss_message":
            return "m200529_050420_insert_data_rss_message_tab";
        case "alternative_identifiers":
            return "m200529_050430_insert_data_alternative_identifiers_tab";
        case "sample_attribute":
            return "m200529_050440_insert_data_sample_attribute_tab";
        case "sample_experiment":
            return "m200529_050450_insert_data_sample_experiment_tab";
        case "sample_rel":
            return "m200529_050460_insert_data_sample_rel_tab";
        case "YiiSession":
            return "m200529_050470_insert_data_YiiSession_tab";
        case "user_command":
            return "m200529_050580_insert_data_user_command_tab";
        default:
            throw new Error("No match for table name!");
    }
};

// Configuration to using Papa Parse CSV parser
let config = {
    delimiter: ",",
    newline: "\n",
    quoteChar: '"',
    escapeChar: '"',
    header: true,
    trimHeaders: false,
    skipEmptyLines: true,
};

// A loop to create Yii migration scripts for each CSV file
// containing table data
var files = fs.readdirSync(csv_dir_path);
for(var a = 0; a < files.length; a ++) {
    // Create file paths
    var file_path = PROJECT_DIR.concat("/data/", csv_dir, "/", files[a]);
    var tokens = files[a].split(".");
    var tableName = tokens[0];
    var outfile = PROJECT_DIR.concat("/protected/migrations/data/", csv_dir, "/", getMigrationFileName(tableName), ".php");

    var out = "";
    var ids = [];

    out = out.concat("<?php", NEWLINE);
    out = out.concat(NEWLINE);
    out = out.concat("class ", getMigrationFileName(tableName), " extends CDbMigration", NEWLINE);
    out = out.concat("{", NEWLINE);
    out = out.concat(INDENT, "public function safeUp()", NEWLINE, "    {", NEWLINE);

    var csvHeaderData = fs.readFileSync(file_path, 'utf8');
    // Parse CSV string
    var jsonData = papa.parse(csvHeaderData, config);
    console.log("No. rows: ", jsonData.data.length);
    for(var t = 0; t < jsonData.data.length; t++) {
        for(var h = 0; h < jsonData.meta.fields.length; h++) {
            if(h === 0) {
                out = out.concat(INDENT, INDENT, "$this->insert('", tableName, "', array(", NEWLINE);
            }

            var field = jsonData.meta.fields[h];
            if(field === "id") {
                ids.push(jsonData.data[t][field]);
            }

            var value = jsonData.data[t][field];

            // Hard-code encrypted user password into migration script for testing purposes
            if(field === "password") {
                out = out.concat(INDENT, INDENT, INDENT, "'", field, "' => '5a4f75053077a32e681f81daa8792f95',", NEWLINE);
            }
            else if (value.length === 0) {  // Deal with fields having empty values
                if(h === jsonData.meta.fields.length-1) {  // if field is last one in jsonData.meta.fields array
                    out = out.concat(INDENT, INDENT, "));", NEWLINE);
                }
                continue;
            }
            else {
                var field_value_str = jsonData.data[t][field];
                // Deal with single quote characters in values which causes
                // problems when running Yii migrations
                field_value_str = field_value_str.split("'").join("\\'");
                out = out.concat(INDENT, INDENT, INDENT, "'", field, "' => '", field_value_str, "',", NEWLINE);
            }

            if(h === jsonData.meta.fields.length-1) {
                out = out.concat(INDENT, INDENT, "));", NEWLINE);
            }
        }
    }

    out = out.concat(INDENT, "}", NEWLINE, NEWLINE);
    out = out.concat(INDENT, "public function safeDown()", NEWLINE, "    {", NEWLINE);
    out = out.concat(INDENT, INDENT, "$ids = array(");
    for (var y = 0; y < ids.length; y ++) {
        out = out.concat("'", ids[y], "',");
    }
    out = out.concat(");", NEWLINE);
    out = out.concat(INDENT, INDENT, "foreach ($ids as $id) {", NEWLINE,
        INDENT, INDENT, INDENT, "$this->delete('", tableName, "', 'id=:id', array(':id' => $id));", NEWLINE,
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
