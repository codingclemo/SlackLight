<?php
/**
 * Created by PhpStorm.
 * User: r8r
 * Date: 24/03/2018
 * Time: 14:18
 */

namespace SlackLight;

class SessionContext extends BaseObject {

	private static $exists;

	public static function create() : bool {
		if (!self::$exists) {
			self::$exists = session_start();
		}
		return self::$exists;
	}
}
