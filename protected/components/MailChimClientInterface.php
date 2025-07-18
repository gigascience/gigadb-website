<?php

declare(strict_types=1);

interface MailChimpClientInterface
{
    const TIMEOUT = 10;

    public function success();
    public function get($method, $args = array(), $timeout = self::TIMEOUT);
    public function post($method, $args = array(), $timeout = self::TIMEOUT);
    public function delete($method, $args = array(), $timeout = self::TIMEOUT);
    public function subscriberHash(string $email): string;
}
