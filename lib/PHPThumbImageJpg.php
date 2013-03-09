<?php

/**
 * GIF image type proxy class
 *
 * @author  M.N.B. <buyskih@gmail.com>
 * @package PHPThumbler
 * @date 2013.03.09
 */
class PHPThumbImageJpg extends PHPThumbImageBase {

	/**
	 * Current image format.
	 * @var PHPThumbImageBase::FORMAT_JPG
	 */
	protected $_format = self::FORMAT_JPG;

	/**
	 * Get image resource
	 * @return resource Image resource
	 */
	protected function _getResource() {
		return imagecreatefromjpeg($this->getSrcImagePath());
	}

	/**
	 * Show an image in browser
	 * @return PHPThumbImageGif Current instance of PHPThumbImageGif
	 */
	protected function _show() {
		header('Content-type: image/jpeg');
		imagejpeg($this->getResource());

		return $this;
	}

	/**
	 * Save an image in new location~
	 *
	 * @param string $fileName New file path.
	 * @param array  $options  List of options. For JPG image format available next options:
	 * <ul>
	 * <li> quality - JPG Quality. Default 80.
	 * </ul>
	 *
	 * @return PHPThumbImageJpg Current instance of PHPThumbImageGif
	 */
	protected function _save($fileName, $options = array()) {
		$quality = !empty($options['quality']) ? $options['quality'] : 80;
		imagejpeg($this->getResource(), $fileName, $quality);

		return $this;
	}

}