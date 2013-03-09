<?php
/**
 * PNG image type proxy class
 *
 * @author  M.N.B. <buyskih@gmail.com>
 * @package PHPThumbler
 * @date 2013.03.09
 */
class PHPThumbImagePng extends PHPThumbImageBase {

	/**
	 * Current image format.
	 * @var PHPThumbImageBase::FORMAT_PNG
	 */
	protected $_format = self::FORMAT_PNG;

	/**
	 * Get image resource
	 * @return resource Image resource
	 */
	protected function _getResource() {
		return imagecreatefrompng($this->getSrcImagePath());
	}

	/**
	 * Show an image in browser
	 * @return PHPThumbImageGif Current instance of PHPThumbImagePng
	 */
	protected function _show() {
		header('Content-type: image/png');
		imagepng($this->getResource());

		return $this;
	}

	/**
	 * Save an image in new location
	 *
	 * @param string $fileName New file path.
	 * @param array  $options  List of options. For PNG image no options available.
	 *
	 * @return PHPThumbImagePng Current instance of PHPThumbImagePng
	 */
	protected function _save($fileName, $options = array()) {
		imagepng($this->getResource(), $fileName);

		return $this;
	}

}