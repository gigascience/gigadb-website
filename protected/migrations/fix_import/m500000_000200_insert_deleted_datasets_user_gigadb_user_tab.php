<?php

class m500000_000200_insert_deleted_datasets_user_gigadb_user_tab extends CDbMigration
{
    /**
     * Creates gigadb_user table record for deleted datasets user if 
     * the account does not exist
     */
    public function safeUp()
    {
        $email = Yii::app()->params['deleted_datasets_user_email'];
        $user = User::model()->find(array(
            'select' => 'email',
            'condition' => 'email=:email',
            'params' => array(':email' => $email),
        ));
        if($user == null)
        {
            $password = Yii::app()->params['deleted_datasets_user_password'];
            $hash = sodium_crypto_pwhash_str(
                $password,
                SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE,
                SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE);

            $this->insert('gigadb_user', array(
                'email' => $email,
                'password' => $hash,
                'first_name' => 'Deleted Datasets',
                'last_name' => 'User',
                'affiliation' => 'GigaScience',
                'is_activated' => 't',
                'newsletter' => 'f',
                'previous_newsletter_state' => 'f',
                'username' => $email,
                'preferred_link' => 'EBI',
            ));
        }
    }

    public function safeDown()
    {
        // This function is empty because reverting safeUp() will mean the
        // delete datasets feature will break
    }
}
