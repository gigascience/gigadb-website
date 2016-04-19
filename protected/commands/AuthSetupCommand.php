<?

class AuthSetupCommand extends CConsoleCommand {
    public function getHelp() {
        return 'Usage: authsetup';
    }

    public function run($args) {
        Yii::log('Running command authsetup', 'debug');

        $connection = Yii::app()->db;

        Yii::log('Clearing out old auth data', 'debug');
        $query = <<<EO_SQL
DELETE FROM AuthItem;
EO_SQL;
        $command = $connection->createCommand($query);
        $command->execute();

        $query = <<<EO_SQL
DELETE FROM AuthItemChild;
EO_SQL;
        $command = $connection->createCommand($query);
        $command->execute();

        $auth = Yii::app()->authManager;
         
        $auth->createOperation('createUser', 'create user');
        $auth->createOperation('showUser', 'read user');
        $auth->createOperation('updateUser', 'update user');
        $auth->createOperation('deleteUser', 'delete user');
        $auth->createOperation('listUsers', 'list users');

        $bizRule = 'return Yii::app()->user->_id==$params["user"]->id;';
        $task = $auth->createTask('updateOwnUser', 'update own user', $bizRule);
        $task->addChild('updateUser');

        $bizRule = 'return Yii::app()->user->_id==$params["user"]->id;';
        $task = $auth->createTask('showOwnUser', 'update own user', $bizRule);
        $task->addChild('showUser');

        #$auth->createOperation('createPayment', 'create payment');
        #$auth->createOperation('showPayment', 'read payment');
        #$auth->createOperation('updatePayment', 'update payment');
        #$auth->createOperation('deletePayment', 'delete payment');
        #$auth->createOperation('listPayments', 'list payments');

        #$task = $auth->createTask('managePayments', 'manage payments');
        #$task->addChild('createPayment');
        #$task->addChild('showPayment');
        #$task->addChild('updatePayment');
        #$task->addChild('deletePayment');
        #$task->addChild('listPayments');

        #$bizRule = 'return Yii::app()->user->_id==$params["payment"]->user_id;';
        #$task = $auth->createTask('manageOwnPayment', 'manage own payment', $bizRule);
        #$task->addChild('updatePayment');
        #$task->addChild('deletePayment');
        
        #$bizRule = 'return Yii::app()->user->_id==$params["payment"]->user_id;';
        #$task = $auth->createTask('showOwnPayment', 'show own payment', $bizRule);
        #$task->addChild('showPayment');

        $role=$auth->createRole('admin');
        #$role->addChild('managePayments');
        #$role->addChild('manageInbounds');
        $role->addChild('createUser');
        $role->addChild('showUser');
        $role->addChild('updateUser');
        $role->addChild('deleteUser');
        $role->addChild('listUsers');

        #$auth->assign('admin', 'admin');
        #$auth->revoke('jake', 'user');
    } 
}
?>

