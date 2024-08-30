<?php
namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendApprovalEmail(string $toEmail, string $feedbackName)
    {
        $email = (new Email())
            ->from('your-email@example.com')
            ->to($toEmail)
            ->subject('Feedback Approved')
            ->text("Dear {$feedbackName},\n\nYour feedback has been approved. Thank you for your input!")
            ->html("<p>Dear {$feedbackName},</p><p>Your feedback has been approved. Thank you for your feedback!</p>");

        $this->mailer->send($email);
    }
}