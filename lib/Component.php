<?php
class Component {

	public function __get($name) {
		$methodName = "get" . ucfirst($name);
		if (method_exists($this, $methodName)) {
			return $this->$methodName();
		}

		throw new Exception("Unknown property $name");
	}

	public function __set($name, $value) {
		$methodName = "set" . ucfirst($name);

		if (method_exists($this, $methodName)) {
			return $this->$methodName($value);
		}

		throw new Exception("Unknown property $name");
	}
}