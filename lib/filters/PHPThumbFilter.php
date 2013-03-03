<?php

abstract class PHPThumbFilter extends CComponent {

	/**
	 * Working instance of PHPThumb
	 * @var PHPThumb
	 */
	public $thumb;

	/**
	 * Run plugin
	 *
	 */
	public abstract function run();

	/**
	 * Constructor
	 *
	 * @param PHPThumb $thumb       Working instance of PHPThumb
	 */
	public function __construct(PHPThumb $thumb) {
		$this->thumb = $thumb;
	}

	public static function create(PHPThumb $thumb, $plugin, $options = array()) {
		if (!$plugin instanceof self) {
			$className = "PHPThumb{$plugin}";
			$file      = PHPTHUMB_FILTERS_PATH . $className . ".php";
			if (!file_exists($file)) {
				throw new CException("Filter {$plugin} not found.");
			}

			require_once $file;
			if (!class_exists($className)) {
				throw new CException("Plugin {$plugin} not found.");
			}

			$plugin = new $className($thumb);
		}

		foreach ($options as $option => $value) {
			$plugin->$option = $value;
		}

		return $plugin;
	}
}