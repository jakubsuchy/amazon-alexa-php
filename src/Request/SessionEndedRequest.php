<?php

namespace Alexa\Request;

/**
 * Class SessionEndedRequest
 * @package Alexa\Request
 */
class SessionEndedRequest extends Request implements RequestInterface
{
    // Fields

    /**
     * @var string
     */
    public $reason;

    // Hooks

    /**
     * SessionEndedRequest()
     *
     * @param string $rawData - The original JSON response, before json_decode
     * @param string $applicationId - Your Alexa Dev Portal application ID
     * @param Certificate $certificate = null - Override the auto-generated Certificate with your own
     * @param Application $application = null - Override the auto-generated Application with your own
     */
    public function __construct($rawData, $applicationId, $certificate = null, $application = null)
    {
        // Parent construct
        parent::__construct($rawData, $applicationId, $certificate, $application);

        $this->reason = $this->data['request']['reason'];
    }
}
