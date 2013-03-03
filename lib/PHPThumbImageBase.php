<?php
abstract class PHPThumbImageBase extends CComponent {

	/**
	 * GIF format
	 */
	const FORMAT_GIF = 101;

	/**
	 * JPG format
	 */
	const FORMAT_JPG = 102;

	/**
	 * PNG format
	 */
	const FORMAT_PNG = 103;

	/**
	 * Full path to source image
	 * @var string
	 */
	private $_srcImagePath;

	/**
	 * Source image resource
	 * @var resource
	 */
	private $_resource;

	/**
	 * Image format
	 * @var PHPThumbImageBase::FORMAT_GIF|PHPThumbImageBase::FORMAT_JPG|PHPThumbImageBase::FORMAT_PNG
	 */
	protected $_format;

	/**
	 * Result of getimagesize() function
	 * @var array
	 */
	private $_imageData;

	/**
	 * Image width
	 * @var integer
	 */
	private $_width;

	/**
	 * Image height
	 * @var integer
	 */
	private $_height;

	/**
	 * Text string with the correct <b>height="yyy" width="xxx"</b> string that can be used directly in an IMG tag.
	 * @var string
	 */
	private $_dimensions;

	/**
	 * Current image MIME-type
	 * @var string
	 */
	private $_mime;

	/**
	 * 3 for RGB pictures and 4 for CMYK pictures
	 * @var 3|4
	 */
	private $_channels;

	/**
	 * Number of bits for each color.
	 * @var integer
	 */
	private $_bits;

	/**
	 * @param $image string|resource Full path to source image or image resource. If image resource passed need second param - $data.
	 * @param $data  array           Result of getimagesize() function passed to original image
	 */
	function __construct($image = null, $data = array()) {
		if (is_resource($image)) {
			$this->setResource($image);
			$this->setImageData($data);
		} else {
			if (!empty($image)) {
				$this->setSrcImagePath($image);
			}
		}
	}

	/**
	 * Set source file path
	 *
	 * @param $srcImagePath string Full path to source image
	 *
	 * @throws CException Throw exception if image file not found
	 */
	public function setSrcImagePath($srcImagePath) {
		$this->_srcImagePath = $srcImagePath;
	}

	/**
	 * Return source file path
	 * @return string Source file path
	 */
	public function getSrcImagePath() {
		return $this->_srcImagePath;
	}

	public static function create($srcImagePath) {
		try {
			$imageData = getimagesize($srcImagePath);
		} catch (CException $e) {
			die($e->getMessage());
		}

		$mime = $imageData['mime'];
		switch ($mime) {
			case 'image/jpeg':
				require_once 'PHPThumbImageJpg.php';

				return new PHPThumbImageJpg($srcImagePath);
				break;

			case 'image/gif':
				require_once 'PHPThumbImageGif.php';

				return new PHPThumbImageGif($srcImagePath);
				break;

			case 'image/png':
				require_once 'PHPThumbImagePng.php';

				return new PHPThumbImagePng($srcImagePath);
				break;

			default:
				throw new CException('Unsupported image type');
				break;
		}
	}

	public function copy($format = null) {
		if (!$format) {
			$format = $this->getFormat();
		}
		switch ($format) {
			case self::FORMAT_GIF:
				require_once "PHPThumbImageGif.php";

				return new PHPThumbImageGif($this->getResource(), $this->getImageData());
				break;

			case self::FORMAT_JPG:
				require_once "PHPThumbImageJpg.php";

				return new PHPThumbImageJpg($this->getResource(), $this->getImageData());
				break;

			case self::FORMAT_PNG:
				require_once "PHPThumbImagePng.php";

				return new PHPThumbImagePng($this->getResource(), $this->getImageData());
				break;

			default:
				throw new CException('Unsupported image type');
				break;
		}
	}

	public function getFormat() {
		return $this->_format;
	}

	public function setResource($resource) {
		$this->_resource = $resource;

		return $this;
	}

	public function getResource() {
		if ($this->_resource === null) {
			$this->_resource = $this->_getResource();
		}

		return $this->_resource;
	}

	public function getImageData() {
		if ($this->_imageData === null) {
			$data = getimagesize($this->getSrcImagePath());
			if (!$data) {
				throw new CException('Can`t read image data');
			}

			$this->setImageData($data);
		}

		return $this->_imageData;
	}

	public function setImageData($data) {
		$this->_width      = $data[0];
		$this->_height     = $data[1];
		$this->_dimensions = $data[3];
		$this->_mime       = $data['mime'];
		$this->_channels   = $data['channels'];
		$this->_bits       = $data['bits'];

		$this->_imageData = $data;

		return $this;
	}

	/**
	 * Save current image in new location. If param $format passed return new instance of PHPThumbImageBaseXXX (XXX - new format).
	 *
	 * @param string $fileName New file name.
	 * @param null|PHPThumbImageBase::FORMAT_GIF|PHPThumbImageBase::FORMAT_JPG|PHPThumbImageBase::FORMAT_PNG $format New image format.
	 * @param array  $options  Options list. For example: <pre>array (
	 *     'quality' => 100 // JPEG-quality (default 80)
	 * );</pre>
	 *
	 * @return PHPThumbImageBase Instance of new image.
	 * @throws CException Throw exception if destination directory is not writable
	 */
	public function save($fileName, $format = null, $options = array()) {
		if (!is_writeable(dirname($fileName))) {
			throw new CException('File not writable: ' . $fileName);
		}

		$newFile = $this->copy($format);
		$newFile->setSrcImagePath($fileName);
		$newFile->_save($fileName, $options);
		$newFile->setResource(null);

		return $newFile;
	}

	public function remove() {
		unlink($this->getSrcImagePath());
	}

	public function getHeight() {
		if (!$this->_imageData) {
			$this->getImageData();
		}

		return $this->_height;
	}

	public function getWidth() {
		if (!$this->_imageData) {
			$this->getImageData();
		}

		return $this->_width;
	}

	protected abstract function _getResource();

	public abstract function _save($fileName, $options = array());

	public abstract function show();
}