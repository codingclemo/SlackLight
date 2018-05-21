<?php
/**
 * Created by PhpStorm.
 * User: r8r
 * Date: 10/03/2018
 * Time: 11:00
 */

namespace SlackLight;


class BaseObject {

	public function __call($name, $arguments) {
		throw new \Exception('method ' . $name . ' is not declared');
	}

	public function __set($name, $value) {
		throw new \Exception('attribute ' . $name . ' is not declared');
	}

	public function __get($name) {
		throw new \Exception('attribute ' . $name . ' is not declared');
	}

	public static function __callStatic($name, $arguments) {
		throw new \Exception('static method ' . $name . ' is not declared');
	}

}