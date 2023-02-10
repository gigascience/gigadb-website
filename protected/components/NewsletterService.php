<?php

/**
 * Provide methods to subscribe/unsubscribe a user from the newsletter
 *
 * @property \DrewM\MailChimp\MailChimp|null $newsletter_api instantiated Mailchimp client or null
 * @property string $list_id the id fo the subscription list
 * @property string $api_key will be used to instantiate a new Mailchimp client if $newsletter_api is null
 * @uses \DrewM\MailChimp\MailChimp
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
*/
class NewsletterService extends CApplicationComponent
{
    /** @var string $api_key Mailchimp API key. Get value set in __construct or in parent::init when set in configuration */
    public $api_key;

    /** @var string $list_id Mailchimp list id. Get value set in __construct or in parent::init when set in configuration */
    public $list_id;

    /**
     * @var \DrewM\MailChimp\MailChimp $newsletter_api Mailchimp client object.
     * Get value set in __construct or in $this->init
     */
    public $newsletter_api;

    public function __construct($api_key = null, $list_id = null, $newsletter_api = null)
    {
        $this->api_key = $api_key;
        $this->list_id = $list_id;

        if (null !== $newsletter_api) {
            $this->newsletter_api = $newsletter_api;
        } elseif (null != $api_key) {
            $this->newsletter_api = new \DrewM\MailChimp\MailChimp($this->api_key);
        }
    }

    /**
     * Implements init() from IAppicationComponent interface for further initialization
     *
     * First called default init from parent and then instantiate a Mailchimp client
     * if it is not yet the case (normal case when this service is used as a configured Yii Application Component)
     * By this time, $this->api_key has been setup through configuration
     */
    public function init()
    {
        parent::init();

        if (null == $this->newsletter_api) {
            $this->newsletter_api = new \DrewM\MailChimp\MailChimp($this->api_key);
        }
    }

    /**
     * Add the email address to the subscription list $list_id
     *
     * @param string $email email address to add
     * @param string $first_name First name associated to the email address, optional
     * @param string $last_name Last name associated to the email address, optional
     * @see http://php.net/manual/en/filter.filters.validate.php
     * @see http://php.net/manual/en/function.checkdnsrr.php
     * @see https://en.wikipedia.org/wiki/Internationalized_domain_name
     * @return boolean whether the subscription was successful or not
     */
    public function addToMailing($email, $first_name = null, $last_name = null)
    {

        // pre-check email
        $username = explode("@", $email)[0];
        $domain = explode("@", $email)[1];
        if (! ($username && $domain)) {
            return false;
        }
        $latinised_username = idn_to_ascii($username);
        $latinised_domain = idn_to_ascii($domain);

        // ensuring that the email is valid according to RFC 822
        $filtered_email = filter_var($latinised_username . "@" . $latinised_domain, FILTER_VALIDATE_EMAIL);
        if (!$filtered_email) {
            return false;
        }

        // ensuring that the domain of the email exists as a valid email (MX) domain
        if (!checkdnsrr($latinised_domain, 'MX')) {
            return false;
        }

        if ($first_name && $last_name) {
            $result = $this->newsletter_api->post("lists/" . $this->list_id . "/members", [
                'email_address' => $filtered_email,
                'merge_fields' => ['FNAME' => $first_name, 'LNAME' => $last_name],
                'status'        => 'subscribed',
            ]);
        } else {
            $result = $this->newsletter_api->post("lists/" . $this->list_id . "/members", [
                'email_address' => $filtered_email,
                'status'        => 'subscribed',
            ]);
        }

        if (
            $this->newsletter_api->success() # email address is add to list
                || in_array('Member Exists', $result) # email address is already in list, still considered a success
        ) {
            return true;
        } else {
            Yii::log($result['detail'], 'error');
            return false;
        }
    }

    /**
     * Remove the email address from the subscription list $list_id
     *
     * @param string $email email address to add
     * @return boolean whether the subscription was successful or not
     */
    public function removeFromMailing($email)
    {
        $subscriber_hash =  $this->newsletter_api->subscriberHash($email);

        $result = $this->newsletter_api->delete("lists/" . $this->list_id . "/members/$subscriber_hash");

        if ($this->newsletter_api->success() || 404 == $result['status']) {
            return true;
        } else {
            Yii::log($result['detail'], 'error');
            return false;
        }
    }

    /**
     * Get info about the list $list_id
     *
     * @return boolean whether the subscription was successful or not
     */
    public function getMailingListInfo()
    {

        $result = $this->newsletter_api->get("lists/" . $this->list_id);

        if ($this->newsletter_api->success()) {
            return true;
        } else {
            Yii::log($result['detail'], 'error');
            return false;
        }
    }
}
