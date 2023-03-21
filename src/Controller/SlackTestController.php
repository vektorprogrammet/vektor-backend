<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Routing\Annotation\Route;

class SlackTestController extends AbstractController
{
    // public function helloworld(ChatterInterface $chatter)
    // {

    // }
    
    public function slackTest(ChatterInterface $chatter)
    {
        // helloworld();
        $message = (new ChatMessage('Hello world!'))
        ->transport('slack');

        $sentMessage = $chatter->send($message);
        // når person besøker /slack
        // så vil denne funksjonen kjøre

        return $this->render('slack.html.twig');
    }
    // tom side /slack-notifier
    // der NÅR noen besøker
    // så sender en notifiaction Slack

}
