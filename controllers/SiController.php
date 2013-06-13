<?php

class SiController extends CController
{
	public function actionIndex(){
		$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : 'temp';
		$gallery_id = isset($_REQUEST['gallery_id']) ? $_REQUEST['gallery_id'] : null;
		$imageManager = new ImageManager();
		foreach($_FILES as $file){
			if(is_uploaded_file($file['tmp_name'])){
				$content = file_get_contents($file['tmp_name']);
			}
			$imageResult = $imageManager->put($type, $content, null, $gallery_id);
		}
		$imageId = $imageResult['id'];
		$imageSrc = $imageResult['src'];
		$html = "<div style=\"width:150px;height:150px;background: url($imageSrc) no-repeat; background-size: contain;\"></div>";
		if(isset($_GET['model']) && isset($_GET['attribute'])){
			$hiddenFieldName = $_GET['model'] . '[' . $_GET['attribute'] . ']';
			$html .= $this->_renderField($hiddenFieldName, $imageId);
		}
		if($gallery_id){
			$html = $this->_renderGallery($gallery_id);
		}

		$siResult = array(
			"status" => "success",
			"div" => $html
		);
		exit(CJSON::encode($siResult));
	}

	public function actionDelete($id, $gallery_id = null){
		$imageManager = new ImageManager();
		$imageManager->delete($id);

		$siResult = array(
			"status" => "success",
			"div" => $gallery_id ? $this->_renderGallery($gallery_id) : ""
		);
		exit(CJSON::encode($siResult));
	}

	private function _renderField($name, $value)
	{
		return "<input type=\"hidden\" name=\"$name\" value=\"$value\" />";
	}

	private function _renderGallery($id)
	{
		$gallery = MediaGallery::model()->findByPk($id);
		$images = $gallery->images;
		return $this->renderPartial('../../widgets/views/Gallery/list', array(
			'gallery' => $gallery,
			'images' => $images
		), true);
	}

}