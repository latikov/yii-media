<?php
/**
 * Created by JetBrains PhpStorm.
 * User: admin
 * Date: 03.06.13
 * Time: 15:26
 * To change this template use File | Settings | File Templates.
 */

class Gallery extends CWidget{

	public $id = null;
	public $type = "gallery";

	public function init()
	{

	}

	public function run()
	{
		$gallery = MediaGallery::model()->findByPk($this->id);
		$images = $gallery->images;
		$this->render('Gallery/index', array(
			'gallery' => $gallery,
			'images' => $images,
			'options' => array()
		));
	}
}