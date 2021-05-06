<?php
/**
 * finders for User related information
 *
 * @uses \User.php
 */
class UserDAO {

	/**
	 * Find a user by email
	 *
	 * @param string $email email of user to find
	 * @return ?\User a User instance if user is found, null otherwise
	 *
	 */
	public function findByEmail(string $email): ?\User
	{
		return User::findAffiliateEmail($email); //that static funtion already exist, so let's just use it.
	}
}
?>