<?php

namespace Data;

use SlackLight\Category;
use SlackLight\Book;
use SlackLight\User;

include 'IDataManager.php';

/**
 * DataManager
 * Mock Version
 * 
 * 
 * @package    
 * @subpackage 
 * @author     John Doe <jd@fbi.gov>
 */
class DataManager implements IDataManager {

	private static $__connection;

	private static function getConnection() {

		if (!isset(self::$__connection)) {

			$type = 'mysql';
			$host = 'localhost';
			$name = 'clk_slacklight';
			$user = 'root';
			$pass = '';

			self::$__connection = new \PDO(
				$type . ':host=' . $host . ';dbname=' . $name . ';charset=utf8',  $user, $pass
			);
		}

		return self::$__connection;
	}

	public static function exposeConnection() {
		return self::getConnection();
	}

	private static function query($connection, $query, $parameters = array()) {

		$connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

		try {

			$statement = $connection->prepare($query);
			$i = 1;

			foreach ($parameters AS $param) {

				if (is_int($param)) {
					$statement->bindValue($i, $param, \PDO::PARAM_INT);
				}
				if (is_string($param)) {
					$statement->bindValue($i, $param, \PDO::PARAM_STR);
				}

				$i++;
			}

			$result = $statement->execute();


		}
		catch (\Exception $e) {
			die($e->getMessage());
		}

		return $statement;

	}

	private static function lastInsertId($connection) {
		return $connection->lastInsertId();
	}

	private static function fetchObject($cursor) {
		return $cursor->fetchObject();
	}

	private static function close($cursor) {
		$cursor->closeCursor();
	}

	private static function closeConnection($connection) {
		self::$__connection = null;
	}

	public static function getCategories() : array {
		$categories = [];

		$con = self::getConnection();
		$res = self::query($con, "
			SELECT id, name
			FROM categories;
		");

		while ($cat = self::fetchObject($res)) {
			$categories[] = new Category($cat->id, $cat->name);
		}

		self::close($res);
		self::closeConnection($con);

		return $categories;
	}


	public static function getBooksByCategory(int $categoryId) : array {
    	$books = [];

		$con = self::getConnection();
		$res = self::query($con, "
			SELECT id, categoryId, title, author, price
			FROM books
			WHERE categoryId = ?;
		", [$categoryId]);

		while ($book = self::fetchObject($res)) {
			$books[] = new Book($book->id, $book->categoryId, $book->title, $book->author, $book->price);
		}

		self::close($res);
		self::closeConnection($con);

    	return $books;
	}


	public static function getUserById(int $userId) {
		$user = null;

		$con = self::getConnection();
		$res = self::query($con, "
			SELECT id, userName, passwordHash
			FROM users
			WHERE id = ?;
		", [$userId]);

		if ($u = self::fetchObject($res)) {
			$user = new User($u->id, $u->userName, $u->passwordHash);
		}

		self::close($res);
		self::closeConnection($con);

		return $user;
	}

	public static function getUserByUserName(string $userName) {
		$user = null;

		$con = self::getConnection();
		$res = self::query($con, "
			SELECT id, userName, passwordHash
			FROM users
			WHERE userName = ?;
		", [$userName]);

		if ($u = self::fetchObject($res)) {
			$user = new User($u->id, $u->userName, $u->passwordHash);
		}

		self::close($res);
		self::closeConnection($con);

		return $user;
	}

	public static function createOrder(int $userId, array $bookIds, string $nameOnCard, string $cardNumber) : int {

		$con = self::getConnection();

		$con->beginTransaction();

		try {
			self::query($con, "
				INSERT INTO orders (
					userId,
					creditCardNumber,
					creditCardHolder
				) VALUES (
					?, ?, ?
				);
			", [
				$userId,
				$cardNumber,
				$nameOnCard
			]);

			$orderId = self::lastInsertId($con);

			foreach ($bookIds AS $bookId) {
				self::query($con, "
					INSERT INTO orderedbooks
					(
						orderId,
						bookId
					) VALUES (
						?, ?
					);
				", [$orderId, $bookId]);
			}

			$con->commit();
		}
		catch (\Exception $e) {
			$con->rollBack();
			$orderId = null;
		}

		self::closeConnection($con);
		return $orderId;
    }

}



















