php-cipher
==========

## Intro

### About
- A simple abstraction for PHP's mcrypt extension. 
- Limited but easy-to-use OOP-style functionality.
- This class is not entirely idiot-proof, however.
- Defaults to AES-256-CBC w/ SHA-256 hashed key.
- The IV (if applicable) is embedded in the encrypted output; the `decrypt` method expects the IV to be prefixed.
- `encrypt` will accept any data type - it internally serializes the input.
- Key and data padding is not necessary, PHP does this internally.

### Included Files
- See `examples.php` for usage.
- Class is in `Cipher.php`.
- An interactive form is available as `interactive.php`.

## Usage

### Basic
```php
$key    = 'secret one @!#$';
$cipher = 'blowfish';
$mode   = 'cbc';
$algo   = 'sha256';
$cbcBlowfish = new Cipher($key,$cipher,$mode,$algo);
$cbcBlowfishEncrypted = $cbcBlowfish->encrypt('Top Secret Stuff');
$cbcBlowfishDecrypted = $cbcBlowfish->decrypt($cbcBlowfishEncrypted);
```

### Persistent
Since the IV prefixed the encrypted output automatically, it can be easily decrypted later on:

```php
$key    = 'correct horse battery staple';
$cipher = 'rijndael-256';
$mode   = 'cbc';
$algo   = 'whirlpool';

// remember, Cipher can accept any serializable data type
$data   = array('just','an','example');

// something on page one
$persistent1 = new Cipher($key,$cipher,$mode,$algo); 
$persistentEncrypted = $persistent1->encrypt($$data);
// do something with $persistentEncrypted

// and at a later date use the same Cipher configuration to decrypt
$persistent2 = new Cipher($key,$cipher,$mode,$algo);
$persistentDecrypted = $persistent2->decrypt($persistentEncrypted);

// do something with the decrypted data
$isIdentical = count(array_diff($data,$persistentDecrypted)) === 0;

```

## License: BSD 3-Clause
Copyright (c) 2014, Andrew Zammit <zammit.andrew@gmail.com> (zamnuts)
All rights reserved.

Redistribution and use in source and binary forms, with or without modification,
are permitted provided that the following conditions are met:

* Redistributions of source code must retain the above copyright notice, this
  list of conditions and the following disclaimer.

* Redistributions in binary form must reproduce the above copyright notice, this
  list of conditions and the following disclaimer in the documentation and/or
  other materials provided with the distribution.

* Neither the name of Andrew Zammit nor the names of its
  contributors may be used to endorse or promote products derived from
  this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR
ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.