mailchimp-extension
===================

Yii extension for Mailchimp API v1.3 and v2.0 with [Ecommerce360](http://kb.mailchimp.com/article/what-is-ecommerce360-and-how-does-it-work-with-mailchimp/) support.

This extension is a simple facade for Mailchimp API allowing to make most common calls in a simple way.

It was created out of necessity and is not pretending to be a fully featured Mailchimp extension - only a piece of code that simplifies Mailchimp integration with most commonly used functions.

## Requirements ##

 * PHP 5.3+

## Installation ##

Copy the files to `/protected/extensions/mailchimp` folder (or any other folder, but you'll need to update some paths).

To set up Mailchimp API v1.3 integration just add this to your config file:

    return array(
        // (...)
        'components' => array(
            // (...)
            'mailchimp' => array(
                // EMailChimp == API v1.3 integration
                'class' => 'ext.mailchimp.EMailChimp',
                // please replace with your API key
                'apikey' => 'your-api-key',
                // you can get your `listId` from Mailchimp panel - go to List, then List Tools, and look for the List ID entry.
                'listId' => 'your-list-id',
                // (optional - default **false**) whether to use Ecommerce360 support or not
                'ecommerce360Enabled' => false,
                // (optional - default **false**) whether to enable dev mode or not
                'devMode' => false
            ),
            // (...)
        )
    );
	
It's the same situation with Mailchimp API v2.0 integration, you only need to change the class name:

    return array(
        // (...)
        'components' => array(
            // (...)
            'mailchimp' => array(
                // EMailChimp2 == API v2.0 integration
                'class' => 'ext.mailchimp.EMailChimp2',
                // please replace with your API key
                'apikey' => 'your-api-key',
                // you can get your `listId` from Mailchimp panel - go to List, then List Tools, and look for the List ID entry.
                'listId' => 'your-list-id',
                // (optional - default **false**) whether to use Ecommerce360 support or not
                'ecommerce360Enabled' => false,
                // (optional - default **false**) whether to enable dev mode or not
                'devMode' => false
            ),
            // (...)
        )
    ); 

## Usage ##

E.g. to get a list of available mailchimp lists:

    $lists = Yii::app()->mailchimp->lists();

To record Ecommerce360 cookies just add this to a controller action:

    Yii::app()->mailchimp->recordCookies();

Then use this to track the actual order:

    Yii::app()->mailchimp->addTrackedOrder(array(
        'id'       => 1,   // order ID
        'total'    => 100, // order total
        'delivery' => 10,  // delivery cost
        'tax'      => 19,  // tax amount
        'items'    => array( // contains all items from the order
            array(
                'product_id'    => 34,              // product ID
                'sku'           => 'sku_code',      // sku code
                'product_name'  => 'Product Name,   // product name
                'category_id'   => 4,               // category ID
                'category_name' => 'Category Name', // category name
                'qty'           => 2,               // quantity of items bought
                'cost'          => 50               // price of a single item
            ),
            array(
                // (...)
            ),
            // (...)
        )
    ));

Dev mode allows to use the extension in read-only mode.

## Available functions ##

Both integrations (with API v1.3 and API v2.0) have the same set of functions:

    lists()
    listSubscribe($email, $params, $doubleOptIn = true)
    listUnsubscribe($email, $sendNotification = true)
    listUpdateMember($email, $params) 
    listMembers($status = self::STATUS_SUBSCRIBED) 
    emailExists($email)
    emailsExist(array $emails)
    membersDetails(array $emails)
    getError()

The two functions below will only work when `ecommerce360Enabled` flag is set to `true`.
    
    recordCookies()
    addTrackedOrder(array $order)
    
Please check the extension file for detailed documentation.

## Resources ##

 * [Download latest version](https://github.com/procreativeeu/mailchimp-extension/archive/master.zip)
 * [Author's page](http://procreative.eu)