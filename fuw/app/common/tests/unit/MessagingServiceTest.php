<?php

declare(strict_types=1);

namespace common\tests;

use \yii\mail\Mailer;
use \yii\mail\Message;
use common\components\MessagingService;


class MessagingServiceTest extends \Codeception\Test\Unit
{

    /**
     * test MessagingService can send email
     */
    public function testSendEmail()
    {

        $from = "admin@gigadb.org";
        $to = "user@gigadb.org";
        $subject = "Uploading instructions";
        $content = "Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo";

        $mockMailer = $this->getMockBuilder(Mailer::class)
                    ->setMethods(['compose'])
                    ->getMock();

        $mockMessage = $this->getMockBuilder(Message::class)
                    ->setMethods(['setFrom','setTo','setSubject','setTextBody','send'])
                    ->getMock();


        $mockMailer->expects($this->once())
                    ->method('compose')
                    ->willReturn($mockMessage);

        $mockMessage->expects($this->once())
                    ->method('setFrom')
                    ->with((array)$from)
                    ->willReturn($mockMessage);

        $mockMessage->expects($this->once())
                    ->method('setTo')
                    ->with((array)$to)
                    ->willReturn($mockMessage);

        $mockMessage->expects($this->once())
                    ->method('setSubject')
                    ->with((array)$subject)
                    ->willReturn($mockMessage);


        $mockMessage->expects($this->once())
                    ->method('setTextBody')
                    ->with((array)$content)
                    ->willReturn($mockMessage);

        $mockMessage->expects($this->once())
                    ->method('send')
                    ->willReturn(true);

        $msgSrv = new MessagingService($mockMailer);
        $result = $msgSrv->sendEmailMessage($from, $to, $subject, $content);
        $this->assertTrue($result);
    }


}
