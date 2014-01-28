<?php

use \ReflectionFunction;

/**
 * A simple abstraction for PHP's mcrypt extension. 
 * Limited but easy-to-use OOP-style functionality.
 * This class is not entirely idiot-proof, however.
 * Defaults to AES-256-CBC w/ SHA-256 hashed key.
 * Note: Key and data padding is not necessary, PHP does this internally.
 * @author Andrew Zammit <zammit.andrew@gmail.com>
 * @license BSD 3-Clause, see included "LICENSE"
 */
class Cipher {
	
	/**
	 * One of MCRYPT_RAND, MCRYPT_DEV_RANDOM, MCRYPT_DEV_URANDOM, depending on preferences/system.
	 * @var int
	 */
	public static $RAND_SOURCE		= MCRYPT_RAND;
	
	/**
	 * The default cipher algorithm to use.
	 * @see Cipher::ciphers()
	 * @var string
	 */
	public static $DEFAULT_CIPHER	= 'rijndael-256';
	
	/**
	 * The default mode to use.
	 * @see Cipher::modes()
	 * @var string
	 */
	public static $DEFAULT_MODE		= 'cbc';
	
	/**
	 * The default hash algorithm to use.
	 * @see Cipher::algos()
	 * @var string
	 */
	public static $DEFAULT_ALGO		= 'sha256';
	
	/**
	 * @var int
	 */
	private static $KEY_SIZE_ARGS	= 0;
	
	/**
	 * @var string
	 */
	private $key;
	
	/**
	 * @var string
	 */
	private $cipher;
	
	/**
	 * @var string
	 */
	private $mode;
	
	/**
	 * @param string $key The super secret passphrase.
	 * @param string $cipher The cipher algorithm to use, one of Cipher::ciphers(), defaults to Cipher::$DEFAULT_CIPHER.
	 * @param string $mode The mode to use, one of Cipher::modes(), defaults to Cipher::$DEFAULT_MODE.
	 * @param string $algo The hash algorithm to use when hashing the key, one of Cipher::algos(), defaults to Cipher::$DEFAULT_ALGO.
	 */
	public function __construct($key,$cipher=null,$mode=null,$algo=null) {
		static::determineKeySizeArguments();
		$this->cipher	= $cipher?$cipher:static::$DEFAULT_CIPHER;
		$this->mode		= $mode?$mode:static::$DEFAULT_MODE;
		$algo			= $algo?$algo:static::$DEFAULT_ALGO;
		$this->key		= $this->truncateKeyToMax(hash($algo,$key,true));
	}
	
	/**
	 * @return string The hashed version of the supplied key.
	 */
	public function getRawKey() {
		return $this->key;
	}
	
	/**
	 * @return string The cipher being used for encryption and decryption.
	 */
	public function getCipher() {
		return $this->cipher;
	}
	
	/**
	 * @return string The mode being used for encryption and decryption.
	 */
	public function getMode() {
		return $this->mode;
	}
	
	/**
	 * Encrypt anything (string, integer, object, array) that can be serialized. 
	 * This outputs raw data, so depending on the application, the output of this 
	 * method may need to be base64 encoded.
	 * @param mixed $input The object that should be encrypted.
	 * @return string Raw/binary encrypted input (prefixed with the IV, if applicable).
	 */
	public function encrypt($input) {
		$iv = $this->ivCreate();
		$encrypted = mcrypt_encrypt($this->cipher,$this->key,serialize($input),$this->mode,$iv?$iv:null);
		return $iv.$encrypted;
	}
	
	/**
	 * Decrypt something returned by Cipher::encrypt. Attempts to return unserialized data.
	 * @param string $input Raw/binary encrypted data (prefixed with the IV, if applicable).
	 * @return mixed|NULL The original object (used with Cipher::encrypt) or null on failure.
	 */
	public function decrypt($input) {
		$ivSize = $this->ivGetSize();
		$iv = substr($input,0,$ivSize);
		$encrypted = substr($input,$ivSize);
		$decrypted = mcrypt_decrypt($this->cipher,$this->key,$encrypted,$this->mode,$iv?$iv:null);
		if ( $decrypted ) {
			return unserialize($decrypted);
		}
		return null;
	}
	
	private function ivIsModeValid() {
		return $this->mode !== MCRYPT_MODE_ECB;
	}
	
	private function ivGetSize() {
		if ( $this->ivIsModeValid() ) {
			return mcrypt_get_iv_size($this->cipher,$this->mode);
		}
		return 0;
	}
	
	private function ivCreate() {
		$ivSize = $this->ivGetSize();
		if ( $this->ivIsModeValid() ) {
			return mcrypt_create_iv($ivSize,static::$RAND_SOURCE);
		}
		return '';
	}
	
	private function truncateKeyToMax($key) {
		$keySize = 8;
		if ( static::$KEY_SIZE_ARGS === 2 ) {
			$keySize = mcrypt_get_key_size($this->cipher,$this->mode);
		} else if ( static::$KEY_SIZE_ARGS === 1 ) {
			$keySize = mcrypt_get_key_size($this->cipher);
		}
		return substr($key,0,$keySize);
	}
	
	/**
	 * @return array[string] A list of mcrypt modes available on the system.
	 */
	public static function modes() {
		return mcrypt_list_modes();
	}
	
	/**
	 * @return array[string] A list of mcrypt cipher algorithms available on the system.
	 */
	public static function ciphers() {
		return mcrypt_list_algorithms();
	}
	
	/**
	 * @return array[string] A list of hash algorithms available on the system.
	 */
	public static function algos() {
		return hash_algos();
	}
	
	/**
	 * Uses Cipher::DEFAULT_ALGO to generate a string with a given length. 
	 * There is no consideration of an algorithm's max key size. 
	 * This is really just a testing/utility method at the end of the day. 
	 * @param number $length Defaults to 16.
	 * @return string A random string confined by the given length.
	 */
	public static function generateKey($length=16) {
		return substr(hash(static::$DEFAULT_ALGO,mt_rand(),false),0,$length);
	}
	
	private static function determineKeySizeArguments() {
		if ( !static::$KEY_SIZE_ARGS ) {
			$reflFunc = new ReflectionFunction('mcrypt_get_key_size');
			static::$KEY_SIZE_ARGS = $reflFunc->getNumberOfRequiredParameters();
		}
	}
	
}