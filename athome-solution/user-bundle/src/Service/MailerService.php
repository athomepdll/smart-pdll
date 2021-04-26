<?php


namespace Athome\UserBundle\Service;

use Athome\UserBundle\Model\UserInterface;
use Swift_Message;

class MailerService implements MailerInterface
{
    /** @var \Twig_Environment */
    protected $twigEnvironment;

    /** @var \Swift_Mailer */
    protected $mailer;

    /** @var string */
    protected $fromEmail;

    /**
     * MailerService constructor.
     * @param \Swift_Mailer $mailer
     * @param \Twig_Environment $twigEnvironment
     * @param string $fromEmail
     */
    public function __construct(
        \Swift_Mailer $mailer,
        \Twig_Environment $twigEnvironment,
        string $fromEmail
    ) {
        $this->mailer = $mailer;
        $this->twigEnvironment = $twigEnvironment;
        $this->fromEmail = $fromEmail;
    }

    /**
     * @param UserInterface $user
     * @return int
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws \Throwable
     */
    public function sendResettingPasswordEmail(UserInterface $user)
    {
        $template = $this->twigEnvironment->load('@AthomeUser/security/resetting_password_email.html.twig');
        $data = [ 'user' => $user ];
        $subject = $template->renderBlock('subject');
        $body = $template->renderBlock('body', $data);
        $recipient = $user->getEmail();

        return $this->sendEmail($subject, $recipient, $body);
    }

    /**
     * @param UserInterface $user
     * @return int
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws \Throwable
     */
    public function sendRegistrationConfirmationEmail(UserInterface $user)
    {
        $template = $this->twigEnvironment->load('@AthomeUser/security/registration_confirmation_email.html.twig');
        $data = [ 'user' => $user ];
        $subject = $template->renderBlock('subject');
        $body = $template->renderBlock('body', $data);
        $recipient = $user->getEmail();

        return $this->sendEmail($subject, $recipient, $body);
    }

    /**
     * @inheritdoc
     */
    protected function sendEmail(
        string $subject,
        string $recipient,
        string $body
    ) {
        $swiftMessage = new Swift_Message();
        $swiftMessage
            ->setFrom($this->fromEmail)
            ->setSubject($subject)
            ->setTo($recipient)
            ->setBody($body, 'text/html')
        ;

        return $this->mailer->send($swiftMessage);
    }
}
