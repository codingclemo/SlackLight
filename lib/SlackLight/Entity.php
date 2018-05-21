<?php
/**
 * Created by PhpStorm.
 * User: r8r
 * Date: 10/03/2018
 * Time: 10:25
 */

namespace SlackLight;

interface IData {
	public function getId();
}

class Entity extends BaseObject implements IData {

	private $id;

	public function getId() {
		return $this->id;
	}

	public function __construct($id) {
		$this->id = $id;
	}
}