<?php
/**
 * Require base class
 */
require_once PHPTHUMB_PATH . "/Component.php";

/**
 * Base class for filters
 * @package PHPThumbler
 * @author  M.N.B. <buyskih@gmail.com>
 * @date 2013.03.09
 * @abstract
 */
abstract class PHPThumbFilter extends Component {

	/**
	 * Working instance of PHPThumb
	 * @var PHPThumb
	 */
	public $thumb;

	/**
	 * Run plugin
	 */
	public abstract function run();

	/**
	 * Constructor
	 *
	 * @param PHPThumbler $thumb       Working instance of PHPThumbler
	 */
	public function __construct(PHPThumbler $thumb) {
		$this->thumb = $thumb;
	}

	/**
	 * Create filter object and return it.
	 *
	 * @param PHPThumbler           $thumb   Instance of PHPThumbler.
	 * @param PHPThumbFilter|string $plugin  Plugin object or plugin name.
	 * @param array                 $options List of filter options.
	 *
	 * @return PHPThumbFilter New instance of filter object
	 * @throws Exception
	 */
	public static function create(PHPThumbler $thumb, $plugin, $options = array()) {
		if (!$plugin instanceof self) {
			$className = "PHPThumb{$plugin}";
			$file      = PHPTHUMB_FILTERS_PATH . $className . ".php";
			if (!file_exists($file)) {
				throw new Exception("Filter {$plugin} not found.");
			}

			require_once $file;
			if (!class_exists($className)) {
				throw new Exception("Plugin {$plugin} not found.");
			}

			$plugin = new $className($thumb);
		}

		foreach ($options as $option => $value) {
			$plugin->$option = $value;
		}

		return $plugin;
	}
}