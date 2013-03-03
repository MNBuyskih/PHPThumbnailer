<?php
class PHPThumbImageGif extends PHPThumbImageBase {

	protected $_format = self::FORMAT_GIF;

	protected function _getResource() {
		return imagecreatefromgif($this->getSrcImagePath());
	}

	public function show() {
		header('Content-type: image/gif');
		imagegif($this->getResource());

		return $this;
	}

	public function _save($fileName, $options = array()) {
		imagegif($this->getResource(), $fileName);

		return $this;
	}

}