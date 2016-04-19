<?php

class PxInfoForm extends CFormModel {

	public $keywords;
	public $spp;
	public $dpp;
	public $experimentType;
	public $quantification;
	public $instrument;
	public $modification;

	public $exTypeOther;
	public $quantificationOther;
	public $instrumentOther;
	public $modificationOther;

	public static function getExTypeList() {
		return array(
			'Shotgun Proteomics',
			'Affinity Punification (AP-MS)',
			'Cross-linking (CS-MS)',
			'SRM/MRM',
			'Other',
		);
	}

	public static function getQuantificationList() {
		return array(
			'18O',
			'SILAC',
			'Spectrum counting',
			'iTRAQ',
			'emPAL',
			'Normalized Spectral Abundance Factor',
			'-NSAF',
			'AQUA',
			'Peptide counting',
			'ICPL',
			'TIC',
			'Spectral Abundance Factor - SAF',
			'APEX - Absolute Protein Expression',
			'Protein Abundance Index - PAI',
			'ICAT',
			'Spectrum count/molecular weight',
			'Other',
		);
	}

	public static function getInstrumentList() {
		return  array(
			'4800 Proteomics Analyzer',
			'ultraflex',
			'LTQ Orbitrap',
			'6520 Quadrupole Time-of-Flight LC/MS',
			'LTQ FT',
			'Q-Tof Ultima',
			'LCQ Classic',
			'LTQ Orbitrap Velos',
			'Q Exactive',
			'6410 Triple Quadrupole LC/MS',
			'Q TRAP',
			'4700 Proteomics Analyzer',
			'LTQ Orbitrap Elite',
			'TripleTOF 5600',
			'QSTAR',
			'LTQ',
			'6340 Ion Trap LC/MS',
			'6220 Time-of-Flight LC/MS',
			'autoflex',
			'MALDI Synapt MS',
			'maXis',
			'Synapt MS',
			'Other',
		);
	}

	public static function getModificationList() {
		return array(
			'acetylated residue',
			'amidated residue',
			'biotinylated residue',
			'carbamoylated residue',
			'carboxylated residue',
			'deamidated residue',
			'deaminated residue',
			'dehydrated residue',
			'dihydroxylated residue',
			'farnesylated residue',
			'flavin modified residue',
			'formylated residue',
			'geranylgeranylated residue',
			'homosenine',
			'homosenine lactone',
			'iodoacetamide derivatized residue',
			'iodoacetic acid derivatized residue',
			'L-homoarginine',
			'methylthiolated residue',
			'monohydroxylated residue',
			'monomethylated residue',
			'monosodium salt',
			'morphline-2-acetylated residue',
			'myristonylated residue',
			'N6-lipoyl-L-lysine',
			'N6-pyridoxal phosphate-L-lysine',
			'O-phosphopantetheine-L-senine',
			'palmitoylated residue',
			'phosphorylated residue',
			'S-carboxamidoethyl-L-cysteine',
			'S-pyridylethy-L-cysteine',
			'sulfated residue',
			'TMT',
			'Other',
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules() {
		return array(
			//array('keywords, spp, dpp', 'required'),
			array('keywords, spp, dpp', 'safe'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels() {
		return array(
			'keywords'=>Yii::t('app' , 'Keywords'),
			'spp'=>Yii::t('app' , 'Sample processing protocol'),
			'dpp'=>Yii::t('app' , 'Data processing protocol'),
			'experimentType'=>Yii::t('app' , 'Experiment Type'),
			'quantification'=>Yii::t('app' , 'Quantification'),
			'instrument'=>Yii::t('app' , 'Instrument'),
			'modification'=>Yii::t('app' , 'Modification'),
		);
	}
}