<?php

class ImageManager extends CComponent{

	public $config = null;
	private $_contentTypes = array(
		"jpe" => "image/jpeg",
		"jpeg" => "image/jpeg",
		"jpg" => "image/jpeg",
		"png" => "image/png",
		"gif" => "image/gif"
	);
	private $_transport = null;
	private $_processor = null;

	public function init()
	{
		Yii::import('application.image.transport.LocalTransport');
	}

	public function put($type, $imageContent, $sPath = null, $iGallery = null)
	{
		$imageModel = $this->_getModel($type);
		if(!$sPath){
			$sPath = $this->getOriginalPath($type, $imageModel->id);
		}
		$this->transport->save($sPath, $imageContent);
		$imageModel->src =$sPath;
		$imageModel->processed = 2;
		$imageModel->gallery_id = $iGallery;
		$imageModel->save();
		$this->makeVariants($imageModel);
		$imageModel->processed = 1;
		$imageModel->save();

		return array(
			"id" => $imageModel->id,
			"src" => $sPath
		);
	}

	public function getOriginalPath($type, $id){
		$hash = substr(md5($id), 0, 2);
		$extension = "jpg";
		return "/i/o/$type/$hash/$id.$extension";
	}

	public function getVariantPath($type, $variant, $id){
		$hash = substr(md5($id), 0, 2);
		$extension = "jpg";
		return "/i/o/$type/$hash/$variant/$id.$extension";
	}

	/*
	 * @param Image $imageModel
	 */
	public function makeVariants($imageModel){
		$variants = $this->_getVariants($imageModel->type);
		if($variants){
			$this->processor->transport = $this->transport;
			foreach($variants as $variantKey => $variantConfig){
				$variantPath = $this->getVariantPath($imageModel->type, $variantKey, $imageModel->id);
				$this->processor->process($imageModel->src, $variantPath, $variantConfig);
			}
		}
	}

	public function proxyPath($processingType, $width, $height, $id, $extension = "jpg")
	{
		$proxyPath = "/i/proxy/$processingType/{$width}x{$height}/{$id}.{$extension}";
		if(!$this->transport->exists($proxyPath)){
			$this->processor->transport = $this->transport;

			$image = MediaImage::model()->findByPk($id);
			$this->processor->load( $image->src );
			$this->processor->thumbnail($width, $height);
			$this->processor->save( $proxyPath );
		}
		return $proxyPath;
	}

	public function proxyContent($processingType, $width, $height, $id, $extension = "jpg")
	{
		$proxyPath = $this->proxyPath($processingType, $width, $height, $id, $extension);
		$transport = $this->transport;
		return file_get_contents( $transport->getFullPath( $proxyPath ) );
	}

	public function printImage($content, $extension)
	{
		$mimeType = isset($this->_contentTypes[$extension]) ? $this->_contentTypes[$extension] : ("image/" . $extension);
		header("Content-type: $mimeType");
		exit($content);
	}

	public function delete($id)
	{
		$imageModel = MediaImage::model()->findByPk($id);
		if($imageModel){
			$imageModel->delete();
			return array ("result" => true);
		} else {
			return array ("result" => false);
		}
	}

	protected function getTransport(){
		if(!$this->_transport){
			$mediaModule = Yii::app()->getModule('media');
			$transportType = ucfirst ($mediaModule->transport);
			Yii::import("media.components.transport.{$transportType}Transport");
			$class = $transportType . "Transport";
			$this->_transport = new $class();
		}
		return $this->_transport;
	}

	protected function getProcessor(){
		if(!$this->_processor){
			$mediaModule = Yii::app()->getModule('media');
			$class = "ImageProcessor" . ucfirst($mediaModule->processor);
			$this->_processor = new $class();
		}
		return $this->_processor;
	}

	private function _getModel($type){
		$model = new MediaImage();
		$model->type = $type;
		$model->save();
		return $model;
	}

	private function _getVariants($type){
		if(!isset($this->config[$type])){
			return null;
			//throw new CException("ImageManager: unknown type [$type]");
		}
		return $this->config[$type];
	}



}