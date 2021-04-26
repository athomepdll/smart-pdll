AthomeUserBundle allows you to add simple user / authentication process to your Symfony 4 application.
The bundle provides : 
- an authentication system (form login) + logout
- a registration process (with a confirmation process by email or not)
- a forgot password process (by sending an email)
- a "edit account" feature

Getting Started With AthomeUserBundle
==================================

### Configuration
in config/packages/athome_user.yaml :

```yaml
athome_user:
  user_class: App\Entity\User
  from_email: 'no-reply@email.com'
  enable_on_registration: false #not mandatory : default to true
```

in config/routes.yaml
```yaml
app_security:
  resource: '@AthomeUserBundle/Resources/config/routing/security.yaml'
```

in config/packages/security.yaml :

You can use the bundle raw authenticator or inherit it.
```yaml
firewalls:
    main:
        guard:
            authenticators: [ Athome\UserBundle\Security\Authenticator ]
        logout:
            path: user_bundle_security_logout
            target: user_bundle_security_login
```

### Extend the user model
```php
<?php

namespace App\Entity;

use Athome\UserBundle\Model\User as BaseUser;

class User extends BaseUser
{
    public $property;
    
    public $anotherProperty;
    
    // ...
```

### Overriding the bundle
In order to override any part of the bundle, you can extend the base class or use service decoration

ex: (src/Security/Authenticator.php)
```php
<?php


namespace App\Security;

use Athome\UserBundle\Security\Authenticator as BaseAuthenticator;

class Authenticator extends BaseAuthenticator
{
    // Inherit functions as needed
}
```

ex: (config/services.yaml)
```yaml
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

```php
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

#### List of available events:
REGISTRATION_SUCCESSFUL : Fired when registration is completed

REGISTRATION_CONFIRMED : Fired when account is activated

PASSWORD_REQUEST_SUCCESSFUL : Fired when reset password email has been sent

PASSWORD_RESET_SUCCESSFUL : Fired when password has been reset

ACCOUNT_UPDATE_SUCCESSFUL : Fired when account has been updated

