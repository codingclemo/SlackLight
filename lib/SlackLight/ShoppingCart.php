<?php
/**
 * Created by PhpStorm.
 * User: r8r
 * Date: 24/03/2018
 * Time: 14:12
 */

namespace SlackLight;


class ShoppingCart extends BaseObject {

	public static function add(int $bookId) {
		$cart = self::getCart();
		$cart[$bookId] = $bookId;
		self::storeCart($cart);
	}

	public static function remove(int $bookId) {
		$cart = self::getCart();
		unset($cart[$bookId]);
		self::storeCart($cart);
	}

	public static function clear() {
		self::storeCart(array());
	}

	public static function size() : int {
		return sizeof(self::getCart());
	}

	public static function contains(int $bookId) : bool {
		$cart = self::getCart();
		return array_key_exists($bookId, $cart);
	}

	public static function getAll() : array {
		return self::getCart();
	}

	private static function getCart() : array {
		return isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
	}

	private static function storeCart(array $cart) {
		$_SESSION['cart'] = $cart;
	}

}