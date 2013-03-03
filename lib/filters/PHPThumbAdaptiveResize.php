<?php
require_once "PHPThumbResize.php";
class PHPThumbAdaptiveResize extends PHPThumbResize {

	/**
	 * Run plugin
	 */
	public function run() {
		$width  = $this->width;
		$height = $this->height;
		// make sure our arguments are valid
		if ((!is_numeric($width) || $width == 0) && (!is_numeric($height) || $height == 0)) {
			throw new InvalidArgumentException('$width and $height must be numeric and greater than zero');
		}

		if (!is_numeric($width) || $width == 0) {
			$width = ($height * $this->thumb->getImage()->getWidth()) / $this->thumb->getImage()->getHeight();
		}

		if (!is_numeric($height) || $height == 0) {
			$height = ($width * $this->thumb->getImage()->getHeight()) / $this->thumb->getImage()->getWidth();
		}

		// make sure we're not exceeding our image size if we're not supposed to
		if ($this->resizeUp === false) {
			$_maxHeight = (intval($height) > $this->thumb->getImage()->getHeight()) ? $this->thumb->getImage()->getHeight() : $height;
			$_maxWidth  = (intval($width) > $this->thumb->getImage()->getWidth()) ? $this->thumb->getImage()->getWidth() : $width;
		} else {
			$_maxHeight = intval($height);
			$_maxWidth  = intval($width);
		}

		$newDimensions = $this->calcImageSizeStrict($this->thumb->getImage()->getWidth(), $this->thumb->getImage()->getHeight(), $_maxWidth, $_maxHeight);

		// resize the image to be close to our desired dimensions
		$this->resize($newDimensions['newWidth'], $newDimensions['newHeight']);

		// reset the max dimensions...
		if ($this->resizeUp === false) {
			$_maxHeight = (intval($height) > $this->thumb->getImage()->getHeight()) ? $this->thumb->getImage()->getHeight() : $height;
			$_maxWidth  = (intval($width) > $this->thumb->getImage()->getWidth()) ? $this->thumb->getImage()->getWidth() : $width;
		} else {
			$_maxHeight = intval($height);
			$_maxWidth  = intval($width);
		}

		// create the working image
		if (function_exists('imagecreatetruecolor')) {
			$workingImage = imagecreatetruecolor($_maxWidth, $_maxHeight);
		} else {
			$workingImage = imagecreate($_maxWidth, $_maxHeight);
		}

		$workingImage = $this->preserveAlpha($workingImage);

		$cropWidth  = $_maxWidth;
		$cropHeight = $_maxHeight;
		$cropX      = 0;
		$cropY      = 0;

		// now, figure out how to crop the rest of the image...
		if ($this->thumb->getImage()->getWidth() > $_maxWidth) {
			$cropX = intval(($this->thumb->getImage()->getWidth() - $_maxWidth) / 2);
		} elseif ($this->thumb->getImage()->getHeight() > $_maxHeight) {
			$cropY = intval(($this->thumb->getImage()->getHeight() - $_maxHeight) / 2);
		}

		imagecopyresampled($workingImage, $this->thumb->getImage()->getResource(), 0, 0, $cropX, $cropY, $cropWidth, $cropHeight, $cropWidth, $cropHeight);

		// update all the variables and resources to be correct
		$this->thumb->getImage()->setResource($workingImage);

		return $this;
	}

	protected function calcImageSizeStrict($width, $height, $maxWidth, $maxHeight) {
		// first, we need to determine what the longest resize dimension is..
		$newDimensions = array();
		if ($maxWidth >= $maxHeight) {
			// and determine the longest original dimension
			if ($width > $height) {
				$newDimensions = $this->calcHeight($width, $height, $maxHeight);

				if ($newDimensions['newWidth'] < $maxWidth) {
					$newDimensions = $this->calcWidth($width, $height, $maxWidth);
				}
			} elseif ($height >= $width) {
				$newDimensions = $this->calcWidth($width, $height, $maxWidth);

				if ($newDimensions['newHeight'] < $maxHeight) {
					$newDimensions = $this->calcHeight($width, $height, $maxHeight);
				}
			}
		} elseif ($maxHeight > $maxWidth) {
			if ($width >= $height) {
				$newDimensions = $this->calcWidth($width, $height, $maxWidth);

				if ($newDimensions['newHeight'] < $maxHeight) {
					$newDimensions = $this->calcHeight($width, $height, $maxHeight);
				}
			} elseif ($height > $width) {
				$newDimensions = $this->calcHeight($width, $height, $maxHeight);

				if ($newDimensions['newWidth'] < $maxWidth) {
					$newDimensions = $this->calcWidth($width, $height, $maxWidth);
				}
			}
		}

		return $newDimensions;
	}

}