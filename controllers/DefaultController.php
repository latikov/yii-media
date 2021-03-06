<?php

class DefaultController extends Controller
{
	protected function _printJson($data)
	{
		header('Content-type: application/json');
		exit(CJSON::encode($data));
	}

	public function actionIndex()
	{
		$result = array(
			"success" => false,
			"message" => "Wrong method"
		);
		switch($_SERVER['REQUEST_METHOD']){
			case "POST":
				$result = $this->postMedia($_POST);
				break;
		}
		$this->_printJson($result);
	}

	public function postMedia($data)
	{
		$content = $data['content'];
		$type = $data['type'];

		$imageManager = new ImageManager();
		$result = $imageManager->put($type, $content);

		return array(
			"success" => true,
			"result" => $result
		);
	}

	public function getMedia()
	{

	}


	public function actionTest(){
		$imageContent = file_get_contents("http://cs309227.vk.me/v309227399/58dd/kpbMDh217AI.jpg");
		$type = "test";
		$result = $this->postMedia(array(
			"content" => $imageContent,
			"type" => $type
		));
		$this->_printJson($result);
	}


	
}