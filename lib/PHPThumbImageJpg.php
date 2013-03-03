<?php
class PHPThumbImageJpg extends PHPThumbImageBase {

	protected $_format = self::FORMAT_JPG;

	protected function _getResource() {
		return imagecreatefromjpeg($this->getSrcImagePath());
	}

	public function show() {
		header('Content-type: image/jpeg');
		imagejpeg($this->getResource());

		return $this;
	}

	public function _save($fileName, $options = array()) {
		$quality = !empty($options['quality']) ? $options['quality'] : 80;
		imagejpeg($this->getResource(), $fileName, $quality);
	}

}