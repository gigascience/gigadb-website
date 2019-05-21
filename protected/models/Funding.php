<?php

/**
 * This is the model class for table "funding".
 *
 * The followings are the available columns in table 'funding':
 * @property integer $id
 * @property integer $dataset_id
 * @property integer $funder_id
 * @property string $program_name
 * @property string $grant
 * @property string $pi_name
 *
 * The followings are the available model relations:
 * @property Dataset $dataset
 * @property Funder $funder
 */
class Funding extends CActiveRecord
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'funding';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('dataset_id ,funder_id, program_name, grant, pi_name', 'required'),
			array('dataset_id ,funder_id', 'numerical', 'integerOnly'=>true),
            array('program_name, grant, pi_name', 'length', 'max'=>100),
            array('dataset_id', 'validateDatasetId'),
            array('funder_id', 'validateFunderId'),
		);
	}

    public function validateDatasetId($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $dataset = Dataset::model()->findByPk($this->$attribute);
            $this->dataset = $dataset;

            if (!$dataset) {
                $labels = $this->attributeLabels();
                $this->addError($attribute, $labels[$attribute] . ' doesn\'t exist.');
            }
        }
    }

    public function validateFunderId($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $funder = Funder::model()->findByPk($this->$attribute);
            $this->funder = $funder;

            if (!$funder) {
                $labels = $this->attributeLabels();
                $this->addError($attribute, $labels[$attribute] . ' doesn\'t exist.');
            }
        }
    }

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'funder' => array(self::BELONGS_TO, 'Funder', 'funder_id'),
			'dataset' => array(self::BELONGS_TO, 'Dataset', 'dataset_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'dataset_id' => 'Dataset',
			'funder_id' => 'funder',
			'program_name' => 'Program Name',
			'grant' => 'Grant',
			'pi_name' => 'PI Name',
		);
	}

	public function loadByData($data)
    {
        $this->dataset_id = !empty($data['dataset_id']) ? $data['dataset_id'] : null;
        $this->funder_id = !empty($data['funder_id']) ? $data['funder_id'] : null;
        $this->program_name = !empty($data['program_name']) ? $data['program_name'] : null;
        $this->grant = !empty($data['grant']) ? $data['grant'] : null;
        $this->pi_name = !empty($data['pi_name']) ? $data['pi_name'] : null;
    }

    public function asArray()
    {
        return array(
            'dataset_id' => $this->dataset_id,
            'funder_id' => $this->funder_id,
            'funder_name' => $this->funder->primary_name_display,
            'program_name' => $this->program_name,
            'grant' => $this->grant,
            'pi_name' => $this->pi_name,
        );
    }
}
