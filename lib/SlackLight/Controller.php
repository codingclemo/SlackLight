<?php

namespace SlackLight;
use Data\DataManager;

/**
 * Controller
 *
 * class handles POST requests and redirects
 * the client after processing
 * - demo of singleton pattern
 */
class Controller
	extends BaseObject {
	// static strings used in views

	const ACTION = 'action';
	const PAGE = 'page';
	const CC_NAME = 'nameOnCard';
	const CC_NUMBER = 'cardNumber';
	const ACTION_ADD = 'addToCart';
	const ACTION_REMOVE = 'removeFromCart';
	const ACTION_ORDER = 'placeOrder';
	const ACTION_LOGIN = 'login';
	const ACTION_LOGOUT = 'logout';
	const USER_NAME = 'userName';
	const USER_PASSWORD = 'password';

	private static $instance = false;

	/**
	 *
	 * @return Controller
	 */
	public static function getInstance() : Controller {

		if ( ! self::$instance) {
			self::$instance = new Controller();
		}

		return self::$instance;
	}

	private function __construct() {

	}

	/**
	 *
	 * processes POST requests and redirects client depending on selected
	 * action
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function invokePostAction(): bool {

		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			throw new \Exception('Controller can only handle POST requests.');

			return null;
		} elseif ( ! isset($_REQUEST[ self::ACTION ])) {
			throw new \Exception(self::ACTION . ' not specified.');

			return null;
		}


		// now process the assigned action
		$action = $_REQUEST[ self::ACTION ];

		switch ($action) {

			case self::ACTION_ADD :
				ShoppingCart::add((int) $_REQUEST['bookId']);
				Util::redirect();
				break;

			case self::ACTION_REMOVE :
				ShoppingCart::remove((int) $_REQUEST['bookId']);
				Util::redirect();
				break;

			case self::ACTION_ORDER :
				$user = AuthenticationManager::getAuthenticatedUser();

				if ($user == null) {
					$this->forwardRequest(['Not logged in.']);
				}

				if (!$this->processCheckout($_POST[self::CC_NAME], $_POST[self::CC_NUMBER])) {
					$this->forwardRequest(['Checkout failed.']);
				}

				break;

			case self::ACTION_LOGIN :
				if (!AuthenticationManager::authenticate($_REQUEST[self::USER_NAME], $_REQUEST[self::USER_PASSWORD])) {
					self::forwardRequest(['Invalid user credentials.']);
				}
				Util::redirect();
				break;

			case self::ACTION_LOGOUT :
				AuthenticationManager::signOut();
				Util::redirect();
				break;

			default :
				throw new \Exception('Unknown controller action: ' . $action);
				break;
		}
	}


	protected function processCheckout(string $nameOnCard = null, string $cardNumber) : bool {

		$errors = [];

		$nameOnCard = trim($nameOnCard);
		if ($nameOnCard == null || strlen($nameOnCard) == 0) {
			$errors[] = "Invalid name on card.";
		}
		if ($cardNumber == null || strlen($cardNumber) != 16 || !ctype_digit($cardNumber)) {
			$errors[] = "Invalid card number. Card number must be sixteen digits.";
		}

		if (sizeof($errors) > 0) {
			$this->forwardRequest($errors);
			return false;
		}

		if (ShoppingCart::size() == 0) {
			$this->forwardRequest(['Shopping cart is empty']);
			return false;
		}

		$user = AuthenticationManager::getAuthenticatedUser();
		$orderId = DataManager::createOrder($user->getId(), ShoppingCart::getAll(), $nameOnCard, $cardNumber);

		if (!$orderId) {
			$this->forwardRequest(['Could not create order.']);
			return false;
		}

		ShoppingCart::clear();
		Util::redirect('index.php?view=success&orderId=' . $orderId);
		return true;

	}


	protected function forwardRequest(array $errors = null, $target = null) {
		if ($target == null) {
			if (isset($_REQUEST[self::PAGE])) {
				$target = $_REQUEST[self::PAGE];
			}
			else {
				$target = $_SERVER['REQUEST_URI'];
			}
		}
		if (count($errors) > 0) {
			$target .= '&errors=' . urlencode(serialize($errors));
			header('Location:' . $target);
			exit();
		}
	}

}