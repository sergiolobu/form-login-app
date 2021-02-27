<?php


namespace App\Message;


class RecoverPasswordEmail
{
    /**
     * @var array
     */
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getData(): array
    {
       return $this->data;
    }
}