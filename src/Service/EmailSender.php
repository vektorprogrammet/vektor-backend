<?php

namespace App\Service;

use App\Entity\AdmissionSubscriber;
use App\Entity\Receipt;
use App\Entity\SupportTicket;
use App\Mailer\MailingInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class EmailSender
{
    /**
     * EmailSender constructor.
     */
    public function __construct(
        private readonly MailingInterface $mailer,
        private readonly Environment $twig,
        private readonly RouterInterface $router,
        private readonly string $defaultEmail,
        private readonly string $economyEmail
    ) {
    }

    public function sendSupportTicketToDepartment(SupportTicket $supportTicket)
    {
        $message = (new \Swift_Message())
            ->setSubject('Nytt kontaktskjema')
            ->setFrom($this->defaultEmail)
            ->setReplyTo($supportTicket->getEmail())
            ->setTo($supportTicket->getDepartment()->getEmail())
            ->setBody($this->twig->render('admission/contactEmail.txt.twig', ['contact' => $supportTicket]));
        $this->mailer->send($message);
    }

    public function sendSupportTicketReceipt(SupportTicket $supportTicket)
    {
        $receipt = (new \Swift_Message())
            ->setSubject('Kvittering for kontaktskjema')
            ->setFrom($this->defaultEmail)
            ->setReplyTo($supportTicket->getDepartment()->getEmail())
            ->setTo($supportTicket->getEmail())
            ->setBody($this->twig->render('admission/receiptEmail.txt.twig', ['contact' => $supportTicket]));
        $this->mailer->send($receipt);
    }

    public function sendPaidReceiptConfirmation(Receipt $receipt)
    {
        $message = (new \Swift_Message())
            ->setSubject('Vi har tilbakebetalt penger for utlegget ditt')
            ->setFrom($this->economyEmail)
            ->setFrom([$this->economyEmail => 'Økonomi - Vektorprogrammet'])
            ->setTo($receipt->getUser()->getEmail())
            ->setBody($this->twig->render('receipt/confirmation_email.txt.twig', [
                'name' => $receipt->getUser()->getFullName(),
                'account_number' => $receipt->getUser()->getAccountNumber(),
                'receipt' => $receipt, ]));

        $this->mailer->send($message);
    }

    public function sendRejectedReceiptConfirmation(Receipt $receipt)
    {
        $message = (new \Swift_Message())
                                 ->setSubject('Refusjon for utlegget ditt har blitt avvist')
                                 ->setFrom([$this->economyEmail => 'Økonomi - Vektorprogrammet'])
                                 ->setReplyTo($this->economyEmail)
                                 ->setTo($receipt->getUser()->getEmail())
                                 ->setBody($this->twig->render('receipt/rejected_email.txt.twig', [
                                     'name' => $receipt->getUser()->getFullName(),
                                     'receipt' => $receipt, ]));

        $this->mailer->send($message);
    }

    public function sendReceiptCreatedNotification(Receipt $receipt)
    {
        $message = (new \Swift_Message())
                                 ->setSubject('Nytt utlegg fra ' . $receipt->getUser())
                                 ->setFrom('vektorbot@vektorprogrammet.no')
                                 ->setTo($this->economyEmail)
                                 ->setBody($this->twig->render('receipt/created_email.html.twig', [
                                      'url' => $this->router->generate('receipts_show_individual', ['user' => $receipt->getUser()->getId()]),
                                     'name' => $receipt->getUser()->getFullName(),
                                     'accountNumber' => $receipt->getUser()->getAccountNumber(),
                                     'receipt' => $receipt, ]), 'text/html')
                                 ->setContentType('text/html');

        $this->mailer->send($message);
    }

    public function sendAdmissionStartedNotification(AdmissionSubscriber $subscriber)
    {
        $message = (new \Swift_Message())
             ->setSubject('Opptak for vektorassistenter har åpnet!')
             ->setFrom($this->defaultEmail)
             ->setTo($subscriber->getEmail())
             ->setBody($this->twig->render('admission/notification_email.html.twig', [
                 'department' => $subscriber->getDepartment(),
                 'infoMeeting' => $subscriber->getDepartment()->getCurrentAdmissionPeriod()->getInfoMeeting(),
                 'subscriber' => $subscriber,
             ]))
             ->setContentType('text/html');

        $this->mailer->send($message, true);
    }

    public function sendInfoMeetingNotification(AdmissionSubscriber $subscriber)
    {
        $message = (new \Swift_Message())
            ->setSubject('Infomøte i dag!')
            ->setFrom($this->defaultEmail)
            ->setTo($subscriber->getEmail())
            ->setBody($this->twig->render('admission/info_meeting_email.html.twig', [
                'department' => $subscriber->getDepartment(),
                'infoMeeting' => $subscriber->getDepartment()->getCurrentAdmissionPeriod()->getInfoMeeting(),
                'subscriber' => $subscriber,
            ]))
            ->setContentType('text/html');
        $this->mailer->send($message, true);
    }
}
