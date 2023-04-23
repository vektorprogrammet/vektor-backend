<?php

namespace App\Service;

use App\Entity\AdmissionSubscriber;
use App\Entity\Receipt;
use App\Entity\SupportTicket;
use App\Mailer\MailingInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class EmailSender
{
    public function __construct(
        private readonly MailingInterface $mailer,
        private readonly Environment $twig,
        private readonly RouterInterface $router,
        private readonly string $defaultEmail,
        private readonly string $economyEmail
    ) {
    }

    public function sendSupportTicketToDepartment(SupportTicket $supportTicket): void
    {
        $message = (new TemplatedEmail())
            ->subject('Nytt kontaktskjema')
            ->from($this->defaultEmail)
            ->replyTo($supportTicket->getEmail())
            ->to($supportTicket->getDepartment()->getEmail())
            ->htmlTemplate('admission/contactEmail.txt.twig')
            ->context(['contact' => $supportTicket]);
        $this->mailer->send($message);
    }

    public function sendSupportTicketReceipt(SupportTicket $supportTicket): void
    {
        $receipt = (new TemplatedEmail())
            ->subject('Kvittering for kontaktskjema')
            ->from($this->defaultEmail)
            ->replyTo($supportTicket->getDepartment()->getEmail())
            ->to($supportTicket->getEmail())
            ->htmlTemplate('admission/receiptEmail.txt.twig')
            ->context(['contact' => $supportTicket]);
        $this->mailer->send($receipt);
    }

    public function sendPaidReceiptConfirmation(Receipt $receipt): void
    {
        $message = (new TemplatedEmail())
            ->subject('Vi har tilbakebetalt penger for utlegget ditt')
            ->from(new Address($this->economyEmail, 'Økonomi - Vektorprogrammet'))
            ->to($receipt->getUser()->getEmail())
            ->htmlTemplate('receipt/confirmation_email.txt.twig')
            ->context([
                'name' => $receipt->getUser()->getFullName(),
                'account_number' => $receipt->getUser()->getAccountNumber(),
                'receipt' => $receipt]);

        $this->mailer->send($message);
    }

    public function sendRejectedReceiptConfirmation(Receipt $receipt): void
    {
        $message = (new TemplatedEmail())
            ->subject('Refusjon for utlegget ditt har blitt avvist')
            ->from(new Address($this->economyEmail, 'Økonomi - Vektorprogrammet'))
            ->replyTo($this->economyEmail)
            ->to($receipt->getUser()->getEmail())
            ->htmlTemplate('receipt/rejected_email.txt.twig')
            ->context([
                'name' => $receipt->getUser()->getFullName(),
                'receipt' => $receipt]);

        $this->mailer->send($message);
    }

    public function sendReceiptCreatedNotification(Receipt $receipt): void
    {
        $message = (new TemplatedEmail())
            ->subject('Nytt utlegg fra ' . $receipt->getUser())
            ->from('vektorbot@vektorprogrammet.no')
            ->to($this->economyEmail)
            ->htmlTemplate('receipt/created_email.html.twig')
            ->context([
                'url' => $this->router->generate('receipts_show_individual', ['user' => $receipt->getUser()->getId()]),
                'name' => $receipt->getUser()->getFullName(),
                'accountNumber' => $receipt->getUser()->getAccountNumber(),
                'receipt' => $receipt,
            ]);

        $this->mailer->send($message);
    }

    public function sendAdmissionStartedNotification(AdmissionSubscriber $subscriber): void
    {
        $message = (new TemplatedEmail())
            ->subject('Opptak for vektorassistenter har åpnet!')
            ->from($this->defaultEmail)
            ->to($subscriber->getEmail())
            ->htmlTemplate('admission/notification_email.html.twig')
            ->context([
                'department' => $subscriber->getDepartment(),
                'infoMeeting' => $subscriber->getDepartment()->getCurrentAdmissionPeriod()->getInfoMeeting(),
                'subscriber' => $subscriber,
            ]);

        $this->mailer->send($message, true);
    }

    public function sendInfoMeetingNotification(AdmissionSubscriber $subscriber): void
    {
        $message = (new TemplatedEmail())
            ->subject('Infomøte i dag!')
            ->from($this->defaultEmail)
            ->to($subscriber->getEmail())
            ->htmlTemplate('admission/info_meeting_email.html.twig')
            ->context([
                'department' => $subscriber->getDepartment(),
                'infoMeeting' => $subscriber->getDepartment()->getCurrentAdmissionPeriod()->getInfoMeeting(),
                'subscriber' => $subscriber,
            ]);
        $this->mailer->send($message, true);
    }
}
