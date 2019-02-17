<?php

namespace guestbook\app\Users;


use Firebase\JWT\JWT;
use guestbook\engine\Core\ActorModel;
use guestbook\engine\Core\Model;


class UsersModel extends Model implements ActorModel

{

	// TODO: Registry pattern for configs
	private const SALT_LENGTH = 10;

	//generated before
	private const SECRET_PASSWORD_WORD = 'k1xm9F31aDpqHVN3hey';

	//generated before
	private const SECRET_JWT_WORD = 'vUFJ2RuY75gnE9cI';

	//algo for token
	private const TOKEN_ALGORITHM = 'HS512';

	private const TOKEN_ISS = BASE_DOMAIN;

	private const TOKENS_FOR_AUTH = ['JWTtoken', 'CSRFtoken'];


	private $actor;

	private $actor_initilized = FALSE;


	/**
	 * @param UsersRecord $user
	 * @param string $password
	 * @return bool
	 */
	public function checkPasswords(UsersRecord $user, string $password) : bool
	{

		$user_password = $user->getPassword();

		$salt = $user->getSalt();

		return ($this->generatePassword($password, $salt)  === $user_password);

	}


	/**
	 * @param string $password
	 * @param string $salt
	 * @param string $secret_word
	 * @return string
	 */
	public function generatePassword(string $password, string $salt, string $secret_word = self::SECRET_PASSWORD_WORD) : string
	{

		return hash('sha512', $password . $secret_word . $salt);

	}


	/**
	 * @return UsersRecord|null
	 */
	public function getAuthorizedActor() : ?UsersRecord
	{

		if (is_null($this->actor) && !$this->actor_initilized) {

			$jwt = $this->getValidDataJWT();

			if (!empty($jwt)) {

				/**
				 * @var UsersRecord $user
				 */
				$user = $this->getRecordByUnique($jwt['user_id']);

				if(!empty($user) && ($user->getName() == $jwt['user_name'])) {

					$this->actor = $user;

				} else {

					$this->unsetAuthTokens();

				}
			}

			$this->actor_initilized = TRUE;

		}

		return $this->actor;

	}


	/**
	 * @param UsersRecord $user
	 * @return string
	 */
	protected function generateToken(UsersRecord $user) : string
	{

		$token_id = base64_encode(random_bytes(32));

		$created_time = time();

		$expire_time = $created_time + 24*3600;

		$data = [
			'iat'  => $created_time,
			'jti'  => $token_id,
			'iss'  => self::TOKEN_ISS,
			'exp'  => $expire_time,
			'data' => [
				'user_id'   => $user->getId(),
				'user_name' => $user->getName()
			]
		];


		return JWT::encode($data, self::SECRET_JWT_WORD, self::TOKEN_ALGORITHM);

	}


	/**
	 * @return array
	 */
	private function getValidDataJWT() : array
	{

		$jwt = $this->getJWT();

		if (!$this->isValidDataToken($jwt)) {

			$this->unsetAuthTokens();

			$jwt = [];

		} elseif (!$this->isValidTokenCSRF()) {

			//If JWT token is valid but CSRF Token not - we do not logout -
			// just dont provide access for authorized actions
			$jwt = [];

		} else {

			$jwt = $jwt['data'];
		}

		return $jwt;

	}


	/**
	 * @return array
	 */
	private function getJWT() : array
	{

		$jwt = [];

		if (isset($_COOKIE['JWTtoken'])) {

			try {

				$jwt_decoded = JWT::decode($_COOKIE['JWTtoken'], self::SECRET_JWT_WORD, [self::TOKEN_ALGORITHM]);

				$jwt = json_decode(json_encode($jwt_decoded), true);

			} catch (\Exception $e) {

				//if decoding with error we need to logout
				$this->unsetAuthTokens();

				// TODO: set notice about decode fail and create Error Proccessor
			}

		}

		return $jwt;

	}


	/**
	 * @return bool
	 */
	private function isValidTokenCSRF() : bool
	{

		return !empty($_SERVER['HTTP_CSRF_TOKEN'])
		&& !empty($_COOKIE['CSRFtoken'])
		&& ($_COOKIE['CSRFtoken'] == $_SERVER['HTTP_CSRF_TOKEN']);

	}


	/**
	 * Check data of decoded JWT
	 * @param array $decoded_token
	 * @return bool
	 */
	private function isValidDataToken(array $decoded_token) : bool
	{

		return !empty($decoded_token['iss'])
			&& ($decoded_token['iss'] == self::TOKEN_ISS)
			&& !empty($decoded_token['data'])
			&& !empty($decoded_token['data']['user_id'])
			&& !empty($decoded_token['data']['user_name']);

	}


	/**
	 * @param UsersRecord $user
	 */
	public function setTokens(UsersRecord $user) : void
	{

		$this->setTokenCSRF();

		$this->setJWT($user);

	}


	/**
	 * unset token all  tokens for logout
	 */
	public function unsetAuthTokens() : void
	{

		foreach (self::TOKENS_FOR_AUTH as $name)
		{
			$this->unsetToken($name);
		}

	}


	/**
	 * Set cookie for CSRF
	 */
	private function setTokenCSRF() : void
	{
		$CSRFtoken = base64_encode(random_bytes(32));

		setcookie("CSRFtoken", $CSRFtoken, time() + 24*3600, '/' , BASE_DOMAIN);

	}


	/**
	 * @param UsersRecord $user
	 */
	private function setJWT(UsersRecord $user) :void
	{

		$token = $this->generateToken($user);

		//Should turn on SECURE FLAG on TRUE in production when it is set https
		setcookie('JWTtoken', $token, time() + 24*3600, '/', BASE_DOMAIN, FALSE, TRUE);

	}


	/**
	 * Unset cookie with Token
	 */
	private function unsetToken(string $name) : void
	{

		if (isset($_COOKIE[$name])) {

			unset($_COOKIE[$name]);

			setcookie($name, '', time() - 3600, '/', BASE_DOMAIN);

		}

	}

	/**
	 * @param int $length
	 * @return string
	 */
	public function generateRand(int $length = self::SALT_LENGTH) : string
	{

		$chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

		$size = strlen($chars) - 1;

		$rand = '';

		for ($i = 0; $i < $length; ++$i) {

			$rand .= $chars[random_int(0, $size)];

		}

		return $rand;

	}


}