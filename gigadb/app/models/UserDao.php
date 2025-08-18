<?php

declare(strict_types=1);

namespace GigaDB\models;

class UserDao
{
    public function findByEmail(string $email): ?\User
    {
        return \User::model()->find('email = :email', array(
            ':email' => $email
        ));
    }

}
