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
            $this->assertTrue(str_replace("Subject: ", "", $msg_content[2]) === $subject, "Email doesn't contain correct subject");
            $this->assertTrue(str_replace("From: ", "", $msg_content[3]) === $from, "Email doesn't contain sender email address");
            $this->assertTrue(str_replace("To: ", "", $msg_content[4]) === $to, "Email doesn't contain recipient email address");

            // Check message body
            $msg_body = end($msg_content);
            $this->assertTrue(trim($msg_body) === trim($body), "Mailtrap email doesn't contain content");
        }
        catch(Error $e) {
            $this->fail("Exception thrown: ".$e->getMessage());
        }
    }

    /**
     * Test functions in MailService Component can send emails
     */
    public function testItShouldSendEmailUsingMailService() {
        try {
            $result = Yii::app()->mailService->sendEmail("foo@bar.com", "hello@world.com", "Testing", "lorem ipsum");
            $this->assertTrue($result, "Problem sending email using sendEmail function");

            $result = Yii::app()->mailService->sendHTMLEmail("foo@bar.com", "hello@world.com", "HTML email test", "<h1>lorem ipsum</h1>");
            $this->assertTrue($result, "Problem sending email using sendHTMLEmail function");
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
        return $lines = file($eml_file, FILE_IGNORE_NEW_LINES);
    }
}
