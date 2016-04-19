<?php

class McController extends Controller {
  public function actionSync() {
    if (isset($_REQUEST['type'])) {
      switch ($_REQUEST['type']) {
      case 'subscribe':
        $this->saveSubscribeStatus($_REQUEST['data'], true);
        break;
      case 'unsubscribe':
        $this->saveSubscribeStatus($_REQUEST['data'], false);
        break;
      case 'cleaned':
        $this->saveSubscribeStatus($_REQUEST['data'], false);
        break;
      }
    }
  }

  private function saveSubscribeStatus($data, $subscribe) {
    Yii::log(__FUNCTION__."> ".$subscribe, 'debug');
    $email = $data['email'];
    $user = User::model()->findByAttributes(array('email' => $email));
    if ($user !== null) {
      // PHP hates 'false', that's why
      $user->previous_newsletter_state = $user->newsletter = $subscribe ? $subscribe : 0;
      if (!$user->save()) {
        Yii::log(__FUNCTION__."> Error: could not save user $email subscribe status", 'error');
        Yii::log(print_r($user->getErrors(), true), 'error');
      }
    }
  }

  public function actionAddWebhook() {
    $api = new MCAPI(Yii::app()->params['mc_apikey']);
    $api->listWebhookAdd(Yii::app()->params['mc_listID'], 
                         Yii::app()->params['home_url'] . '/mc/sync');
    if ($api->errorCode){
      switch ($api->errorCode) {
      case 508:
        print "AddWebhook already added\n";
        break;
      default:
        print "AddWebhook failed!\n";
        print "code:".$api->errorCode."\n";
        print "msg :".$api->errorMessage."\n";
      }
    } else {
      print "Successfully";
    }
  }
}