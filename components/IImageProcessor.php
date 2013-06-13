<?php 

/**
 * Interface for Siteindex image processor libraries.
 * 
 * @author Dmitry Latikov <dlatikov@promo.ru>
 * @package common.components.utils.images
 * @see PImageProcessor
 */
interface IImageProcessor
{
	/**
	 * Open file for processing.
	 * 
	 * @param string $file File path.
	 * @return PImageProcessor
	 */
	public function load($file);

	/**
	 * Save file.
	 * 
	 * @param string $file File path.
	 * @return PImageProcessor
	 */
	public function save($file);

	/**
	 * Crop image.
	 * 
	 * @param int $x
	 * @param int $y
	 * @param int $width
	 * @param int $height
	 * @return PImageProcessor
	 */
	public function crop($x, $y, $width, $height);

	/**
	 * Make thumbnail, filling given dimensions with scaled and cropped image.
	 * 
	 * @param int $width
	 * @param int $height
	 * @return PImageProcessor
	 */
	public function thumbnail($width, $height);

	/**
	 * Scale image to fit dimensions, if image sizes are over them. 
	 * 
	 * @param int $width
	 * @param int $height
	 * @return PImageProcessor
	 */
	public function scaleDown($width, $height);

	/**
	 * Resize image to given dimensions exactly. This method may break aspect ratio of source image.
	 * 
	 * @param int $width
	 * @param int $height
	 * @return PImageProcessor
	 */
	public function resize($width = null, $height = null);

	/**
	 * Get image dimensions.
	 * 
	 * @return array Returns array with width and height keys.
	 */
	public function getImageGeometry();
}

?>