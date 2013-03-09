<?php

/**
 * Path to  current file
 * @var string
 */
defined('PHPTHUMB_PATH') or define('PHPTHUMB_PATH', dirname(__FILE__));

/**
 * Path to filters directory
 */
defined('PHPTHUMB_FILTERS_PATH') or define('PHPTHUMB_FILTERS_PATH', PHPTHUMB_PATH . '/filters/');

/**
 * Require base class
 */
require "Component.php";

/**
 * Class for work with image
 *
 * @property PHPThumbImageBase $image    Source PHPThumbImage object (before manipulations)
 * @property integer           $width    Image width
 * @property integer           $height   Image height
 * @property resource          $resource Image resource
 * @property PHPThumbImageBase:FORMAT_GIF|PHPThumbImageBase:FORMAT_JPG|PHPThumbImageBase:FORMAT_PNG Image Format
 *
 * @author  M.N.B. <buyskih@gmail.com>
 * @package PHPThumbler
 */
class PHPThumbler extends Component {

	/**
	 * Source PHPThumbImage object (before manipulations)
	 * @var PHPThumbImageBase
	 */
	private $_image;

	/**
	 * @return object|PHPThumbImageBase|PHPThumbImageGif|PHPThumbImageJpg|PHPThumbImagePng
	 */
	public function getImage() {
		return $this->_image;
	}

	/**
	 * @param $srcImagePath string|PHPThumbImageBase Full path to image file or instance of PHPThumbImageBase
	 */
	public function __construct($srcImagePath) {
		if (is_object($srcImagePath) && $srcImagePath instanceof PHPThumbImageBase) {
			$this->setImage($srcImagePath);
		} else {
			require_once 'PHPThumbImageBase.php';
			$this->_image = PHPThumbImageBase::create($srcImagePath);
		}
	}

	/**
	 * Set current image
	 *
	 * @param PHPThumbImageBase $image Instance of PHPThumbImageBase
	 *
	 * @return PHPThumbler Current instance of PHPThumbler
	 */
	public function setImage(PHPThumbImageBase $image) {
		$this->image = $image;

		return $this;
	}

	/**
	 * Show an image
	 * @return PHPThumbler Instance of current PHPThumbler
	 * @throws Exception
	 */
	public function show() {
		if (headers_sent() && php_sapi_name() != 'cli') {
			throw new Exception('Cannot show image, headers have already been sent');
		}

		$this->getImage()->show();

		return $this;
	}

	public function remove() {
		$this->getImage()->remove();
	}

	/**
	 * Save current image in new location. If $format passed - return new instance of PHPThumb.
	 *
	 * @param string $fileName New file name.
	 * @param null|PHPThumbImageBase::FORMAT_GIF|PHPThumbImageBase::FORMAT_JPG|PHPThumbImageBase::FORMAT_PNG $format New image format
	 * @param array  $options  Options list. For example: <pre>array (
	 *     'quality' => 100 // JPEG-quality (default 80)
	 * );</pre>
	 *
	 * @return PHPThumbler
	 */
	public function save($fileName, $format = null, $options = array()) {
		return new self($this->getImage()->save($fileName, $format, $options));
	}

	/**
	 * Image format
	 * @return PHPThumbImageBase::FORMAT_GIF|PHPThumbImageBase::FORMAT_JPG|PHPThumbImageBase::FORMAT_PNG
	 */
	public function getFormat() {
		return $this->getImage()->getFormat();
	}

	/**
	 * Image width
	 * @return integer
	 */
	public function getWidth() {
		return $this->getImage()->getWidth();
	}

	/**
	 * Image height
	 * @return integer
	 */
	public function getHeight() {
		return $this->getImage()->getHeight();
	}

	/**
	 * Get image resource
	 * @return resource Image resource
	 */
	public function getResource() {
		return $this->getImage()->getResource();
	}

	/**
	 * Apply filter to image
	 *
	 * @param PHPThumbFilter|string $plugin  Plugin object or plugin name
	 * @param array                 $options List of plugin options
	 */
	public function filter($plugin, $options = array()) {
		require_once "filters/PHPThumbFilter.php";
		$plugin = PHPThumbFilter::create($this, $plugin, $options);

		$plugin->run();
	}

}