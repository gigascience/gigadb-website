/**
 * Transforms CSV files in a directory into Yii migration scripts
 */

const fs = require('fs');
const fsPath = require('fs-path');
const papa = require('papaparse');
const handlebars = require('handlebars');
const jp = require('jsonpath');

// Global scope
let CMD_ARGS = process.argv.slice(2);
let PROJECT_DIR = "/var/www";
let INPUT_CSV_DIR = PROJECT_DIR + "/data/" + CMD_ARGS[0];
let OUTPUT_MIGRATION_SCRIPT_DIR = PROJECT_DIR + "/protected/migrations/data/" + CMD_ARGS[0];
let HANDLEBARS_TEMPLATE_FILE = PROJECT_DIR + "/ops/configuration/yii-conf/migration.php.dist";

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

// Configuration for using Papa Parse CSV parser
let config = {
    delimiter: ",",
    newline: "\n",
    quoteChar: '"',
    escapeChar: '"',
    header: true,
    trimHeaders: false,
    skipEmptyLines: true,
};

// Main program
var files = fs.readdirSync(INPUT_CSV_DIR);
for(let a = 0; a < files.length; a ++) {
    // Create file paths
    let csvFile = INPUT_CSV_DIR + "/" + files[a];
    let tokens = files[a].split(".");
    let tableName = tokens[0];
    let outputMigrationFile = OUTPUT_MIGRATION_SCRIPT_DIR + "/" + getMigrationFileName(tableName) + ".php";
    let csvHeaderData = fs.readFileSync(csvFile, 'utf8');
    // Parse CSV string
    let jsonData = papa.parse(csvHeaderData, config);
    let ids = jp.query(jsonData, '$..id');
    // Remove empty strings in ids array
    var filtered_ids = ids.filter(function (el) {
        return el !== "";
    });
    var data = JSON.stringify(jsonData);
    var parsed = JSON.parse(data);
    let context = {
        class_name: getMigrationFileName(tableName),
        table_name: tokens[0],
        safeup_data: parsed.data,
        safedown_data: filtered_ids
    };
    // Read handlebars template as string
    let template = fs.readFileSync(HANDLEBARS_TEMPLATE_FILE, "utf8");
    const templateScript = handlebars.compile(template);
    // Output Yii migration script
    fsPath.writeFile(outputMigrationFile, templateScript(context), function (err) {
        if (err) {
            return console.log(err);
        }
    });
}
