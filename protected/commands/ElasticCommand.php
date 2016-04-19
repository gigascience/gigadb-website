<?php
function __autoload_elastica ($class) {
  $path = str_replace('\\', '/', $class);
  $path = Yii::getPathOfAlias('Elastica') . "/{$path}.php";
  Yii::log($path, 'debug');
  if (file_exists($path)) {
    require_once($path);
  }
}

spl_autoload_unregister(array('YiiBase','autoload'));
spl_autoload_register('__autoload_elastica');
spl_autoload_register(array('YiiBase','autoload'));

class ElasticCommand extends CConsoleCommand
{
    const BULK = 500;
    const DATASET_QUERY = 
<<<EOF
    with dprojects as (select dataset_id, array_agg(p.name) as names from dataset_project dp, project p where dp.project_id = p.id group by dataset_id),
    dauthors as (select dataset_id, array_agg(a.*) as author_names from dataset_author da, author a where da.author_id = a.id group by dataset_id),
    dattributes as (select dataset_id, value, array_agg(a.*) as definition from dataset_attributes da, attribute a where da.attribute_id = a.id group by dataset_id, value),
    dlinks as (select dataset_id, array_agg(elt.name) as link_types from external_link el, external_link_type elt where el.external_link_type_id = elt.id group by dataset_id),
    dmanuscripts as (select dataset_id, array_agg(identifier) as identifiers from manuscript group by dataset_id),
    dtypes as (select dataset_id, array_agg(t.name) as typenames from dataset_type dt, type t where dt.type_id = t.id group by dataset_id) 
    
    select d.id, d.dataset_size, d.description, d.excelfile, d.ftp_site, d.title, d.publication_date, 
    d.modification_date, d.identifier, d.upload_status, 
    dauthors.author_names as authors, dprojects.names as projects, dattributes.value as attr_value, dattributes.definition as attr_def,  
    dmanuscripts.identifiers as manuscripts, publisher.name as pub_name, publisher.description as pub_desc, dtypes.typenames,
    dlinks.link_types  
    from dataset d 
    left join dprojects on d.id = dprojects.dataset_id 
    left join dauthors on d.id = dauthors.dataset_id 
    left join dattributes on d.id = dattributes.dataset_id 
    left join publisher on d.publisher_id = publisher.id 
    left join dmanuscripts on d.id = dmanuscripts.dataset_id 
    left join dtypes on d.id = dtypes.dataset_id
    left join dlinks on d.id = dlinks.dataset_id 
    where d.upload_status = 'Published' 
    limit :limit offset :offset
EOF;
    
    const FILE_QUERY = 
<<<EOF
    select file.id, file.dataset_id, file.name, file.size, file.description, 
    file_format.name as format, file_type.name as filetype 
    from file  
    left join file_format on file.format_id = file_format.id 
    left join file_type on file.type_id = file_type.id 
    left join dataset d on d.id = file.dataset_id 
    where d.upload_status = 'Published' 
    limit :limit offset :offset
EOF;

    const SAMPLE_QUERY = 
<<<EOF
    with sattrs as (select sample_id, value, array_agg(a.*) as definition from sample_attribute sa, attribute a where sa.attribute_id = a.id group by sample_id, value),
    
    sexperiment as (select se.sample_id, array_agg(e.*) as exps, array_agg(ea.value) as exp_attrs, array_agg(a.*) as exp_attr_def 
        from sample_experiment se left join experiment e on se.experiment_id = e.id left join exp_attributes ea on e.id = ea.exp_id 
        left join attribute a on ea.attribute_id = a.id group by se.sample_id, e.id)

    select s.id, ds.dataset_id, s.name, s.consent_document, s.contact_author_name, s.contact_author_email, s.submission_date, s.sampling_protocol,
    sattrs.value as attr_value, sattrs.definition as attr_def, sp.common_name, sp.genbank_name, sp.scientific_name, sp.eol_link,
    sexp.exps, sexp.exp_attrs, sexp.exp_attr_def 
    from sample s
    left join sexperiment sexp on s.id = sexp.sample_id
    left join dataset_sample ds on ds.sample_id = s.id 
    left join sattrs on s.id = sattrs.sample_id 
    left join species sp on sp.id = s.species_id 
    left join dataset d on d.id = ds.dataset_id 
    where d.upload_status = 'Published' 
    limit :limit offset :offset
EOF;

    public function actionClearIndex() {
        $elastic = Yii::app()->elastic;
        $index = $elastic->client->getIndex('gigadb');
        if($index->exists()) {
          $index->delete();
        }
        $elastic->createIndex();
        $elastic->createDatasetMapping();
        $elastic->createSampleMapping();
        $elastic->createFileMapping();

    }

    public function addDocuments($typename, $query, $format) {
        $elastic = Yii::app()->elastic;
        $index = $elastic->client->getIndex('gigadb');
        $type = $index->getType($typename);

        $command = Yii::app()->db->createCommand($query);
        $offset = 0;
        $limit = $this::BULK;
        while(True) {
            $rows = $command->query(array(':offset'=>$offset, ':limit'=>$limit));
            $num = count($rows);
            //Yii::log(print_r($num, true), 'debug');
            
            $documents = array();
            foreach($rows as $row) {
                $doc = $format($row);
                $documents[] = $elastic->createDocument(
                    $row['id'],
                    $doc
                );
            }
            if($num > 0)
                $type->addDocuments($documents);
            $offset += $limit;
            if($num < $limit)
                break;
        }
        $index->refresh();
    }

    public function actionAddGigaDocument() {
        $format = function($row) {
            $date_columns = array('publication_date', 'modification_date', 'submission_date','date_stamp');
            $results = array();
            $DATE_FORMAT = "%Y%m%d";
            foreach($row as $key=>$val) {
                if(in_array($key, $date_columns))
                    $results[$key] = strftime($DATE_FORMAT, strtotime($val));
                else
                    $results[$key] = $val;
            }
            return $results;
        };
        $this->addDocuments("dataset", $this::DATASET_QUERY, $format);
        echo "Finish adding dataset\n";
        $this->addDocuments("file", $this::FILE_QUERY, $format);
        echo "Finish adding file\n";
        $this->addDocuments("sample", $this::SAMPLE_QUERY, $format);
        echo "Finish adding sample\n";
    }
}

?>
