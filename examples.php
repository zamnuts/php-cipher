<?php

require_once 'Cipher.php';

use \Cipher;

/*
 * specify what cipher, mode and algo to use (pass null for default)
 */
$cbcBlowfish = new Cipher('secret one @!#$','blowfish','cbc','sha256');
$cbcBlowfishEncrypted = $cbcBlowfish->encrypt('Top Secret Stuff');
$cbcBlowfishDecrypted = $cbcBlowfish->decrypt($cbcBlowfishEncrypted);

/*
 * use all defaults and figure out what they are
 */
$defaultStaticCipher = Cipher::$DEFAULT_CIPHER;
$defaultStaticMode = Cipher::$DEFAULT_MODE;
$defaultStaticAlgo = Cipher::$DEFAULT_ALGO;
$defaults = new Cipher('secret two (*&( lawl');
$defaultCipher = $defaults->getCipher();
$defaultMode = $defaults->getMode();
$defaultsEncrypted = $defaults->encrypt('Moar Private Info');
$defaultsDecrypted = $defaults->decrypt($defaultsEncrypted);

/*
 * be sure to base64 encode/decode if using in something like HTML
 * note, this example will not function entirely as written, it is just an example!
 */
$html = new Cipher('secret three blah yada $$','serpent','ctr','whirlpool');
$htmlEncrypted = $html->encrypt('Supa Secret Sam');
echo '<input type="hidden" name="encrypted" value="'.base64_encode($htmlEncrypted).'" />';
$htmlDecrypted = $html->decrypt(base64_decode($_POST['encrypted']));

/*
 * the IV is embedded in the encrypted message, so decrypting something like CBC is supported
 */
$sampleData = array('just','an','example');
$persistent1 = new Cipher('correct horse battery staple','rijndael-256','cbc','whirlpool');
$persistentEncrypted = $persistent1->encrypt($sampleData);
$persistent2 = new Cipher('correct horse battery staple','rijndael-256','cbc','whirlpool');
$persistentDecrypted = $persistent2->decrypt($persistentEncrypted);
$isIdentical = count(array_diff($sampleData,$persistentDecrypted)) === 0;

/*
 * example of an insecure, but "fast" encryption method
 * Passphrase is subject to dictionary attack.
 * DES.
 * ECB does not use a random IV.
 * MD5 is not a strong hash.
 */
$insecure = new Cipher('12345','des','ecb','md5');

