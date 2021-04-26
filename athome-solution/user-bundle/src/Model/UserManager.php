<?php


namespace Athome\UserBundle\Model;

use Athome\UserBundle\Service\MailerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class UserManager implements UserManagerInterface
{
    /** @var ObjectManager */
    protected $objectManager;

    /** @var UserPasswordEncoderInterface */
    protected $userPasswordEncoder;

    /** @var TokenGeneratorInterface */
    protected $tokenGenerator;

    /** @var MailerInterface */
    protected $mailer;

    /** @var string */
    protected $class;

    /**
     * UserManager constructor.
     * @param ObjectManager $objectManager
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @param TokenGeneratorInterface $tokenGenerator
     * @param MailerInterface $mailer
     * @param string $class
     */
    public function __construct(
        ObjectManager $objectManager,
        UserPasswordEncoderInterface $userPasswordEncoder,
        TokenGeneratorInterface $tokenGenerator,
        MailerInterface $mailer,
        string $class
    ) {
        $this->objectManager = $objectManager;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->tokenGenerator = $tokenGenerator;
        $this->mailer = $mailer;
        $this->class = $class;
    }

    /**
     * @inheritdoc
     */
    public function create()
    {
        $class = $this->getClass();

        return new $class;
    }

    /**
     * @inheritdoc
     */
    public function update(UserInterface $user, $andFlush = true)
    {
        $this->objectManager->persist($user);

        if ($andFlush === true) {
            $this->objectManager->flush();
        }
    }

    /**
     * @inheritdoc
     */
    public function updatePassword(UserInterface $user)
    {
        $encodedPassword = $this->userPasswordEncoder->encodePassword($user, $user->getPlainPassword());
        $user
            ->setPassword($encodedPassword)
            ->eraseCredentials()
            ->resetConfirmationToken()
        ;

        return $user;
    }

    /**
     * @inheritdoc
     */
    public function getClass()
    {
        if (false !== strpos($this->class, ':')) {
            $metadata = $this->objectManager->getClassMetadata($this->class);
            $this->class = $metadata->getName();
        }

        return $this->class;
    }

    /**
     * @inheritdoc
     */
    public function findUserBy(array $criteria)
    {
        return $this->objectManager->getRepository($this->getClass())->findOneBy($criteria);
    }

    /**
     * @inheritdoc
     */
    public function sendResettingPasswordEmail(UserInterface $user)
    {
        // TODO : throw token exception or mail failure exception
        $token = $this->tokenGenerator->generateToken();
        $user->setConfirmationToken($token);
        $user->setPasswordRequestedAt(new \DateTime());

        // TODO : Check if token already exists and not expired (prevent flood)
        $result = $this->mailer->sendResettingPasswordEmail($user);

        if ($result === 0) {
            return false;
        }

        $this->update($user);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function enableUser(UserInterface $user)
    {
        $user->setEnabled(true);
        $user->resetConfirmationToken();

        return $this->update($user);
    }

    /**
     * @inheritdoc
     */
    public function generatePassword(int $length = 8)
    {
        $characterRange = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';

        $count = mb_strlen($characterRange);

        for ($i = 0, $password = ''; $i < $length; $i++) {
            $index = rand(0, $count - 1);
            $password .= mb_substr($characterRange, $index, 1);
        }

        return $password;
    }

    /**
     * @inheritdoc
     */
    public function lockUser(UserInterface $user)
    {
        $user->setLocked(true);

        return $this->update($user);
    }

    /**
     * @inheritdoc
     */
    public function sendRegistrationConfirmationEmail(UserInterface $user)
    {
        // TODO : throw token exception or mail failure exception
        $token = $this->tokenGenerator->generateToken();
        $user->setConfirmationToken($token);

        // TODO : Check if token already exists and not expired (prevent flood)
        $result = $this->mailer->sendRegistrationConfirmationEmail($user);

        if ($result === 0) {
            return false;
        }

        $this->update($user);

        return true;
    }
}
