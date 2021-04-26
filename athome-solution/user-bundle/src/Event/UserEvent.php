<?php


namespace Athome\UserBundle\Event;

use Athome\UserBundle\Model\UserInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserEvent
 * @package Athome\UserBundle\Event
 */
abstract class UserEvent extends Event
{
    /** @var UserInterface */
    protected $user;

    /** @var Request */
    protected $request;

    /** @var Response|null */
    protected $response;

    /**
     * UserEvent constructor.
     * @param UserInterface $user
     * @param Request $request
     */
    public function __construct(
        UserInterface $user,
        Request $request
    ) {
        $this->user = $user;
        $this->request = $request;
    }

    /**
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return null|Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param Response|null $response
     */
    public function setResponse(Response $response = null)
    {
        $this->response = $response;
    }
}
