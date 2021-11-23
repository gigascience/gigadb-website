<?php

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
                    // ->disableOriginalConstructor()
                    ->getMock();

        $mockMessage = $this->getMockBuilder(Message::class)
                    ->setMethods(['setFrom','setTo','setSubject','setTextBody','send'])
                    // ->disableOriginalConstructor()
                    ->getMock();


        $mockMailer->expects($this->once())
                    ->method('compose')
                    ->willReturn($mockMessage);

        $mockMessage->expects($this->once())
                    ->method('setFrom')
                    ->with($from)
                    ->willReturn($mockMessage);

        $mockMessage->expects($this->once())
                    ->method('setTo')
                    ->with($to)
                    ->willReturn($mockMessage);

        $mockMessage->expects($this->once())
                    ->method('setSubject')
                    ->with($subject)
                    ->willReturn($mockMessage);


        $mockMessage->expects($this->once())
                    ->method('setTextBody')
                    ->with($content)
                    ->willReturn($mockMessage);

        $mockMessage->expects($this->once())
                    ->method('send')
                    ->willReturn(true);

        $msgSrv = new MessagingService($mockMailer);
        $result = $msgSrv->sendEmailMessage($from, $to, $subject, $content);
        $this->assertTrue($result);
    }


}