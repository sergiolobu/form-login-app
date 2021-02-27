<?php


namespace App\MessageHandler;


use App\Message\RecoverPasswordEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mime\Email;

class RecoverPasswordEmailHandler implements MessageHandlerInterface
{
    /**
     * @var MailerInterface
     */
    private $mailer;


    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function __invoke(RecoverPasswordEmail $recoverPasswordEmail)
    {
        $data = $recoverPasswordEmail->getData();

        $email = (new Email())
            ->from('mailer@example.com')
            ->to($data['email'])
            ->subject('Your pass')
            ->text('Your pass')
            ->html(sprintf('<h1>user: %s</h1><br><h1>password: %s</h1>', $data['email'],$data['password']));

        $this->mailer->send($email);
    }
}