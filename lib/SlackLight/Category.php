<?php
/**
 * Created by PhpStorm.
 * User: r8r
 * Date: 10/03/2018
 * Time: 10:20
 */

namespace SlackLight;

class Category extends Entity {
	private $name;

	public function getName() {
		return $this->name;
	}

	public function __construct($id, $name) {
		parent::__construct($id);
		$this->name = $name;
	}
}