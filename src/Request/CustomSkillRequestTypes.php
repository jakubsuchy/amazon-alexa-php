<?php

namespace Alexa\Request;

use Alexa\Request\IntentRequest;
use Alexa\Request\LaunchRequest;
use Alexa\Request\SessionEndedRequest;

/**
 * Class AlexaRequestTypes
 *
 * Encapsulate the valid Alexa request types
 *
 * @package Alexa\Request
 */
abstract class CustomSkillRequestTypes
{
    // Constants

    const TYPE_LAUNCH = 'LaunchRequest';
    const TYPE_INTENT = 'IntentRequest';
    const TYPE_SESSION_ENDED = 'SessionEndedRequest';

    // Fields

    public static $validTypes = [
        self::TYPE_LAUNCH => LaunchRequest::class,
        self::TYPE_INTENT => IntentRequest::class,
        self::TYPE_SESSION_ENDED => SessionEndedRequest::class
    ];
}
