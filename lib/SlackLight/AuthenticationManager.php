<?php
/**
 * Created by PhpStorm.
 * User: r8r
 * Date: 24/03/2018
 * Time: 15:54
 */

namespace SlackLight;


use Data\DataManager;

class AuthenticationManager extends BaseObject {

	public static function authenticate(string $userName, string $password) : bool {
		$user = DataManager::getUserByUserName($userName);

		if ($user != null &&
		    $user->getPasswordHash() == hash('sha1', $userName . '|' . $password)
		) {
			$_SESSION['user'] = $user->getId();
			return true;
		}
		self::signOut();
		return false;
	}

	public static function signOut() {
		unset($_SESSION['user']);
	}

	public static function isAuthenticated() : bool {
		return isset($_SESSION['user']);
	}

	public static function getAuthenticatedUser() {
		return self::isAuthenticated() ? DataManager::getUserById($_SESSION['user']) : null;
	}

    public static function registerUser(string $userName, string $password) : bool {
        if (DataManager::getUserByUserName($userName)) {
            self::signOut();
            return false;
        } else {
        // create a new user in the db and pass on its generated id to variable $user

           $userId =  DataManager::createUser(
               $userName,
               hash('sha1', $userName . '|' . $password)
           );


        $user = DataManager::getUserById($userId);

        $_SESSION['user'] = $user->getId();
        return true;
        }
    }
}