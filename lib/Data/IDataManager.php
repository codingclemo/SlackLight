<?php

namespace Data;

interface IDataManager {
	public static function getCategories() : array;
	public static function getBooksByCategory(int $categoryId) : array;
	public static function getUserById(int $userId);
	public static function getUserByUserName(string $userName);
    public static function createUser(string $userName, string $passwordHash) : int;
	public static function createOrder(int $userId, array $bookIds, string $nameOnCard, string $cardNumber) : int;
    public static function getMessages(int $channelId);
    //TODO: add these functions:
    public static function createMessage(int $authorId, int $channelId, string $text);
    public static function markChannel(int $channelId, int $userId);
    public static function markMessage(int $messageId);
    /*
    public static function editMessage(int $messageId, string text);
    public static function markMessage(int $messageId, int $userId);
    public static function markChannel(int $channelId, int $userId);
    */
}
