<?php

namespace App\Services;

final class EmailService
{
    public string $receiver;
    public string $sender;
    public string $subject;
    public string $body;

    final public function __construct()
    {
        $this->receiver = "";
        $this->sender = "";
        $this->subject = "";
        $this->body = "";
    }

    final public function withReceiver(string $receiver): self
    {
        $this->receiver = $receiver;

        return $this;
    }

    final public function withSender(string $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    final public function withSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    final public function withBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    final public function send(): bool
    {
        return true;
    }
}
