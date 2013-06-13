<?php

/**
 * This is the model class for table "media_image".
 *
 * The followings are the available columns in table 'media_image':
 * @property integer $id
 * @property string $type
 * @property string $src
 * @property integer $processed
 * @package application.models
 */
class MediaImage extends PActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return MediaImage the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'media_image';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('type', 'required'),
			array('processed', 'numerical', 'integerOnly'=>true),
			array('type', 'length', 'max'=>32),
			array('src', 'length', 'max'=>128),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, type, src, processed', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'gallery' => array(self::BELONGS_TO, 'MediaGallery', 'gallery_id')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('mediaImage', 'ID'),
			'type' => Yii::t('mediaImage', 'Type'),
			'src' => Yii::t('mediaImage', 'Src'),
			'processed' => Yii::t('mediaImage', 'Processed'),
		);
	}
		
	/**
	 * @return array MediaImage data fields. Use it for admin forms.
	 */
	public function fields()
	{
		return array(
			'type' => 'string',
			'src' => 'string',
			'processed' => 'integer',
		);
	}
	
	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @param CActiveDataProvider $dataProvider Optional, for custom extended CActiveDataProvider instance.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search(CActiveDataProvider $dataProvider = null)
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.
		
		if ($dataProvider === null) {
			$dataProvider = new CActiveDataProvider($this);
		}

		$criteria = new CDbCriteria;

		$criteria->compare('t.id', $this->id);
		$criteria->compare('t.type', $this->type, true);
		$criteria->compare('t.src', $this->src, true);
		$criteria->compare('t.processed', $this->processed);
		
		$dataProvider->criteria = $criteria;
		
		return $dataProvider;
	}
}