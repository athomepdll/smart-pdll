<?php


namespace App\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class AdminVoter
 * @package App\Security
 */
class AdminVoter extends Voter
{
    public const ATTRIBUTE = 'admin_panel';

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * ExportExpertVoter constructor.
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed $subject The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool True if the attribute and subject are supported, false otherwise
     */
    protected function supports($attribute, $subject)
    {
        if ($subject !== self::ATTRIBUTE) {
            return false;
        }

        return true;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
     *
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if (!$this->authorizationChecker->isGranted('ROLE_ADMIN', $token)) {
            return false;
        }

        return true;
    }
}
