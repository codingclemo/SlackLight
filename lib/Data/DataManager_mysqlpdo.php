<?php

namespace Data;

use SlackLight\AuthenticationManager;
use SlackLight\Channel;
use SlackLight\Category;
use SlackLight\Book;
use SlackLight\php;
use SlackLight\User;
use SlackLight\Message;

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

    public static function createUser(string $userName, string $passwordHash) : int {

        $con = self::getConnection();

        $con->beginTransaction();

        try {

            self::query($con,"
                INSERT INTO users (
                    userName,
                    passwordHash
                ) VALUES (
                    ?, ?
                );
            
                ", [
                    $userName,
                    $passwordHash
                ]);
            $userId = self::lastInsertId($con);

            $con->commit();

        } catch (\Exception $e) {
            $con->rollBack();
            $userId = null;
        }

        self::closeConnection($con);

	    return $userId;
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


    public static function getChannelsByUserId(int $userId) {
        $channels = null;
//	    $user = null;

        $con = self::getConnection();

        //get all the channels that the user has
        $res = self::query($con, "
			SELECT channelId, marked
			FROM channelUserRef
			WHERE userId = ?;
		", [$userId]);

        //$channels = null;
        //get name and description from those channels
        while ($channel = self::fetchObject($res)) {
            if ($channel !== null) {
                $resTwo = self::query($con, "
                    SELECT name, description
                    FROM channels
                    WHERE id = ?;
                ", [$channel->channelId]);

                // create the channels as objects
                while ($channelDetails = self::fetchObject($resTwo)) {
                    $channels[] = new Channel($channel->channelId,
                        $channelDetails->name,
                        $channelDetails->description,
                        $channel->marked);
                }
                self::close($resTwo);
            }

        }

       // if ($channel !== null) { self::close($resTwo); }
        self::close($res);
        self::closeConnection($con);

        return $channels;
    }

    //TODO: Add input variable int $userId and get information from userMessageRef if msg was read or marked
    public static function getMessages(int $channelId) {
        $messages = null;

        $con = self::getConnection();

        //get all the messages that the channel has
        $res = self::query($con, "
			SELECT id, authorId, channelId, text, creationTime, edited
			FROM messages
			WHERE channelId = ?;
		", [$channelId]);

        // create the messages as objects
        while ($message = self::fetchObject($res)) {
            $messages[] = new Message($message->id,
                $message->authorId,
                $message->channelId,
                $message->text,
                $message->creationTime,
                $message->edited);
        }

        self::close($res);
        self::closeConnection($con);

        return $messages;
    }

    public static function createMessage(int $authorId, int $channelId, string $text)
    {
        $con = self::getConnection();

        $con->beginTransaction();

        try {
            self::query($con, "
                INSERT INTO messages (authorId, channelId, text, creationTime, edited) 
                            VALUES (?, ?, ?, NOW(), 0);
            ", [$authorId, $channelId, $text]);
            $msgId = self::lastInsertId($con);
            $con->commit();
        } catch (\Exception $e) {
            $con->rollBack();
            $msgId = NULL;
        }

	    self::closeConnection($con);
	    return $msgId;
    }

    public static function getMessage(int $msgId)
    {
        $con = self::getConnection();

        $res = self::query($con, "
            SELECT *
            FROM messages
            WHERE id == ?
        ", [$msgId]);

        $message = self::fetchObject($res);
        $msg = new Message($message->id,
            $message->authorId,
            $message->channelId,
            $message->text,
            $message->creationTime,
            $message->edited);


        self::closeConnection($con);
        return $msg;
    }


    public static function getChannelById(int $channelId)
    {
        $con = self::getConnection();

        $res = self::query($con, "
            SELECT *
            FROM channels
            WHERE id == ?
        ", [$channelId]);

        $channel = self::fetchObject($res);
        $chn = new Channel($channel->id, $channel->name, $channel->description);

        self::closeConnection($con);
        return $chn;
    }

    public static function getChannelByName(string $channelName)
    {
        $user = AuthenticationManager::getAuthenticatedUser();
        $con = self::getConnection();

        $res = self::query($con, "
            SELECT *
            FROM channels c
            WHERE c.name LIKE ?
        ", [$channelName]);
        $channel = self::fetchObject($res);

        // this is kind of a pain in the ass but necessary due to the channelUserRef
        $resTwo = self::query($con, "
            SELECT marked
            FROM channelUserRef
            WHERE channelId = ?
            AND userId = ?
        ", [$channel->id, $user->getId()]);
        $m = self::fetchObject($resTwo);
        $marked = $m != 0;

        $chn = new Channel($channel->id, $channel->name, $channel->description, $marked);

        self::closeConnection($con);
        return $chn;
    }
}



















