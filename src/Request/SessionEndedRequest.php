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
    protected $reason;

    // Hooks

    /**
     * SessionEndedRequest()
     *
     * @param string $rawData - The original JSON response, before json_decode
     * @param string $applicationId - Your Alexa Dev Portal application ID
     * @param Certificate|null $certificate - Override the auto-generated Certificate with your own
     * @param Application|null $application - Override the auto-generated Application with your own
     */
    public function __construct(
        $rawData,
        $applicationId,
        Certificate $certificate = null,
        Application $application = null
    ) {
        // Parent construct
        parent::__construct($rawData, $applicationId, $certificate, $application);

        $this->setReason($this->data['request']['reason']);
    }

    // Accessors

    /**
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    // Mutators

    /**
     * @param string $reason
     */
    public function setReason($reason)
    {
        $this->reason = $reason;
    }
}
