<?php 

Yii::import('media.components.IImageProcessor');
Yii::import('media.components.ImageProcessor');

/**
 * SiteIndex image processor ImageMagick implementation.
 *
 * @author Dmitry Latikov <dlatikov@promo.ru>
 * @package common.components.utils.images
 * @link http://www.imagemagick.org/
 */
class ImageProcessorImagick extends ImageProcessor implements IImageProcessor
{

	public function create($content = null){
		$imagick = new Imagick();
		if($content){
			$imagick->readImageBlob($content);
		}
		$this->_imageHander = $imagick;
		return $this;
	}



	public function getContent()
	{
		$this->_imageHander->stripImage();

		$this->_imageHander->setCompressionQuality($this->_quality);

		return $this->_imageHander->getImageBlob();
	}

	/**
	 * Crop image.
	 * 
	 * @param int $width
	 * @param int $height
	 * @param int $x
	 * @param int $y
	 * @throws ImagickException
	 * @return PImageProcessorImagick
	 */
	public function crop($width, $height, $x, $y)
	{
		$this->_imageHander->cropImage( $width, $height, $x, $y);
		$this->_imageHander->setImagePage(0, 0, 0, 0);
		return $this;
	}

	/**
	 * Scale image to fit dimensions, if image sizes are over them. 
	 * 
	 * @param int $width
	 * @param int $height
	 * @throws ImagickException
	 * @return PImageProcessorImagick
	 */
	public function scaleDown($width, $height)
	{
		$originalSizes = $this->getImageGeometry();
		
		if ($originalSizes['width'] > $width || $originalSizes['height'] > $height) {
			$this->_imageHander->scaleImage($width, $height, true);
			$this->_imageHander->setImagePage(0, 0, 0, 0);
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
		return $this->_imageHander->getImageGeometry();
	}

	/**
	 * Resize image to given dimensions exactly. This method may break aspect ratio of source image.
	 * 
	 * @param int $width
	 * @param int $height
	 * @throws ImagickException
	 * @return PImageProcessorImagick
	 */
	public function resize($width = null, $height = null)
	{
		$this->_imageHander->resizeImage($width, $height, Imagick::FILTER_LANCZOS, 1);
		$this->_imageHander->setImagePage(0, 0, 0, 0);
		return $this;
	}
	
	/**
	 * @see PImageProcessor::thumbnail()
	 */
	public function thumbnail($width, $height)
	{
		parent::thumbnail($width, $height);
		
		if ($width < 300) {
			$this->_imageHander->sharpenImage(0, 0.5);
		}
		
		return $this;
	}

	/**
	 * Make round corners.
	 * 
	 * @param int $xRadius
	 * @param int $yRadius
	 * @throws ImagickException
	 * @return PImageProcessorImagick
	 */
	public function roundCorners($xRadius, $yRadius){
		$this->_imageHander->roundCorners($xRadius, $yRadius);
		$this->_imageHander->setImagePage(0, 0, 0, 0);
		return $this;
	}


	/**
	 * Set background color.
	 * 
	 * @param string $color
	 * @throws ImagickException
	 * @return PImageProcessorImagick
	 */
	public function backgroundColor($color = 'black')
	{
		$bgCanvas = new Imagick();

		$bgCanvas->newimage($this->_imageHander->getImageWidth(),  $this->_imageHander->getImageHeight(), $color);
		$bgCanvas->compositeImage($this->_imageHander, Imagick::COMPOSITE_OVER, 0, 0);

		$this->_imageHander->destroy();
		$this->_imageHander = $bgCanvas;
		return $this;
	}

	/**
	 * Custom ImageMagick command.
	 * 
	 * E.g.:
	 * <pre>
	 * Yii::app()->imageProcessor->load($fromFile)->imagick('reduceNoiseImage', 2)->save($toFile);
	 * </pre>
	 * 
	 * @throws ImagickException
	 * @return PImageProcessorImagick
	 */
	public function imagick()
	{
		$args = func_get_args();
		$method = array_shift($args);
		call_user_func_array(array($this->_imageHander , $method), $args);
		return $this;
	}
}

?>