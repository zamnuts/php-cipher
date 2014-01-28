<?php

require_once 'Cipher.php';

use \Cipher;

$thisUri	= parse_url($_SERVER['REQUEST_URI']);
$thisUri	= $thisUri['path'];

$ciphers	= Cipher::ciphers();
$modes		= Cipher::modes();
$algos		= Cipher::algos();

$cipher		= isset($_POST['cipher'])?$_POST['cipher']:Cipher::$DEFAULT_CIPHER;
$mode		= isset($_POST['mode'])?$_POST['mode']:Cipher::$DEFAULT_MODE;
$algo		= isset($_POST['algo'])?$_POST['algo']:Cipher::$DEFAULT_ALGO;
$secret		= isset($_POST['secret'])?$_POST['secret']:Cipher::generateKey();
$encrypt	= isset($_POST['encrypt'])?$_POST['encrypt']:'';
$decrypt	= isset($_POST['decrypt'])?$_POST['decrypt']:'';

$inst = new Cipher($secret,$cipher,$mode,$algo);
if ( $encrypt ) {
	$start = microtime(true);
	$encrypted = base64_encode($inst->encrypt($encrypt));
	$time = microtime(true)-$start;
	$subject = $encrypt;
	$encrypt = '';
	$decrypt = $encrypted;
	unset($start);
} else if ( $decrypt ) {
	$start = microtime(true);
	$decrypted = $inst->decrypt(base64_decode($decrypt));
	$time = microtime(true)-$start;
	$subject = $decrypt;
	$encrypt = $decrypted;
	$decrypt = '';
	unset($start);
} else {
	$encrypt = 'Hello world.';
}

?>
<!doctype html>
<html>
	<head>
		<title>Interactive Cipher</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<style type="text/css">
			html,body {font-family:sans-serif;}
			a {color:#333;text-decoration:underline;}
			a:hover {color:#00f;}
			input[type="text"],select {vertical-align:middle;}
			textarea {height:100px;vertical-align:top;}
			input[type="text"],textarea,select {margin:0;padding:2px;border:1px solid #999;width:500px;background:#f9f9f9;box-shadow:1px 1px 4px -1px #999;}
			select {width:506px;}
			input[type="submit"] {padding:5px 8px;cursor:pointer;}
			form > p > span {width:100px;display:inline-block;}
			form .submit {text-align:right;}
				form .submit a {display:inline-block;margin-right:30px;vertical-align:middle;font-size:smaller;}
			.out {padding:10px;border:1px solid #999;background:#f9f9f9;box-shadow:1px 1px 4px -1px #999;}
				.out p {margin:0;padding:10px 0 10px;border-bottom:1px solid #999;}
				.out p:first-child {padding-top:0;}
				.out p:last-child {padding-bottom:0;border-bottom:0;}
				.out span {display:inline-block;margin-right:30px;width:100px;}
				.out code {display:inline-block;font-family:monospace;vertical-align:text-bottom;}
			.out.encrypt-out {background:#ffe9ff;}
			.out.decrypt-out {background:#e9ffff;}
			h1,.out,form {float:left;clear:both;}
		</style>
	</head>
	<body>
		<h1>Interactive Cipher</h1>
		<?php if ( isset($subject,$encrypted) ): ?>
		<div class="out encrypt-out">
			<p><span>Subject:</span> <code><?php echo htmlentities($subject); ?></code></p>
			<p><span>Encrypted:</span> <code><?php echo htmlentities($encrypted); ?></code></p>
			<?php if ( isset($time) ): ?>
			<p><span>Time:</span> <code><?php echo sprintf('%0.16f',$time/1000); ?>&nbsp;ms</code></p>
			<?php endif; ?>
		</div>
		<?php endif; ?>
		<?php if ( isset($subject,$decrypted) ): ?>
		<div class="out decrypt-out">
			<p><span>Subject:</span> <code><?php echo htmlentities($subject); ?></code></p>
			<p><span>Decrypted:</span> <code><?php echo htmlentities($decrypted); ?></code></p>
			<?php if ( isset($time) ): ?>
			<p><span>Time:</span> <code><?php echo sprintf('%0.16f',$time/1000); ?>&nbsp;ms</code></p>
			<?php endif; ?>
		</div>
		<?php endif; ?>
		<form action="<?php echo htmlentities($thisUri); ?>" method="post">
			<p><span>Cipher Algo:</span> <select name="cipher"><?php
				foreach ( $ciphers as $value ) {
					echo '<option value="'.htmlentities($value).'"'.($value===$cipher?' selected="selected"':'').'>'.htmlentities($value).'</option>';
				}
			?></select></p>
			<p><span>Mode:</span> <select name="mode"><?php
				foreach ( $modes as $value ) {
					echo '<option value="'.htmlentities($value).'"'.($value===$mode?' selected="selected"':'').'>'.htmlentities($value).'</option>';
				}
			?></select></p>
			<p><span>Hash Algo:</span> <select name="algo"><?php
				foreach ( $algos as $value ) {
					echo '<option value="'.htmlentities($value).'"'.($value===$algo?' selected="selected"':'').'>'.htmlentities($value).'</option>';
				}
			?></select></p>
			<p><span>Secret:</span> <input type="text" name="secret" value="<?php echo htmlentities($secret); ?>" /></p>
			<p><span>Encrypt:</span> <textarea name="encrypt"><?php echo htmlentities($encrypt); ?></textarea></p>
			<p><span>Decrypt:</span> <textarea name="decrypt"><?php echo htmlentities($decrypt); ?></textarea></p>
			<p class="submit"><a href="<?php echo htmlentities($thisUri); ?>">Reset to Defaults</a> <input type="submit" value="Encrypt/Decrypt &rarr;" /></p>
		</form>
	</body>
</html>