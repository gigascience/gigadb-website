<?php

/**
 * Functional test for MailService component class using eml files
 *
 * @author Peter Li <peter+git@gigasciencejournal.com>
 * @license GPL-3.0
 */
class MailServiceTest extends FunctionalTesting
{
    /** @var string directory containing eml files */
    public static $eml_dir;

    /**
     * Initialise eml directory
     */
    public static function setUpBeforeClass()
    {
        self::$eml_dir = Yii::$app->mailer->fileTransportPath;
    }

    /**
     * Remove all eml messages after running tests
     */
    public static function tearDownAfterClass()
    {
        $files = scandir(self::$eml_dir);
        // Iterate files and use unlink to delete them
        foreach($files as $file){
            $file = self::$eml_dir."/".$file;
            if(is_file($file))
                unlink($file);
        }
    }

    /**
     * Test Yii2 swiftmailer component is able to send emails
     */
    public function testItShouldSendEmailUsingYii2Swiftmailer() {
        try {
            $from = "admin@gigadb.org";
            $to = "user@gigadb.org";
            $subject = "Uploading instructions";
            $body = "Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo";

            // Call Yii2 swiftmailer component to send email
            Yii::$app->mailer->compose()
                ->setFrom($from)
                ->setTo($to)
                ->setSubject($subject)
                ->setTextBody($body)
                ->send();

            $msg = $this->getLastMessage();
            $msg_content = $this->getMessageContent($msg);
            $this->assertNotFalse(strpos($msg_content, "Subject: ".$subject), "Email doesn't contain correct subject");
            $this->assertNotFalse(strpos($msg_content, "From: ".$from), "Email doesn't contain sender email address");
            $this->assertNotFalse(strpos($msg_content, "To: ".$to), "Email doesn't contain recipient email address");
            $this->assertNotFalse(strpos($msg_content, $body), "Email doesn't contain message body");
        }
        catch(Error $e) {
            $this->fail("Exception thrown: ".$e->getMessage());
        }
    }

    /**
     * Test functions in MailService Component can send plain text email
     */
    public function testItShouldSendEmailUsingMailService() {
        try {
            $from = "foo@bar.com";
            $to = "hello@world.com";
            $subject = "Testing";
            $body = "lorem ipsum";
            $result = Yii::app()->mailService->sendEmail($from, $to, $subject, $body);
            $this->assertTrue($result, "Problem sending email using MailService sendEmail function");

            $msg = $this->getLastMessage();
            $msg_content = $this->getMessageContent($msg);
            $this->assertNotFalse(strpos($msg_content, "Subject: ".$subject), "Email doesn't contain correct subject");
            $this->assertNotFalse(strpos($msg_content, "From: ".$from), "Email doesn't contain sender email address");
            $this->assertNotFalse(strpos($msg_content, "To: ".$to), "Email doesn't contain recipient email address");
            $this->assertNotFalse(strpos($msg_content, $body), "Email doesn't contain message body");
        }
        catch(Error $e) {
            $this->fail("Exception thrown: ".$e->getMessage());
        }
    }

    /**
     * Test functions in MailService Component can send HTML email
     */
    public function testItShouldSendHTMLEmailUsingMailService() {
        try {
            $from = "foo@xyzzy.com";
            $to = "xyzzy@world.com";
            $subject = "Test HTML message";
            $body = "<h1>Hello World</h1>";
            $result = Yii::app()->mailService->sendHTMLEmail($from, $to, $subject, $body);
            $this->assertTrue($result, "Problem sending email using MailService sendHTMLEmail function");
            $msg = $this->getLastMessage();
            $msg_content = $this->getMessageContent($msg);
            $this->assertNotFalse(strpos($msg_content, "Subject: ".$subject), "HTML email doesn't contain correct subject");
            $this->assertNotFalse(strpos($msg_content, "From: ".$from), "HTML email doesn't contain sender email address");
            $this->assertNotFalse(strpos($msg_content, "To: ".$to), "HTML email doesn't contain recipient email address");
            $this->assertNotFalse(strpos($msg_content, $body), "HTML email doesn't contain message body");
        }
        catch(Error $e) {
            $this->fail("Exception thrown: ".$e->getMessage());
        }
    }

    /**
     * Fetch filenames of eml messages in protected/runtime/mail directory
     */
    private function getMessages()
    {
        return $msgs = array_diff(scandir(self::$eml_dir), array('.', '..'));
    }

    /**
     * Fetch most recent eml filename
     */
    private function getLastMessage()
    {
        $messages = $this->getMessages();
        if (empty($messages))
        {
            $this->fail('No messages found in eml directory');
        }
        $last_msg = end($messages);
        return self::$eml_dir."/".$last_msg;
    }

    /**
     * Get contents of message as an array of strings
     * @param $eml_file
     * @return array|false
     */
    private function getMessageContent($eml_file)
    {
        return $content = file_get_contents($eml_file);
    }
}
