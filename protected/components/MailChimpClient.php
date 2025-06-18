<?php

declare(strict_types=1);

class MailChimpClient implements MailChimpClientInterface
{
    private \DrewM\MailChimp\MailChimp $client;

    public function __construct(string $apiKey)
    {
        $this->client = new \DrewM\MailChimp\MailChimp($apiKey);
    }

    public function success()
    {
        return $this->client->success();
    }

    public function get($method, $args = array(), $timeout = self::TIMEOUT)
    {
        return $this->client->get($method, $args, $timeout);
    }

    public function post($method, $args = array(), $timeout = self::TIMEOUT)
    {
        return $this->client->post($method, $args, $timeout);
    }

    public function delete($method, $args = array(), $timeout = self::TIMEOUT)
    {
        return $this->client->delete($method, $args, $timeout);
    }

    public function subscriberHash(string $email): string
    {
        return $this->client::subscriberHash($email);
    }
}
