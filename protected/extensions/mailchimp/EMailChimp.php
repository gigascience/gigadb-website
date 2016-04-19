<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'base' . DIRECTORY_SEPARATOR . 'MCAPI.class.php';

/**
 * MailChimp API integration (version 1.3)
 * @link http://apidocs.mailchimp.com/api/1.3/
 */
class EMailChimp extends CApplicationComponent
{
    const STATUS_SUBSCRIBED = 'subscribed';
    const STATUS_UNSUBSCRIBED = 'unsubscribed';
    const STATUS_CLEANED = 'cleaned';
    const STATUS_UPDATED = 'updated';
    
    /**
     * @var string API Key - see http://admin.mailchimp.com/account/api
     */
    public $apikey = '';
    
    /**
     * @var string A List Id. use lists() to view all.
     * Also, login to MC account, go to List, then List Tools, and look for the List ID entry
     */
    public $listId = '';
    
    public $ecommerce360Enabled = false;
    public $devMode = false;
    
    private $_api = null;
    
    
    public function init()
    {
        $this->_api = new MCAPI($this->apikey);
    }
    
    /**
     * Returns all available subscription lists with details
     * @return array
     */
    public function lists()
    {
        return $this->_api->lists();
    }
    
    /**
     * Subscribes new email to the subscription list
     * (allows also to add additional data, like name, type, etc.)
     * @param string $email
     * @param array $params Additional data for the list item
     * @param boolean $doubleOptIn
     * @return boolean 
     */
    public function listSubscribe($email, $params, $doubleOptIn = true)
    {
        if ($this->devMode) {
            return true;
        }
        
        // By default this sends a confirmation email - you will not see new members
        // until the link contained in it is clicked!
        $retval = $this->_api->listSubscribe($this->listId, $email, $params, 'html', $doubleOptIn);
        return $this->_api->errorCode === false;
    }
    
    /**
     * Removes given email from the subscription list
     * @param string $email
     * @return boolean 
     */
    public function listUnsubscribe($email, $sendNotification = true)
    {
        if ($this->devMode) {
            return true;
        }
        
        $retval = $this->_api->listUnsubscribe($this->listId, $email, false, $sendNotification, $sendNotification);
        return $this->_api->errorCode === false;
    }
    
    /**
     * Updates an entry in the subscription list.
     * Allows to change entry details (name, type, email, etc.)
     * @param string $email
     * @param array $params
     * @param boolean $doubleOptIn
     * @return boolean 
     */
    public function listUpdateMember($email, $params)
    {
        if ($this->devMode) {
            return true;
        }
        
        $retval = $this->_api->listUpdateMember($this->listId, $email, $params, 'html', true);
        return $this->_api->errorCode === false;
    }
    
    /**
     * Lists all list members with the given status
     * @param string $status
     * @return array
     */
    public function listMembers($status = self::STATUS_SUBSCRIBED, $since = null)
    {
        $retval = $this->_api->listMembers($this->listId, $status, date('Y-m-d H:i:s', strtotime($since)), 0, 15000);
        if ($this->_api->errorCode === false && (int)$retval['total'] > 0) {
            return $retval['data'];
        }
        return array();
    }
    
    /**
     * Checks if the given email address exists on the subscription list
     * @param string $email
     * @return boolean 
     */
    public function emailExists($email)
    {
        if ($this->devMode) {
            return false;
        }
        
        $retval = $this->_api->listMemberInfo($this->listId, array($email));
        return $retval['success'] != 0;
    }
    
    /**
     * Checks whether the given emails exist in the subscription list
     * Returns an array in a form: email -> boolean (false - not exists, true - exists)
     * @param array $emails
     * @return array
     */
    public function emailsExist(array $emails)
    {
        //if ($this->devMode) {
        //    return array_map(function(){return false;}, $emails);
        //}
        
        $retval = $this->_api->listMemberInfo($this->listId, $emails);
        $result = array_flip($emails);
        // set initial value to false (doesn't exist)
        foreach ($result as $id => $val) {
            $result[$id] = false;
        }
        // set only the found emails
        foreach ($retval['data'] as $entry) {
            $result[$entry['email']] = true;
        }
        return $result;
    }
    
    public function membersDetails(array $emails)
    {
        $retval = $this->_api->listMemberInfo($this->listId, $emails);
        //CVarDumper::dump($retval);
        if ($retval['success']) {
            foreach ($retval['data'] as $id => $val) {
                $retval['data'][$id] = $val['data']['merges'];
            }
            return $retval;
        }
        return array();
    }
    
    /**
     * Returns last Mailchimp error
     * @return string 
     */
    public function getError()
    {
        return $this->_api->errorMessage;
    }
    
    
    
    /*** Part for handling Mailchimp Ecommerce360 functionality 
     *** @link http://apidocs.mailchimp.com/api/how-to/ecommerce.php
     ****/
    
    /**
     * Record cookies from the current request URL (if any)
     */
    public function recordCookies()
    {
        if (!$this->ecommerce360Enabled) {
            return false;
        }
        
        $mcCid = Yii::app()->request->getParam('mc_cid');
        $mcEid = Yii::app()->request->getParam('mc_eid');
        if ($mcCid) {
            // store the value in the browser cookie for 30 days
            $cookie = new CHttpCookie('mc_cid', $mcCid);
            $cookie->expire = time() + 86400 * 30; // setting this in CHttpCookie constructor doesn't work
            Yii::app()->request->cookies['mc_cid'] = $cookie;
        }
        if ($mcEid) {
            // store the value in the browser cookie for 30 days
            $cookie = new CHttpCookie('mc_eid', $mcEid);
            $cookie->expire = time() + 86400 * 30;
            Yii::app()->request->cookies['mc_eid'] = $cookie;
        }
        return true;
    }
    
    /**
     * Adds tracking info for the campaign (only if we have campaign_id and email_id
     * stored previously in the browser
     * @param Orders $order
     * @return boolean
     */
    public function addTrackedOrder(Orders $order)
    {
        // need both values and Ecommerce360 enabled
        if (!$this->ecommerce360Enabled || !Yii::app()->request->cookies->contains('mc_cid') || !Yii::app()->request->cookies->contains('mc_eid')) {
            Yii::log("Mailchimp Ecommerce360 - tracked order failed (ID #" . $order['id'] . ") - not enough data", CLogger::LEVEL_INFO);
            return false;
        }
        
        $orderItems = array();
        foreach ($order['items'] as $item) {
            $orderItems[] = array(
                'product_id'    => $item['product_id'],
                'sku'           => $item['sku'],
                'product_name'  => $item['product_name'],
                'category_id'   => $item['category_id'],
                'category_name' => $item['category_name'],
                'qty'           => $item['qty_ordered'],
                'cost'          => $item['price']
            );
        }
        $orderData = array(
            'id'            => $order['id'],
            'campaign_id'   => Yii::app()->request->cookies['mc_cid']->value,
            'email_id'      => Yii::app()->request->cookies['mc_eid']->value,
            'total'         => $order['total'],
            //'order_date'    => now
            'shipping'      => $order['delivery'],
            'tax'           => $order['tax'],
            'store_id'      => Yii::app()->name,
            'store_name'    => Yii::app()->request->serverName,
            'items'         => $orderItems
        );
        if ($this->_api->campaignEcommOrderAdd($orderData)) {
            Yii::log("Mailchimp Ecommerce360 - tracked order added (ID #" . $order['id'] . ")", CLogger::LEVEL_INFO);
            return true;
        }
        Yii::log("Mailchimp Ecommerce360 - tracked order failed (ID #" . $order['id'] . ")", CLogger::LEVEL_INFO);
        return false;
    }
}