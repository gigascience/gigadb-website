<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class MailHelper extends \Codeception\Module
{
    /** @var string directory containing eml files */
    public static $eml_dir;

    /**
     * Get eml file output directory
     * @return void
     */
    public function _initialize(): void
    {
        $currentConfig = require("protected/config/yii2/test.php");
        self::$eml_dir = $currentConfig["components"]["mailer"]["fileTransportPath"];
    }

    /**
     * Clear all emails from eml directory. Probably want to do this before
     * sending emails
     */
    public function resetEmails(): void
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
     * Fetch filenames of messages in eml file output directory
     */
    public function getMessages()
    {
        return $msgs = array_diff(scandir(self::$eml_dir), array('.', '..'));
    }

    /**
     * Fetch most recent eml filename
     */
    public function getLastMessage()
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
    public function getMessageContent($eml_file)
    {
        return $content = file_get_contents($eml_file);
    }

    /**
     * Grab urls from email
     */
    public function grabUrlsFromLastEmail(): array
    {
        $regex = '#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#';
        $email = $this->getLastMessage();
        $content = $this->getMessageContent($email);
        preg_match_all($regex, $content, $matches);
        return $matches[0];
    }
}
