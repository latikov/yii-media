<?php 

/**
 * SiteIndex image processor master class.
 * 
 * Add this to config for ImageMagick image processor:
 * <pre>
 * 'components' => array(
 *    'imageProcessor' => array(
 *       'class' => 'common.components.utils.images.PImageProcessorImagick'
 *    )
 * )
 * </pre>
 * 
 * Usage example:
 * <pre>
 * Yii::app()->imageProcessor->load($fromFile)->thumbnail(300, 200)->setQuality(70)->save($toFile);
 * </pre>
 * 
 * @author Dmitry Latikov <dlatikov@promo.ru>
 * @package common.components.utils.images
 * @see IImageProcessor, PImageActiveRecord
 * 
 * @property string $quality Image quality
 */
abstract class ImageProcessor extends CApplicationComponent
{
	/**
	 * @var int Quality for jpeg image
	 */
	protected $_quality = 90;

	public $transport = null;

	/**
	 * @var mixed Image processing object, e.g. Imagick object
	 */
	protected $_imageHander = NULL;

	public function load($file)
	{
		if($this->transport){
			$content = $this->transport->load($file);
		} else {
			$content = file_get_contents($file);
		}
		return $this->create($content);
	}

	public function save($file)
	{
		$content = $this->getContent();
		if($this->transport){
			$this->transport->save($file, $content);
		} else {
			file_put_contents($file, $content);
		}
	}


	/**
	 * Load image from source file, apply array of commands to it and save result to target file.
	 * 
	 * E.g.:
	 * <pre>
	 * Yii::app()->imageProcessor->process(
	 *    $fromFile,
	 *    $toFile,
	 *    array(
	 *    	 "thumbnail" => array("width" => 100, "height" => 100),
	 *       "crop" => array("width" => 50, "height" => 50, "x" => 10, "y" => 10)
	 *    )
	 * );
	 * </pre>
	 * 
	 * @param string $sourceFile
	 * @param string $targetFile
	 * @param array $commandArray
	 */
	public function process($sourceFile, $targetFile, $commandArray)
	{
		$this->load($sourceFile);

		if(isset($commandArray['width']) && isset($commandArray['height'])){
			$commandArray = $this->_upgradeSimplifiedFormat($commandArray);
		}
		foreach($commandArray as $command => $params){
			if($command){
				$this->executeCommand($command, $params);
			}
		}

		$this->save($targetFile);
	}

	/**
	 * Execute processor method with given params.
	 * 
	 * @param string $command Method name, e.g. 'scaleDown'.
	 * @param array $params Array of method params, e.g. array('width' => 400, 'height' => 300).
	 * @throws CException
	 */
	public function executeCommand($command, $params = array())
	{
		// _comand syntax for same command to use multiple times
		$command = ltrim($command, '_');
		
		if(!is_array($params)){
			$params = array( $params );
		}

		$classReflection =  new ReflectionClass($this);

		if (!$classReflection->hasMethod($command)){
			throw new CException(Yii::t('app', 'Image processor method "{method}" not found', array('{method}' => $command)));
		}

		if(is_array($params)){
			$methodReflection = $classReflection->getMethod($command);
			$methodParams = $methodReflection->getParameters();

			$callArgs = array();

			if(empty($methodParams)){
				$callArgs = $params;
			} else {
				foreach($methodParams as $methodParam) {
					if(isset($params[$methodParam->getName()])) {
						$callArgs[] = $params[$methodParam->getName()];
					} else {
						if ($methodParam->isOptional()) {
							$callArgs[] = $methodParam->getDefaultValue();
						} else {
							throw new CException(Yii::t('app', 'Image processor method "{method}" required parameter "{param}" missing', array('{method}' => $command, '{param}' => $methodParam->getName())));
						}
					}
				}
			}

			$methodReflection->invokeArgs($this, $callArgs);
		} else {
			// setter for processor property
			$this->$command = $params;
		}

	}

	/**
	 * Scale image to fit dimensions, if image sizes are over them. 
	 * 
	 * @param int $width
	 * @param int $height
	 * @return PImageProcessor
	 */
	public function thumbnail($width, $height)
	{
		$originalSizes = $this->getImageGeometry();

		if (
			$originalSizes['width'] * $height / $originalSizes['height'] >= $width
		){
			$this->resize(null, $height);
		} else {
			$this->resize($width, null);
		}

		$scaledSizes = $this->getImageGeometry();

		$this->crop(
			$width,
			$height,
			floor(($scaledSizes['width'] - $width) / 2),
			floor(($scaledSizes['height'] - $height) / 2)
		);

		return $this;
	}

	/**
	 * Set output compression quality.
	 * 
	 * @param $quality
	 */
	public function setQuality($quality)
	{
		$this->_quality = $quality;
	}

	/**
	 * Backward compatibility "process" alias.
	 * 
	 * @param string $sourceFile
	 * @param string $targetFile
	 * @param array $commandArray
	 * @deprecated Used for backward compatibility with v3.0 only. Use thumbnail method instead.
	 */
	public function writeImageCropedThumbnail($sourceFile, $targetFile, $commandArray)
	{
		$this->process($sourceFile, $targetFile, $commandArray);
	}

	/**
	 * @param array $params
	 * @deprecated Used for backward compatibility with v3.0 only.
	 */
	private function _upgradeSimplifiedFormat($params)
	{
		$commandArray = array();
		if (isset($params['source'])
			&& isset($params['source']['x']) && isset($params['source']['y'])
			&& isset($params['source']['width']) && isset($params['source']['height'])
		) {
			$commandArray['crop'] = array(
				'x' => $params['source']['x'],
				'y' => $params['source']['y'],
				'width' => $params['source']['width'],
				'height' => $params['source']['height'],
			);
		}

		$commandArray['thumbnail'] = array(
			'width' => $params['width'],
			'height' => $params['height'],
		);

		if(isset($params['round_corners'])){
			$commandArray['roundCorners'] = array(
				'x_radius' => $params['round_corners'],
				'y_radius' => $params['round_corners']
			);
		}

		$commandArray['backgroundColor'] = array(
			'color' => isset($params['background_color']) ? $params['background_color'] : 'black'
		);

		return $commandArray;
	}

}

?>