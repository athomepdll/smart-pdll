<?php


namespace Athome\UserBundle;


class Events
{
    const REGISTRATION_SUCCESSFUL = 'user_bundle.register.success';
    const REGISTRATION_CONFIRMED = 'user_bundle.register.confirmed';
    const PASSWORD_REQUEST_SUCCESSFUL = 'user_bundle.password_request.success';
    const PASSWORD_RESET_SUCCESSFUL = 'user_bundle.password_reset.success';
    const ACCOUNT_UPDATE_SUCCESSFUL = 'user_bundle.account_update.success';
}
