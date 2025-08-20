<?php

declare(strict_types=1);

class EditProfileForm extends CFormModel
{
    public ?string $first_name = null;
    public ?string $last_name = null;
    public ?string $email = null;
    public ?string $affiliation = null;
    public bool $newsletter;
    public int $user_id;
    public ?string $preferred_link = null;

    /**
     * Declares the validation rules.
     * The rules state that username and password are required,
     * and password needs to be authenticated.
     */
    public function rules()
    {
        return array(
            // username and password are required
            array('email, first_name, last_name, newsletter, user_id, affiliation', 'required'),
            array('preferred_link', 'safe'),
        );
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels()
    {
        return array(
                    'email' => Yii::t('app', 'Email'),
                    'first_name' => Yii::t('app', 'First Name'),
                    'last_name' => Yii::t('app', 'Last Name'),
                    'affiliation' => Yii::t('app', 'Affiliation'),
                    'preferred_link' => Yii::t('app', 'Link out preference'),
                    'newsletter' => Yii::t('app', 'Mailing list subscriber'),
        );
    }

    public function updateInfo(): bool
    {
        $user = User::model()->findByPk($this->user_id);
        if ($user) {
            $user->first_name = $this->first_name;
            $user->last_name = $this->last_name;
            $user->affiliation = $this->affiliation;
            $user->password_repeat = 'NoNeed';
            $user->email = $this->email;
            $user->previous_newsletter_state = $user->newsletter;
            $user->newsletter = $this->newsletter;
            $user->preferred_link = $this->preferred_link;

            return $user->save();
        }

        return false;
    }
}
