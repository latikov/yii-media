<?php

class TestController extends Controller
{
	public function actionPost(){
		$imageContent = file_get_contents("http://cs309227.vk.me/v309227399/58dd/kpbMDh217AI.jpg");
		$type = "test";
		$this->postMedia(array(
			"content" => $imageContent,
			"type" => $type
		));
	}



}