<?php
/**
 * Verify that password supplied by a user attempting a login matches the hashes in the database
 *
 * Until every user has changed their password to use the stronger hashing algorithm,
 * The verification will happen in two stages, corresponding to each algorithm
 * The class's API is modeled after CPasswordHelper
 *
 * @uses sodium_crypto_pwhash_str_verify()
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class PasswordHelper
{
	/**
	 * Verify a password against a password hash created with libsodium
	 *
	 * It first checks if the hash is 32 characters length (length of MD5 hashes),
	 * in which case it delegates to verifyLegacyPassword()
	 * Otherwise, it uses sodium_crypto_pwhash_str_verify from php-libsodium to verify the password and the hashes match
	 *
	 * @param string $password The password to verify. If password is empty or not a string, method will return false.
	 * @param string $hash The hash to verify the password against.
	 * @uses sodium_crypto_pwhash_str_verify()
	 * @uses sodium_memzero()
	 * @return bool True if the password matches the hash.
	 * @see https://paragonie.com/book/pecl-libsodium/read/07-password-hashing.md
	 */
	public static function verifyPassword($password, $hash)
	{
		if ( 32 == strlen($hash) )
			return self::verifyLegacyPassword($password, $hash) ;

		if (sodium_crypto_pwhash_str_verify($hash, $password)) {
		    // recommended: wipe the plaintext password from memory
		    sodium_memzero($password);

		    // Password was valid
		    return true;
		} else {
		    // recommended: wipe the plaintext password from memory
		    sodium_memzero($password);

		    // Password was invalid.
		    return false;
		}


	}

	/**
	 * Verify a password against a password hash created the old way
	 *
	 * It must use PHP's hash_equals to avoid timing attacks
	 *
	 * @param string $password The password to verify. If password is empty or not a string, method will return false.
	 * @param string $hash The hash to verify the password against.
	 * @uses sodium_memzero()
	 * @return bool True if the password matches the hash.
	 */
	public static function verifyLegacyPassword($password, $hash)
	{
		if ( hash_equals($hash, md5($password)) ) {
		    // recommended: wipe the plaintext password from memory
		    sodium_memzero($password);

		    // Password was valid
		    return true;
		} else {
		    // recommended: wipe the plaintext password from memory
		    sodium_memzero($password);

		    // Password was invalid.
		    return false;
		}


	}
}

 ?>