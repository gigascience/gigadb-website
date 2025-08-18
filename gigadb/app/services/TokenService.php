<?php

declare(strict_types=1);

namespace GigaDB\services;

use GigaDB\models\UserDao;
use Lcobucci\JWT\Builder;
use yii;

/**
 * Service to manage JSON Web Token used to authenticate
 * to File Upload Wizard API
 *
 *
 * @property string     $jwtTTL     time validity for the JSON Web Tokens
 * @property Builder    $jwtBuilder JSON Web Token builder library
 * @property            $jwtSigner
 * @property UserDao    $users      finders for acessing User data
 * @property \DateTime  $dt         DateTime object for time calculation
 *
 * @author  Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class TokenService extends yii\base\Component
{
    public int       $jwtTTL;
    public Builder   $jwtBuilder;
    public           $jwtSigner;
    public UserDao   $users;
    public \DateTime $dt;

    public function __construct(int $jwtTTL, Builder $jwtBuilder, $jwtSigner, UserDao $users, \DateTime $dt, $config = [])
    {
        $this->jwtTTL = $jwtTTL;
        $this->jwtBuilder = $jwtBuilder;
        $this->jwtSigner = $jwtSigner;
        $this->users = $users;
        $this->dt = $dt;

        parent::__construct($config);
    }


    /**
     * Initializes the application component.
     * This method overrides the parent implementation by setting default cache key prefix.
     */
    public function init()
    {
        parent::init();
    }

    /**
     * Generate JWT token for a user
     *
     * If the last two parameters are not provided or are null, the user is searched in the database
     * using email, and full name and role is retrieved from the database record
     *
     * @param string      $email    the email of the user to lookup and generate a token for
     * @param null|string $fullName name of the user for which to create the token
     * @param null|string $role     GigaDB role of the user for which to create the token
     *
     * @return \Lcobucci\JWT\Token the signed token
     */
    public function generateTokenForUser(string $email, string $fullName = null, string $role = null): \Lcobucci\JWT\Token
    {
        if (null === $fullName || null === $role) {
            $user = $this->users->findByEmail($email);
            $role = $user->role;
            $fullName = $user->getFullName();
        }

        $signer = $this->jwtSigner;
        $issuedTime = $this->dt->format('U');
        $notBeforeTime = $issuedTime;
        $expirationTime = $this->dt->modify("+1 hour")->format('U');

        // Retrieves the generated token

        return $this->jwtBuilder
            ->setIssuer('www.gigadb.org') // Configures the issuer (iss claim)
            ->setAudience('fuw.gigadb.org') // Configures the audience (aud claim)
            ->setSubject('API Access request from client') // Configures the subject
            ->set('email', $email)
            ->set('name', $fullName)
            ->set('role', $role)
            ->setIssuedAt($issuedTime) // Configures the time that the token was issue (iat claim)
            ->setNotBefore($notBeforeTime) // Configures the time before which the token cannot be accepted (nbf claim)
            ->setExpiration($expirationTime)    // Configures the expiration time of the token (exp claim) 1 year
            ->sign($signer, Yii::$app->jwt->key)// creates a signature using [[Jwt::$key]]
            ->getToken();
    }
}
