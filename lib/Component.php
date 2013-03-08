<?php

/**
 * Base class
 * @package PHPThumbler
 * @author  M.N.B. <buyskih@gmail.com>
 * @abstract
 */
abstract class Component {

	/**
	 * Call getter method
	 *
	 * @param $name string Method name
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public function __get($name) {
		$methodName = "get" . ucfirst($name);
		if (method_exists($this, $methodName)) {
			return $this->$methodName();
		}

		throw new Exception("Unknown property $name");
	}

	/**
	 * Call setter method
	 *
	 * @param $name  string Method name
	 * @param $value mixed  Value to set
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public function __set($name, $value) {
		$methodName = "set" . ucfirst($name);

		if (method_exists($this, $methodName)) {
			return $this->$methodName($value);
		}

		throw new Exception("Unknown property $name");
	}
}