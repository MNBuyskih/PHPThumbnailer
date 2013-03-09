<?php
/**
 * GIF image type proxy class
 *
 * @author  M.N.B. <buyskih@gmail.com>
 * @package PHPThumbler
 * @date 2013.03.09
 */
class PHPThumbImageGif extends PHPThumbImageBase {

	/**
	 * Current image format.
	 * @var PHPThumbImageBase::FORMAT_GIF
	 */
	protected $_format = self::FORMAT_GIF;

	/**
	 * Get image resource
	 * @return resource Image resource
	 */
	protected function _getResource() {
		return imagecreatefromgif($this->getSrcImagePath());
	}

	/**
	 * Show an image in browser
	 * @return PHPThumbImageGif Current instance of PHPThumbImageGif
	 */
	public function _show() {
		header('Content-type: image/gif');
		imagegif($this->getResource());

		return $this;
	}

	/**
	 * Save an image in new location
	 *
	 * @param string $fileName New file path.
	 * @param array  $options  List of options. For GIF image no options available.
	 *
	 * @return PHPThumbImageGif Current instance of PHPThumbImageGif
	 */
	public function _save($fileName, $options = array()) {
		imagegif($this->getResource(), $fileName);

		return $this;
	}

}