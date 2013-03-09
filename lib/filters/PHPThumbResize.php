<?php
/**
 * Require base filter class
 */
require_once "PHPThumbFilter.php";

/**
 * Resize filter for PHPThumbler
 * @package PHPThumbler
 * @author  M.N.B. <buyskih@gmail.com>
 * @date 2013.03.09
 */
class PHPThumbResize extends PHPThumbFilter {

	/**
	 * New image width
	 * @var integer
	 */
	public $width;

	/**
	 * New image height
	 * @var integer
	 */
	public $height;

	/**
	 * Allow make image bigger
	 * @var bool
	 */
	public $resizeUp = false;

	/**
	 * Save PNG transparency
	 * @var bool
	 */
	public $preserveAlpha = true;

	/**
	 * Transparency color for PNG image
	 * @var array
	 */
	public $alphaMaskColor = array(
		255,
		255,
		255
	);

	/**
	 * Save GIF transparency
	 * @var bool
	 */
	public $preserveTransparency = true;

	/**
	 * Transparency color for GIF image
	 * @var array
	 */
	public $transparencyMaskColor = array(
		0,
		0,
		0
	);

	/**
	 * Run filter
	 * @return PHPThumbler Current instance of PHPThumbler
	 */
	public function run() {
		$maxWidth  = $this->width;
		$maxHeight = $this->height;

		return $this->resize($maxWidth, $maxHeight);
	}

	/**
	 * Resize image
	 *
	 * @param $maxWidth  integer New image width
	 * @param $maxHeight integer New image height
	 *
	 * @return PHPThumbler Current instance of PHPThumbler
	 * @throws InvalidArgumentException
	 */
	public function resize($maxWidth, $maxHeight) {
		// make sure our arguments are valid
		if (!is_numeric($maxWidth)) {
			throw new InvalidArgumentException('$_maxWidth must be numeric');
		}

		if (!is_numeric($maxHeight)) {
			throw new InvalidArgumentException('$_maxHeight must be numeric');
		}

		// make sure we're not exceeding our image size if we're not supposed to
		if ($this->resizeUp === false) {
			$_maxHeight = (intval($maxHeight) > $this->thumb->getImage()->getHeight()) ? $this->thumb->getImage()->getHeight() : $maxHeight;
			$_maxWidth  = (intval($maxWidth) > $this->thumb->getImage()->getWidth()) ? $this->thumb->getImage()->getWidth() : $maxWidth;
		} else {
			$_maxHeight = intval($maxHeight);
			$_maxWidth  = intval($maxWidth);
		}

		// get the new dimensions...
		$newDimensions = $this->calcImageSize($this->thumb->getImage()->getWidth(), $this->thumb->getImage()->getHeight(), $_maxWidth, $_maxHeight);

		// create the working image
		if (function_exists('imagecreatetruecolor')) {
			$workingImage = imagecreatetruecolor($newDimensions['newWidth'], $newDimensions['newHeight']);
		} else {
			$workingImage = imagecreate($newDimensions['newWidth'], $newDimensions['newHeight']);
		}

		$workingImage = $this->preserveAlpha($workingImage);

		// and create the newly sized image
		imagecopyresampled($workingImage, $this->thumb->getImage()->getResource(), 0, 0, 0, 0, $newDimensions['newWidth'], $newDimensions['newHeight'], $this->thumb->getImage()->getWidth(), $this->thumb->getImage()->getHeight());

		// update all the variables and resources to be correct
		$this->thumb->getImage()->setResource($workingImage);
		$imageData    = $this->thumb->getImage()->getImageData();
		$imageData[0] = imagesx($this->thumb->getImage()->getResource());
		$imageData[1] = imagesy($this->thumb->getImage()->getResource());
		$this->thumb->getImage()->setImageData($imageData);

		return $this->thumb;
	}

	/**
	 * Calculate image size
	 *
	 * @param $width     integer Current width
	 * @param $height    integer Current height
	 * @param $maxWidth  integer Maximum image width
	 * @param $maxHeight integer Maximum image height
	 *
	 * @return array Calculated image size array('newWidth', 'newHeight')
	 */
	protected function calcImageSize($width, $height, $maxWidth, $maxHeight) {
		$newSize = array(
			'newWidth'  => $width,
			'newHeight' => $height
		);

		if ($maxWidth > 0) {
			$newSize = $this->calcWidth($width, $height, $maxWidth);

			if ($maxHeight > 0 && $newSize['newHeight'] > $maxHeight) {
				$newSize = $this->calcHeight($newSize['newWidth'], $newSize['newHeight'], $maxHeight);
			}
		}

		if ($maxHeight > 0) {
			$newSize = $this->calcHeight($width, $height, $maxHeight);

			if ($maxWidth > 0 && $newSize['newWidth'] > $maxWidth) {
				$newSize = $this->calcWidth($newSize['newWidth'], $newSize['newHeight'], $maxWidth);
			}
		}

		return $newSize;
	}

	/**
	 * Calculate image width
	 *
	 * @param $width    integer Current width
	 * @param $height   integer Current height
	 * @param $maxWidth integer Maximum image width
	 *
	 * @return array Calculated image size array('newWidth', 'newHeight')
	 */
	protected function calcWidth($width, $height, $maxWidth) {
		$newWidthPercentage = (100 * $maxWidth) / $width;
		$newHeight          = ($height * $newWidthPercentage) / 100;

		return array(
			'newWidth'  => intval($maxWidth),
			'newHeight' => intval($newHeight)
		);
	}

	/**
	 * Calculate image width
	 *
	 * @param $width     integer Current width
	 * @param $height    integer Current height
	 * @param $maxHeight integer Maximum image height
	 *
	 * @return array Calculated image size array('newWidth', 'newHeight')
	 */
	protected function calcHeight($width, $height, $maxHeight) {
		$newHeightPercentage = (100 * $maxHeight) / $height;
		$newWidth            = ($width * $newHeightPercentage) / 100;

		return array(
			'newWidth'  => ceil($newWidth),
			'newHeight' => ceil($maxHeight)
		);
	}

	/**
	 * Preserve transparency for GIF and PNG
	 *
	 * @param $workingImage resource
	 *
	 * @return mixed resource
	 */
	protected function preserveAlpha($workingImage) {
		// preserve transparency in PNG
		if ($this->thumb->getFormat() == PHPThumbImageBase::FORMAT_PNG && $this->preserveAlpha) {
			imagealphablending($workingImage, false);

			$colorTransparent = imagecolorallocatealpha($workingImage, $this->alphaMaskColor[0], $this->alphaMaskColor[1], $this->alphaMaskColor[2], 0);

			imagefill($workingImage, 0, 0, $colorTransparent);
			imagesavealpha($workingImage, true);
		}

		// preserve transparency in GIFs... this is usually pretty rough tho
		if ($this->thumb->getFormat() == PHPThumbImageBase::FORMAT_GIF && $this->preserveTransparency) {
			$colorTransparent = imagecolorallocate($workingImage, $this->transparencyMaskColor[0], $this->transparencyMaskColor[1], $this->transparencyMaskColor[2]);

			imagecolortransparent($workingImage, $colorTransparent);
			imagetruecolortopalette($workingImage, true, 256);
		}

		return $workingImage;
	}
}