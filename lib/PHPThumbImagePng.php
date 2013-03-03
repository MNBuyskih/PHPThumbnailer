<?php
class PHPThumbImagePng extends PHPThumbImageBase {

	protected $_format = self::FORMAT_PNG;

	protected function _getResource() {
		return imagecreatefrompng($this->getSrcImagePath());
	}

	public function show() {
		header('Content-type: image/png');
		imagepng($this->getResource());

		return $this;
	}

	public function _save($fileName, $options = array()) {
		imagepng($this->getResource(), $fileName);

		return $this;
	}

}