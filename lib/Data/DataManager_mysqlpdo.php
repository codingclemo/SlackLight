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

            for ($i = 1; $i <=2; $i++) {
                self::query($con,"
                INSERT INTO channelUserRef (channelId, userId, marked, lastRead)
                VALUES (?, ?, 0, 0);
                ", [
                    $i,
                    $userId
                ]);
            }

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
			SELECT channelId, marked, lastRead
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
                    $marked = $channel->marked == 0;

                    $channels[] = new Channel($channel->channelId,
                        $channelDetails->name,
                        $channelDetails->description,
                        $marked,
                        $channel->lastRead);
                }
                self::close($resTwo);
            }

        }

       // if ($channel !== null) { self::close($resTwo); }
        self::close($res);
        self::closeConnection($con);

        return $channels;
    }

    public static function getMessages(int $channelId) {
        $messages = null;
        $user = AuthenticationManager::getAuthenticatedUser();
        $channel = DataManager::getChannelByName($_REQUEST['channel']);
        $channelId = $channel->getId();

        $con = self::getConnection();

        //get all the messages that the channel has
        $res = self::query($con, "
			SELECT id, authorId, channelId, text, creationTime, edited, deleted
			FROM messages
			WHERE channelId = ?
			AND deleted = 0;
		", [$channelId]);

        // create the messages as objects
        while ($message = self::fetchObject($res)) {
            $resTwo = self::query($con, "
                SELECT marked 
                FROM userMessageRef
                WHERE ( userId = ?
                AND messageId = ? )
        ", [$user->getId(), $message->id]);
            $m = self::fetchObject($resTwo);
            // if the entry of the message is not in the userMessageRef, set marked as false by default
            if ($m == null) {
                $marked = 0;
                //TODO: should I add the entry in the userMessageRef here?
            } else {
                $marked = $m->marked;
            }
            $messages[] = new Message(  $message->id,
                                        $message->authorId,
                                        $message->channelId,
                                        $message->text,
                                        $message->creationTime,
                                        $message->edited,
                                        $message->deleted,
                                        $marked);
            self::close($resTwo);
        }



        self::close($res);
        self::closeConnection($con);

        return $messages;
    }

    public static function createMessage(int $authorId, int $channelId, string $text)
    {
        $user = AuthenticationManager::getAuthenticatedUser();
        $con = self::getConnection();

        $con->beginTransaction();

        try {
            self::query($con, "
                INSERT INTO messages (authorId, channelId, text, creationTime, edited) 
                            VALUES (?, ?, ?, NOW(), 0);
            ", [$authorId, $channelId, $text]);
            $msgId = self::lastInsertId($con);

            // not sure if even necessary to store every own created message here
            self::query($con, "
                INSERT INTO userMessageRef (userId, messageId, marked) 
                            VALUES (?, ?, 0);
            ", [$user->getId(), $msgId]);

            $con->commit();
        } catch (\Exception $e) {
            $con->rollBack();
            $msgId = NULL;
        }

	    self::closeConnection($con);
	    return $msgId;
    }

    public static function getChannelById(int $channelId)
    {
        $user = AuthenticationManager::getAuthenticatedUser();
        $con = self::getConnection();

        $res = self::query($con, "
            SELECT *
            FROM channels
            WHERE id = ?
        ", [$channelId]);
        $channel = self::fetchObject($res);

        // this is kind of a pain in the ass but necessary due to the channelUserRef
        $resTwo = self::query($con, "
            SELECT marked, lastRead
            FROM channelUserRef
            WHERE channelId = ?
            AND userId = ?
        ", [$channelId, $user->getId()]);
        $m = self::fetchObject($resTwo);

        $marked = $m->marked == 0;

        $chn = new Channel($channel->id, $channel->name, $channel->description, $marked, $m->lastRead);

        self::close($resTwo);
        self::close($res);
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
            SELECT marked, lastRead
            FROM channelUserRef
            WHERE channelId = ?
            AND userId = ?
        ", [$channel->id, $user->getId()]);
        $m = self::fetchObject($resTwo);

        $marked = $m->marked == 0;

        $chn = new Channel($channel->id, $channel->name, $channel->description, $marked, $m->lastRead);

        self::close($resTwo);
        self::close($res);
        self::closeConnection($con);
        return $chn;
    }

    public static function markChannel(int $channelId, int $userId){
        $con = self::getConnection();

        $channel = DataManager::getChannelById($channelId);
        if ($channel->isMarked())
            $newMarked = (int) 0;
        else
            $newMarked = (int) 1;
        /*
                var_dump($newMarked);
                var_dump($channelId);
                var_dump($userId);
                die();
        */

        $con->beginTransaction(); // not sure if necessary

        try {
            self::query($con, "
                    UPDATE channelUserRef 
                    SET marked = ?
                    WHERE  userId = ?
                    AND channelId = ?
                ", [$newMarked, $userId, $channelId]);
            $con->commit();
        } catch (\Exception $e) {
            $con->rollBack();
            $msgId = NULL;
        }

        self::closeConnection($con);
    }

    public static function markMessage(int $messageId){
        $user = AuthenticationManager::getAuthenticatedUser();
        $con = self::getConnection();
        // check if message exists in userMessageRef

        $con->beginTransaction();

        try {
            $res = self::query($con, "
                SELECT marked 
                FROM userMessageRef
                WHERE ( userId = ?
                AND messageId = ? )
        ", [$user->getId(), $messageId]);
            $message = self::fetchObject($res);

            if ($message == null) {
                // if message does not exist in userMessageRef create and set marked true
                self::query($con, "
                INSERT INTO userMessageRef (userId, messageId, marked) 
                VALUES (?, ?, ?)
            ", [$user->getId(), $messageId, 1]);
            } else {
                // if message exists in userMessageRef then switch marked
                $oldMarked = $message->marked;
                if ($oldMarked == 0) {
                    $newMarked = 1;
                } else {
                    $newMarked = 0;
                }

                self::query($con, "
                    UPDATE userMessageRef 
                    SET marked = ?
                    WHERE  userId = ?
                    AND messageId = ?
                ", [$newMarked, $user->getId(), $messageId]);
            }
            $con->commit();
        } catch (\Exception $e) {
            $con->rollBack();
            $msgId = NULL;
        }

        self::close($res);
        self::closeConnection($con);
    }

    public static function updateLastRead(){
        $user = AuthenticationManager::getAuthenticatedUser();
        $channelName = isset($_REQUEST['channel']) ? $_REQUEST['channel'] : null;
        $channel = self::getChannelByName($channelName);

        $con = self::getConnection();

        $preRes = self::query($con, "
                SELECT * 
                FROM messages 
                WHERE channelId = ?
                ORDER BY id DESC LIMIT 0, 1
                ", [ $channel->getId()]);
        $oldLastRead = self::fetchObject($preRes);

        $res = self::query($con, "
                UPDATE channelUserRef 
                SET lastRead = ?
                WHERE  userId = ?
                AND channelId = ?
        ", [$oldLastRead->id, $user->getId(), $channel->getId() ]);

        self::close($res);
        self::closeConnection($con);
    }

    public static function deleteMessage(int $messageId){
        $con = self::getConnection();
        self::query($con, "
                UPDATE messages 
                SET deleted = 1
                WHERE  id = ?
        ", [ $messageId ]);
        self::closeConnection($con);
    }

    public static function editMessage(int $messageId, string $text) {
        $user = AuthenticationManager::getAuthenticatedUser();
        $con = self::getConnection();

        $con->beginTransaction();

        try {
            self::query($con, "
                UPDATE messages
                SET text = ?, edited = 1
                WHERE id = ?
                AND authorId = ?
            ", [ $text, $messageId, $user->getId()]);
            $msgId = $messageId; // just for checking if it worked

            $con->commit();
        } catch (\Exception $e) {
            $con->rollBack();
            $msgId = NULL;
        }

        self::closeConnection($con);
        return $msgId;
    }

}




















