AthomeUserBundle allows you to add simple user / authentication process to your Symfony 4 application.
The bundle provides : 
- an authentication system (form login) + logout
- a registration process
- a forgot password process (by sending an email)
- a "edit account" feature

Getting Started With AthomeUserBundle
==================================

### Configuration
in config/packages/athome_user.yaml :

```
athome_user:
  user_class: App\Entity\User
```

in config/routes.yaml
```
app_security:
  resource: '@AthomeUserBundle/Resources/config/routing/security.yaml'
```

in config/packages/security.yaml :

You can use the bundle raw authenticator or inherit it.
```
firewalls:
    main:
        guard:
            authenticators: [ Athome\UserBundle\Security\AppAuthenticator ]
        logout:
            path: user_bundle_security_logout
            target: user_bundle_security_login
```
example of inheritance (src/Security/Authenticator.php)
```
<?php


namespace App\Security;

use Athome\UserBundle\Security\Authenticator as BaseAuthenticator;

class Authenticator extends BaseAuthenticator
{
    // Inherit as many functions as needed
}
```

### Overriding the bundle
In order to override any part of the bundle, you can extend the base class or use service decoration

ex: (config/services.yaml)
```
App\Form\RegisterType:
    decorates: Athome\UserBundle\Form\Type\RegisterType
```

Overriding templates :

Just follow the same directory structure


ex: to override register, create template in templates/bundles/AthomeUserBundle/security/register.html.twig


### Events
With every action come an event which carries an object, the request and the response.

For example, if you want to keep the bundle default behavior for register, but you want to add business logic :

1. Create a subscriber (src/EventSubscriber/RegisterSubscriber.php)
Redirect after register
use RegisterEvent->setResponse()

```
    public static function getSubscribedEvents()
    {
        return [
            UserEvents::REGISTRATION_SUCCESSFUL => 'onRegistrationSuccessful'
        ];
    }

    /**
     * @param UserEvent $event
     */
    public function onRegistrationSuccessful(UserEvent $event)
    {
        $user = $event->getUser();

        // Authenticate user
        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $this->tokenStorage->setToken($token);
        $this->session->set('_security_main', serialize($token));
        
        // Redirect user 
        $event->setResponse(new RedirectResponse($this->router->generate('homepage')));
    }
```

