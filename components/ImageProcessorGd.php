<?php

Yii::import('media.components.IImageProcessor');
Yii::import('media.components.ImageProcessor');

/**
 * SiteIndex image processor GD implementation.
 *
 * @author Dmitry Latikov <dlatikov@promo.ru>
 * @package common.components.utils.images
 * @link http://php.net/manual/ru/book.image.php
 */
class ImageProcessorGd extends ImageProcessor implements IImageProcessor
{

	public function create($content)
	{
		$this->_imageHander = imagecreatefromstring($content);
		return $this;
	}

	/**
	 * Save file.
	 * 
	 * @param string $file File path.
	 * @return PImageProcessorImagick
	 */
	public function save($file)
	{
		imagejpeg($this->_imageHander, $file, $this->_quality);
		imagedestroy($this->_imageHander);
		chmod($file, 0777);
		return $this;
	}

	public function getContent(){
		ob_start();
		imagejpeg($this->_imageHander, null, $this->_quality);
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}

	public function destroy(){
		imagedestroy($this->_imageHander);
	}

	/**
	 * Crop image.
	 * 
	 * @param $width
	 * @param $height
	 * @param $x
	 * @param $y
	 * @return PImageProcessorImagick
	 */
	public function crop($width, $height, $x, $y)
	{
		$origSize = $this->getImageGeometry();
		$newImage = imagecreatetruecolor($width, $height);
		imagecopy ($newImage, $this->_imageHander, 0, 0, $x, $y, $width, $height);

		imagedestroy($this->_imageHander);
		$this->_imageHander = $newImage;
		return $this;
	}

	/**
	 * Scale image to fit dimensions, if image sizes are over them. 
	 * 
	 * @param $width
	 * @param $height
	 * @return PImageProcessorImagick
	 */
	public function scaleDown($width, $height)
	{
		$srcSize = $this->getImageGeometry();
		
		if ($width < $srcSize['width'] || $height < $srcSize['height']) {
			if ($width / $srcSize['width'] > $height / $srcSize['height']) {
				$this->resize(null, $height);
			} else {
				$this->resize($width, null);
			}
		}

		return $this;
	}

	/**
	 * Get image dimensions.
	 * 
	 * @return array Returns array with width and height keys.
	 */
	public function getImageGeometry()
	{
		return array(
			'width' => imagesx($this->_imageHander),
			'height' => imagesy($this->_imageHander)
		);
	}

	/**
	 * Resize image to given dimensions exactly. This method may break aspect ratio of source image.
	 * 
	 * @param null $width
	 * @param null $height
	 * @return PImageProcessorImagick
	 */
	public function resize($width = null, $height = null)
	{
		$srcSize = $this->getImageGeometry();
		$srcSizeRatio = $srcSize['width'] / $srcSize['height'];
		
		if (!$width && !$height) {
			throw new CException(Yii::t('app', 'You must specify at least width or height for resize.'));
		} elseif (!$width) {
			$width = $height * $srcSizeRatio;
		} elseif (!$height) {
			$height = $width / $srcSizeRatio;
		}

		$newImage = imagecreatetruecolor($width, $height);
		imagecopyresampled($newImage, $this->_imageHander, 0, 0, 0, 0, $width, $height, $srcSize['width'], $srcSize['height']);

		imagedestroy($this->_imageHander);
		$this->_imageHander = $newImage;
		return $this;
	}

}

?>