<?php
class Message
{
    private $user_name;
    private $user_email;
    private $main;
    private $created_at;

    public function __construct(string $user_name, string $user_email, string $main, string $created_at)
    {
        $this->user_name = $user_name;
        $this->user_email = $user_email;
        $this->main = $main;
        $this->created_at = $created_at;
    }

    public function get_user_name(): string
    {
        return $this->user_name;
    }

    public function get_user_email(): string
    {
        return $this->user_email;
    }

    public function get_main(): string
    {
        return $this->main;
    }

    public function get_created_at(): string
    {
        return $this->created_at;
    }
}