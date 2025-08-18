<?php

declare(strict_types=1);

class AdminExternalLinkTest extends \Codeception\Test\Unit
{
    public function testDescriptionValidationFails()
    {
        $externalLink = new ExternalLink();
        $externalLink->dataset_id = 8;
        $externalLink->url = 'required';
        $externalLink->external_link_type_id = 1;
        $externalLink->description = 'JqLwMsZbNxVcTaRfGhYpOjUkIeDqAzWsXcErTfVbNyUiOpLmKhJgFdSeDcRfTgHyJuKiLoPmNzQwErTyUiOpAsDfGhJkLzXcVbNmQwErTyUiOpLpKjHgFdSaZxCvBnMqWeRtYuIoPaSdFgHjKlZxCvBnMqWeRtYuIpOlKjHgFdSaZxCvBnMqWeRtYuIpOlKjHgFdSaZxCvBnMqWeRtYuIpOlKjHgFdSaZxCvBnMqWeRtYuIpOlKjHgFdSaZxCvBnMqWeRtYuIpOlKjHgFdSaZxCvBnMqWeRtYuIpOlKjHgFdSaZxCvBnMqWeRtYuIpOlKjHgFd';

        $this->assertFalse($externalLink->validate());

        $messages = [];
        foreach ($externalLink->getErrors() as $attr => $values) {
            foreach ($values as $value) {
                $messages[] = $value;
            }
        }
        $this->assertEquals('Description is too long (maximum is 200 characters).', implode(',', $messages));
    }

    public function testTagValidationSucceed()
    {
        $externalLink = new ExternalLink();
        $externalLink->dataset_id = 8;
        $externalLink->url = 'required';
        $externalLink->external_link_type_id = 1;
        $externalLink->description = 'JqLwMsZbNxVcTaRfGhYpOjUkIeDq';

        $this->assertTrue($externalLink->validate());

        $messages = [];
        foreach ($externalLink->getErrors() as $attr => $values) {
            foreach ($values as $value) {
                $messages[] = $value;
            }
        }
        $this->assertNotEquals('Description is too long (maximum is 200 characters).', implode(',', $messages));
    }
}
