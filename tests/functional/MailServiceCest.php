<?php

declare(strict_types=1);

namespace functional;

use FunctionalTester;
use Yii;

class MailServiceCest
{
    /** @var string $emlDir */
    protected $emlDir;

    public function _before(FunctionalTester $I) {
        $this->emlDir = Yii::$app->mailer->fileTransportPath;
        if (!is_dir($this->emlDir)) {
            mkdir($this->emlDir, 0777, true);
        }
    }

    public function _after(FunctionalTester $I) {
        $files = array_diff(scandir($this->emlDir), ['.', '..']);
        foreach ($files as $file) {
            $path = $this->emlDir . DIRECTORY_SEPARATOR . $file;
            if (is_file($path)) {
                unlink($path);
            }
        }
    }

    public function sendEmailUsingSwiftmailer(FunctionalTester $I) {
        $from = 'admin@gigadb.org';
        $to = 'user@gigadb.org';
        $subject = 'Uploading instructions';
        $body = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo';

        Yii::$app->mailer->compose()
                         ->setFrom($from)
                         ->setTo($to)
                         ->setSubject($subject)
                         ->setTextBody($body)
                         ->send();

        $msgPath = $this->getLastMessage();
        $parser = new \PhpMimeMailParser\Parser();
        $parser->setPath($msgPath);

        $I->assertEquals($to, $parser->getHeader('to'), 'Recipient mismatch');
        $I->assertEquals($from, $parser->getHeader('from'), 'Sender mismatch');
        $I->assertEquals($subject, $parser->getHeader('subject'), 'Subject mismatch');
    }

    public function sendPlainTextUsingMailService(FunctionalTester $I) {
        $from = 'foo@bar.com';
        $to = 'hello@world.com';
        $subject = 'Testing';
        $body = 'lorem ipsum';

        $result = Yii::app()->mailService->sendEmail($from, $to, $subject, $body);
        $I->assertTrue($result, 'MailService::sendEmail should return true');

        $msgPath = $this->getLastMessage();
        $parser = new \PhpMimeMailParser\Parser();
        $parser->setPath($msgPath);

        $I->assertEquals($to, $parser->getHeader('to'), 'Recipient mismatch');
        $I->assertEquals($from, $parser->getHeader('from'), 'Sender mismatch');
        $I->assertEquals($subject, $parser->getHeader('subject'), 'Subject mismatch');
    }

    public function sendHtmlUsingMailService(FunctionalTester $I) {
        $from = 'foo@xyzzy.com';
        $to = 'xyzzy@world.com';
        $subject = 'Test HTML message';
        $body = '<h1>Hello World</h1>';

        $result = Yii::app()->mailService->sendHTMLEmail($from, $to, $subject, $body);
        $I->assertTrue($result, 'MailService::sendHTMLEmail should return true');

        $msgPath = $this->getLastMessage();
        $parser = new \PhpMimeMailParser\Parser();
        $parser->setPath($msgPath);
        $attachments = $parser->getAttachments();

        $I->assertEquals($to, $parser->getHeader('to'));
        $I->assertEquals($from, $parser->getHeader('from'));
        $I->assertEquals($subject, $parser->getHeader('subject'));

        $I->assertCount(3, $attachments, 'Should have 3 attachments');
        $I->assertEquals('top.png', $attachments[0]->getFilename());
        $I->assertEquals('bottom.png', $attachments[1]->getFilename());
        $I->assertEquals('logo.png', $attachments[2]->getFilename());
    }

    public function sendHtmlWithAttachmentUsingMailService(FunctionalTester $I) {
        $from = 'foo@xyzzy.com';
        $to = 'xyzzy@world.com';
        $subject = 'Test HTML with Attachment';
        $body = '<h1>Hello World</h1>';
        $filePath = '/var/www/images/new_interface_image/frog.jpg';
        $filename = 'frog.jpg';

        $result = Yii::app()->mailService->sendHTMLEmailWithAttachment(
            $from, $to, $subject, $body, $filePath, $filename
        );
        $I->assertTrue($result, 'MailService::sendHTMLEmailWithAttachment should return true');

        $msgPath = $this->getLastMessage();
        $parser = new \PhpMimeMailParser\Parser();
        $parser->setPath($msgPath);
        $attachments = $parser->getAttachments();

        $I->assertEquals($to, $parser->getHeader('to'));
        $I->assertEquals($from, $parser->getHeader('from'));
        $I->assertEquals($subject, $parser->getHeader('subject'));

        $I->assertCount(4, $attachments, 'Should have 4 attachments');
        $I->assertEquals('frog.jpg', $attachments[3]->getFilename(), 'Missing frog.jpg');
    }

    private function getLastMessage() {
        $messages = array_diff(scandir($this->emlDir), ['.', '..']);
        if (empty($messages)) {
            throw new \Exception('No messages found in eml directory');
        }

        return $this->emlDir . DIRECTORY_SEPARATOR . end($messages);
    }
}
