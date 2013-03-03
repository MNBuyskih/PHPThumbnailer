<?php
defined('PHPTHUMB_PATH') or define('PHPTHUMB_PATH', dirname(__FILE__));
defined('PHPTHUMB_FILTERS_PATH') or define('PHPTHUMB_FILTERS_PATH', PHPTHUMB_PATH . '/filters/');

/**
 * @property PHPThumbImageBase $image  Source PHPThumbImage object (before manipulations)
 * @property integer           $width  Image width
 * @property integer           $height Image height
 */
class PHPThumb extends CComponent {

	/**
	 * Source PHPThumbImage object (before manipulations)
	 * @var PHPThumbImageBase
	 */
	private $_image;

	public function getImage() {
		return $this->_image;
	}

	/**
	 * @param $srcImagePath string|PHPThumbImageBase Full path to image file or instance of PHPThumbImageBase
	 */
	public function __construct($srcImagePath) {
		if (is_object($srcImagePath) && $srcImagePath instanceof PHPThumbImageBase) {
			$this->_image = $srcImagePath;
		} else {
			require_once 'PHPThumbImageBase.php';
			$this->_image = PHPThumbImageBase::create($srcImagePath);
		}
	}

	public function show() {
		if (headers_sent() && php_sapi_name() != 'cli') {
			throw new CException('Cannot show image, headers have already been sent');
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
	 * @return PHPThumb
	 */
	public function save($fileName, $format = null, $options = array()) {
		return new self($this->getImage()->save($fileName, $format, $options));
	}

	public function getFormat() {
		return $this->getImage()->getFormat();
	}

	public function getWidth() {
		return $this->getImage()->getWidth();
	}

	public function getHeight() {
		return $this->getImage()->getHeight();
	}

	/**
	 * @param PHPThumbFilter|string $plugin
	 * @param array                 $options
	 */
	public function filter($plugin, $options = array()) {
		require_once "filters/PHPThumbFilter.php";
		$plugin = PHPThumbFilter::create($this, $plugin, $options);

		$plugin->run();
	}

}