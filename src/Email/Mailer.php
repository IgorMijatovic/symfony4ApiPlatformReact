<?php
namespace App\Email;


use App\Entity\User;
use Swift_Message;

class Mailer
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function __construct(
        \Swift_Mailer $mailer,
        \Twig_Environment $twig
    )
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function sendConfirmationEmail(User $user)
    {
        $body = $this->twig->render(
            'email/confirmation.html.twig',
            [
                'user' => $user
            ]
        );

        $message= (new Swift_Message('Hallo from api platform'))
            ->setFrom('api-platform@gmail.com')
            ->setTo($user->getEmail())
            ->setBody($body);

        $this->mailer->send($message);
    }
}