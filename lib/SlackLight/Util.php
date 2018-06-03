<?php
/**
 * Created by PhpStorm.
 * User: r8r
 * Date: 24/03/2018
 * Time: 14:04
 */

namespace SlackLight;


class Util extends BaseObject {

	public static function escape(string $string) : string {
		return nl2br(htmlspecialchars($string));
	}

	public static function action(string $action, array $params = null) : string {
		$url = null;

		$url = 'index.php?' . Controller::ACTION . '=' . rawurlencode($action);

		if (is_array($params)) {
			foreach ($params AS $key => $value) {
				$url .= '&' . rawurlencode($key) . '=' . rawurlencode($value);
			}
		}

		$url .= '&' . Controller::PAGE . '=' . rawurlencode(
				isset($_REQUEST[Controller::PAGE]) ?
					$_REQUEST[Controller::PAGE] :
					$_SERVER['REQUEST_URI']
			);

		return $url;
	}

	public static function redirect(string $page = null) {
		if ($page == null) {
			$page = isset($_REQUEST[Controller::PAGE]) ?
				rawurldecode($_REQUEST[Controller::PAGE]) :
				$_SERVER['REQUEST_URI'];
		}
		header("Location: $page");
		exit();
	}

    public static function logError($msg1, $msg2 = '')
    {
        $date = date("D M d, Y G:i");
        $myfile = file_put_contents("DEBUG_LOG", "ERROR" ."\t" . $_SERVER['REMOTE_ADDR'] . "\t" . $date . "\t" . $msg1 . "\t" . $msg2 . "\n", FILE_APPEND | LOCK_EX);
    }

}