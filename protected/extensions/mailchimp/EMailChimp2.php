<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'base' . DIRECTORY_SEPARATOR . 'MailChimpBase.php';

/**
 * Mailchimp API integration (version 2.0)
 * @link http://apidocs.mailchimp.com/api/2.0/
 */
class EMailChimp2 extends MailChimpBase
{
    const STATUS_SUBSCRIBED = 'subscribed';
    const STATUS_UNSUBSCRIBED = 'unsubscribed';
    const STATUS_CLEANED = 'cleaned';
    const STATUS_UPDATED = 'updated';
    
    /**
     * @var string A List Id. use lists() to view all.
     * Also, login to MC account, go to List, then List Tools, and look for the List ID entry
     */
    public $listId = '';
    
    public $ecommerce360Enabled = false;
    public $devMode = false;
    
    
    /**
     * Returns all available subscription lists with details
     * @return array
     */
    public function lists($filters = null, $start = 0, $limit = 25, $sort_field = 'created', $sort_dir = 'DESC')
    {
        $params = array(
            'filters'       => $filters,
            'start'         => (int)$start,
            'limit'         => (int)$limit,
            'sort_field'    => $sort_field,
            'sort_dir'      => $sort_dir
        );
        return $this->call('lists/list', $params);
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
        
        $params = array(
            'id'            => $this->listId,
            'email'         => array(
                'email' => $email
            ),
            'merge_vars'    => $params,
            'email_type'    => 'html',
            'double_optin'  => $doubleOptIn
        );
        // By default this sends a confirmation email - you will not see new members
        // until the link contained in it is clicked!
        $this->call('lists/subscribe', $params);
        return $this->errorCode === false;
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
        
        $params = array(
            'id'            => $this->listId,
            'email'         => array(
                'email' => $email
            ),
            'delete_member' => false,
            'send_goodbye'  => $sendNotification,
            'send_notify'   => $sendNotification
        );
        $this->call('lists/unsubscribe', $params);
        return $this->errorCode === false;
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
        
        $params = array(
            'id'                => $this->listId,
            'email'             => array(
                'email' => $email
            ),
            'merge_vars'        => $params,
            'email_type'        => 'html',
            'replace_interests' => true
        );
        $this->call('lists/update-member', $params);
        return $this->errorCode === false;
    }
    
    /**
     * Lists all list members with the given status
     * @param string $status
     * @return array
     */
    public function listMembers($status = self::STATUS_SUBSCRIBED, $since = null)
    {
        $params = array(
            'id'        => $this->listId,
            'status'    => $status,
            'opts'      => array(
                // date('Y-m-d H:i:s', strtotime($since))
                'start'         => 0,
                'limit'         => 100,
                'sort_field'    => 'last_update_time',
                'sort_dir'      => 'DESC'
            )
        );
        $retval = $this->call('lists/members', $params);
        if ($this->errorCode === false && (int)$retval['total'] > 0) {
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
        
        $params = array(
            'id'        => $this->listId,
            'emails'    => array(
                array(
                    'email' => $email
                )
            )
        );
        $retval = $this->call('lists/member-info', $params);
        return $retval['success_count'] != 0;
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
        
        $emailsArray = array();
        foreach ($emails as $email) {
            $emailsArray[] = array(
                'email'=>$email
            );
        }
        $params = array(
            'id'        => $this->listId,
            'emails'    => $emailsArray
        );
        $retval = $this->call('lists/member-info', $params);
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
        $emailsArray = array();
        foreach ($emails as $email) {
            $emailsArray[] = array(
                'email'=>$email
            );
        }
        $params = array(
            'id'        => $this->listId,
            'emails'    => $emailsArray
        );
        $retval = $this->call('lists/member-info', $params);
        if ($retval['success_count'] > 0) {
            foreach ($retval['data'] as $id => $val) {
                $retval['data'][$id] = $val['merges'];
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
        return $this->errorMessage;
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
    public function addTrackedOrder(array $order)
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