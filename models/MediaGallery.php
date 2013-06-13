<?php

/**
 * This is the model class for table "media_gallery".
 *
 * The followings are the available columns in table 'media_gallery':
 * @property integer $id
 * @property string $name
 * @property string $type
 * @package application.models
 */
class MediaGallery extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return MediaGallery the static model class
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
		return 'media_gallery';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'length', 'max'=>255),
			array('type', 'length', 'max'=>128),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, type', 'safe', 'on'=>'search'),
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
			'images' => array(self::HAS_MANY, 'MediaImage', 'gallery_id')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('mediaGallery', 'ID'),
			'name' => Yii::t('mediaGallery', 'Name'),
			'type' => Yii::t('mediaGallery', 'Type'),
		);
	}
		
	/**
	 * @return array MediaGallery data fields. Use it for admin forms.
	 */
	public function fields()
	{
		return array(
			'name' => 'string',
			'type' => 'string',
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
		$criteria->compare('t.name', $this->name, true);
		$criteria->compare('t.type', $this->type, true);
		
		$dataProvider->criteria = $criteria;
		
		return $dataProvider;
	}
}