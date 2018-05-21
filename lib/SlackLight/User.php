<?php
/**
 * Created by PhpStorm.
 * User: r8r
 * Date: 10/03/2018
 * Time: 10:20
 */

namespace SlackLight;

class User extends Entity {
	private $userName;
	private $passwordHash;

	public function __construct(int $id, string $userName, string $passwordHash) {
		parent::__construct($id);
		$this->userName = $userName;
		$this->passwordHash = $passwordHash;
	}

	public function getUserName() {
		return $this->userName;
	}

	public function getPasswordHash() {
		return $this->passwordHash;
	}

}