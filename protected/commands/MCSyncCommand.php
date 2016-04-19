<?php
class MCSyncCommand extends CConsoleCommand {
  public function getHelp() {
    print "Sync subscription/unsubscription list with MC";
  }
  public function run($args) {
    $subscribers = User::model()->findAllByAttributes(array('newsletter'=>true, 
							    'previous_newsletter_state'=>false));
    $batch = array();
    foreach ($subscribers as $s) {
      $batch[] = array('EMAIL'=>$s->email, 'FNAME'=>$s->first_name, 'LNAME'=>$s->last_name);
      $s->previous_newsletter_state = true;
      $s->save();
    }
    $api = new MCAPI(Yii::app()->params['mc_apikey']);
    $result = $api->listBatchSubscribe(Yii::app()->params['mc_listID'], $batch, false);

    if (isset($result['errors'])) {
      foreach ($result['errors'] as $e) {
        if ($e['code'] == 212) { //User unsubscribed, need to resubscribe individually
          $user = User::model()->findByAttributes(array('email' => $e['email']));
          if ($user !== null) {
            $info = array('FNAME'=>$user->first_name, 'LNAME'=>$user->last_name);
            $api->listSubscribe(Yii::app()->params['mc_listID'], $user->email, $info);
	    if ($api->errorCode){
                echo "Subscribe $user->email failed!\n";
                echo "code:".$api->errorCode."\n";
                echo "msg :".$api->errorMessage."\n";
	    } else {
                echo "Re-Subscribe $user->email Successfully\n";
	    }
          }
	} else if ($e['code'] != 214) {
          print_r($e);
	}
      }
    }


    $unsubscribers = User::model()->findAllByAttributes(array('newsletter'=>false,
							      'previous_newsletter_state'=>true));
    $batch = array();
    foreach ($unsubscribers as $u) {
      $batch[] = $u->email;      
      $u->previous_newsletter_state = $u->newsletter = 0;
      $u->save();
    }

    $result = $api->listBatchUnsubscribe(Yii::app()->params['mc_listID'], $batch);
    if (isset($result['errors'])) {
      foreach ($result['errors'] as $e) {
	if ($e['code'] != 215) {
          print_r($e);
	}
      }
    }    
  }
}