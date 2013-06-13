<?php

class LocalTransport {
	static function preparePath($sPath){
		if(!file_exists($sPath)){
			mkdir($sPath, 0777, true);
			chmod($sPath, 0777);
		}
	}

	static function load($sPath)
	{
		$sRealPath = Yii::getPathOfAlias('webroot') . $sPath;
		return file_get_contents($sRealPath);
	}

	static function save($sPath, $sContent){
		$sRealPath = Yii::getPathOfAlias('webroot') . $sPath;
		self::preparePath(dirname($sRealPath));
		file_put_contents($sRealPath, $sContent );
		chmod($sRealPath, 0777);
	}

	static function getFullPath($sPath){
		return Yii::getPathOfAlias('webroot') . $sPath;
	}

	static function exists($sPath){
		return (file_exists(Yii::getPathOfAlias('webroot') . $sPath));
	}
}