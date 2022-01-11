<?php
namespace Helper;

use Yii;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Mail extends \Codeception\Module
{
    /** @var string directory containing eml files */
    public static $eml_dir;

    // HOOK: used after configuration is loaded
//    public function _initialize(): void
//    public function _before(\Codeception\TestInterface $test)
//    {
//        print_r("In _initialize()...\n");
//        print_r("Params: ".Yii::app()->params['adminEmail']);
//        print_r("eml dir: ".\Yii::$app->mailer->fileTransportPath);
//        print_r("components: ".\Yii::$app->components);
//        print_r("components: ".\Yii::app()->components);
//        print_r("eml dir: ".\Yii::$app->id);


//        self::$eml_dir = \Yii::app()->mailer->fileTransportPath;
//        self::$eml_dir = \Yii::$app->mailer->fileTransportPath;
//        print_r("eml dir: ".\Yii::$app->mailer->fileTransportPath);
//    }

    /**
     * Clear all emails from eml directory. You probably want to do this before
     * you do the thing that will send emails
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
     * Fetch filenames of eml messages in protected/runtime/mail directory
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

################################################################################

    /**
     * See In Last Email
     *
     * Look for a string in the most recent email
     *
     * @author Jordan Eldredge <jordaneldredge@gmail.com>
     **/
    public function seeInLastEmail(string $expected): void
    {
        $email = $this->lastMessage();
        $this->seeInEmail($email, $expected);
    }

    /**
     * See In Last Email subject
     *
     * Look for a string in the most recent email subject
     *
     * @author Antoine Augusti <antoine.augusti@gmail.com>
     **/
    public function seeInLastEmailSubject(string $expected): void
    {
        $email = $this->lastMessage();
        $this->seeInEmailSubject($email, $expected);
    }

    /**
     * Don't See In Last Email subject
     *
     * Look for the absence of a string in the most recent email subject
     **/
    public function dontSeeInLastEmailSubject(string $expected): void
    {
        $email = $this->lastMessage();
        $this->dontSeeInEmailSubject($email, $expected);
    }

    /**
     * Don't See In Last Email
     *
     * Look for the absence of a string in the most recent email
     **/
    public function dontSeeInLastEmail(string $unexpected): void
    {
        $email = $this->lastMessage();
        $this->dontSeeInEmail($email, $unexpected);
    }

    /**
     * See In Last Email To
     *
     * Look for a string in the most recent email sent to $address
     *
     * @author Jordan Eldredge <jordaneldredge@gmail.com>
     **/
    public function seeInLastEmailTo(string $address, string $expected): void
    {
        $email = $this->lastMessageTo($address);
        $this->seeInEmail($email, $expected);
    }

    /**
     * Don't See In Last Email To
     *
     * Look for the absence of a string in the most recent email sent to $address
     **/
    public function dontSeeInLastEmailTo(string $address, string $unexpected): void
    {
        $email = $this->lastMessageTo($address);
        $this->dontSeeInEmail($email, $unexpected);
    }

    /**
     * See In Last Email Subject To
     *
     * Look for a string in the most recent email subject sent to $address
     *
     * @author Antoine Augusti <antoine.augusti@gmail.com>
     **/
    public function seeInLastEmailSubjectTo(string $address, string $expected): void
    {
        $email = $this->lastMessageTo($address);
        $this->seeInEmailSubject($email, $expected);
    }

    /**
     * Don't See In Last Email Subject To
     *
     * Look for the absence of a string in the most recent email subject sent to $address
     **/
    public function dontSeeInLastEmailSubjectTo(string $address, string $unexpected): void
    {
        $email = $this->lastMessageTo($address);
        $this->dontSeeInEmailSubject($email, $unexpected);
    }

    public function lastMessageTo(string $address): \Codeception\Util\Email
    {
        $ids = [];
        $messages = $this->messages();
        if (empty($messages)) {
            $this->fail("No messages received");
        }

        foreach ($messages as $message) {
            foreach ($message['recipients'] as $recipient) {
                if (strpos($recipient, $address) !== false) {
                    $ids[] = $message['id'];
                }
            }
        }

        if (count($ids) === 0) {
            $this->fail("No messages sent to {$address}");
        }

        return $this->emailFromId(max($ids));
    }

    public function lastMessageFrom(string $address): \Codeception\Util\Email
    {
        $ids = [];
        $messages = $this->messages();
        if (empty($messages)) {
            $this->fail("No messages received");
        }

        foreach ($messages as $message) {
            if (strpos($message['sender'], $address)) {
                $ids[] = $message['id'];
            }

            // @todo deprecated, remove
            foreach ($message['recipients'] as $recipient) {
                if (strpos($recipient, $address) !== false) {
                    trigger_error('`lastMessageFrom` no longer accepts a recipient email.', E_USER_DEPRECATED);
                    $ids[] = $message['id'];
                }
            }
        }

        if (count($ids) === 0) {
            $this->fail("No messages sent from {$address}");
        }

        return $this->emailFromId(max($ids));
    }

    /**
     * Grab Matches From Last Email
     *
     * Look for a regex in the email source and return it's matches
     *
     * @author Stephan Hochhaus <stephan@yauh.de>
     * @return mixed[]
     **/
    public function grabMatchesFromLastEmail(string $regex): array
    {
        $email = $this->lastMessage();
        return $this->grabMatchesFromEmail($email, $regex);
    }

    /**
     * Grab From Last Email
     *
     * Look for a regex in the email source and return it
     *
     * @author Stephan Hochhaus <stephan@yauh.de>
     **/
    public function grabFromLastEmail(string $regex): string
    {
        $matches = $this->grabMatchesFromLastEmail($regex);
        return $matches[0];
    }

    /**
     * Grab Matches From Last Email To
     *
     * Look for a regex in most recent email sent to $addres email source and
     * return it's matches
     *
     * @author Stephan Hochhaus <stephan@yauh.de>
     * @return mixed[]
     **/
    public function grabMatchesFromLastEmailTo(string $address, string $regex): array
    {
        $email = $this->lastMessageTo($address);
        return $this->grabMatchesFromEmail($email, $regex);
    }

    /**
     * Grab From Last Email To
     *
     * Look for a regex in most recent email sent to $addres email source and
     * return it
     *
     * @author Stephan Hochhaus <stephan@yauh.de>
     **/
    public function grabFromLastEmailTo(string $address, string $regex): string
    {
        $matches = $this->grabMatchesFromLastEmailTo($address, $regex);
        return $matches[0];
    }

    /**
     * Grab Urls From Email
     *
     * Return the urls the email contains
     *
     * @author Marcelo Briones <ing@marcelobriones.com.ar>
     * @return mixed[]
     */
    public function grabUrlsFromLastEmail(): array
    {
        $regex = '#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#';
        $email = $this->lastMessage();

        $message = Message::from($email->getSource());

        $text = $message->getTextContent();
        preg_match_all($regex, $text, $text_matches);

        $html = $message->getHtmlContent();
        preg_match_all($regex, $html, $html_matches);

        return array_merge($text_matches[0], $html_matches[0]);
    }

    /**
     * Grab Attachments From Email
     *
     * Returns array with the format [ [filename1 => bytes1], [filename2 => bytes2], ...]
     *
     * @return array<string, string>
     * @author Marcelo Briones <ing@marcelobriones.com.ar>
     */
    public function grabAttachmentsFromLastEmail(): array
    {
        $email = $this->lastMessage();

        $message = Message::from($email->getSource());

        $attachments = [];

        foreach ($message->getAllAttachmentParts() as $attachmentPart) {
            $filename = $attachmentPart->getFilename();
            $content = $attachmentPart->getContent();
            $attachments[$filename] = $content;
        }

        return $attachments;
    }

    /**
     * See Attachment In Last Email
     *
     * Look for a attachement with certain filename in the most recent email
     *
     * @author Marcelo Briones <ing@marcelobriones.com.ar>
     **/
    public function seeAttachmentInLastEmail(string $expectedFilename): void
    {
        $email = $this->lastMessage();
        $message = Message::from($email->getSource());

        foreach ($message->getAllAttachmentParts() as $attachmentPart) {
            if ($attachmentPart->getFilename() === $expectedFilename) {
                return;
            }
        }
        $this->fail("Filename not found in attachments.");
    }

    /**
     * Test email count equals expected value
     *
     * @author Mike Crowe <drmikecrowe@gmail.com>
     **/
    public function seeEmailCount(int $expected): void
    {
        $messages = $this->messages();
        $count = count($messages);
        $this->assertEquals($expected, $count);
    }

    /**
     * Checks expected count of attachment in last email.
     *
     * @author Marcelo Briones <ing@marcelobriones.com.ar>
     **/
    public function seeEmailAttachmentCount(int $expectedCount): void
    {
        $email = $this->lastMessage();
        $message = Message::from($email->getSource());
        $this->assertEquals($expectedCount, $message->getAttachmentCount());
    }

    // ----------- HELPER METHODS BELOW HERE -----------------------//
    /**
     * Messages
     *
     * Get an array of all the message objects
     *
     * @author Jordan Eldredge <jordaneldredge@gmail.com>
     **/
    protected function messages(): array
    {
        $response = $this->mailcatcher->get('/messages');
        $messages = json_decode($response->getBody(), true);
        // Ensure messages are shown in the order they were recieved
        // https://github.com/sj26/mailcatcher/pull/184
        usort($messages, function ($messageA, $messageB): int {
            $sortKeyA = $messageA['created_at'] . $messageA['id'];
            $sortKeyB = $messageB['created_at'] . $messageB['id'];
            return ($sortKeyA > $sortKeyB) ? -1 : 1;
        });
        return $messages;
    }

    /**
     * @param int|string $id
     */
    protected function emailFromId($id): \Codeception\Util\Email
    {
        $response = $this->mailcatcher->get("/messages/{$id}.json");
        $plainMessage = $this->mailcatcher->get("/messages/{$id}.source");
        $messageData = json_decode($response->getBody(), true);
        $messageData['source'] = $plainMessage->getBody()->getContents();

        return Email::createFromMailcatcherData($messageData);
    }

    protected function seeInEmailSubject(Email $email, string $expected): void
    {
        if(method_exists($this, 'assertStringContainsString')){
            $this->assertStringContainsString($expected, $email->getSubject(), "Email Subject Contains");
        }else{
            $this->assertContains($expected, $email->getSubject(), "Email Subject Contains");
        }
    }

    protected function dontSeeInEmailSubject(Email $email, string $unexpected): void
    {
        if(method_exists($this, 'assertStringContainsString')){
            $this->assertStringNotContainsString($unexpected, $email->getSubject(), "Email Subject Does Not Contain");
        }else{
            $this->assertNotContains($unexpected, $email->getSubject(), "Email Subject Does Not Contain");
        }
    }

    protected function seeInEmail(Email $email, string $expected): void
    {
        if(method_exists($this, 'assertStringContainsString')){
            $this->assertStringContainsString($expected, $email->getSource(), "Email Contains");
        }else{
            $this->assertContains($expected, $email->getSource(), "Email Contains");
        }
    }

    protected function dontSeeInEmail(Email $email, string $unexpected): void
    {
        if(method_exists($this, 'assertStringContainsString')){
            $this->assertStringNotContainsString($unexpected, $email->getSource(), "Email Does Not Contain");
        }else{
            $this->assertNotContains($unexpected, $email->getSource(), "Email Does Not Contain");
        }
    }

    protected function grabMatchesFromEmail(Email $email, string $regex): array
    {
        preg_match($regex, $email->getSource(), $matches);
        $this->assertNotEmpty($matches, "No matches found for $regex");
        return $matches;
    }
}
