<?php

class ProxyController extends CController {

	public function actionPlain()
	{
		$query = Yii::app()->request->requestUri;
		$queryParts = explode("/", $query);
		//VarDumper::dump($queryParts);

		list($null, $null, $null, $processingType, $size, $file) = explode("/", $query);
		list($imageId, $imageExtension) = explode(".", $file);
		list($width, $height) = explode("_", $size);

		$imageManager = new ImageManager();

		$content = $imageManager->proxyContent('plain', $width, $height, $imageId, $imageExtension);
		$imageManager->printImage($content, $imageExtension);
	}

}