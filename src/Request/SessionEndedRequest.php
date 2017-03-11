<?php

namespace Alexa\Request;

use Alexa\Utility\PurifierHelper;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class SessionEndedRequest
 * @package Alexa\Request
 */
class SessionEndedRequest extends Request implements RequestInterface
{
    // Traits

    use PurifierHelper;

    // Fields


    /**
     * @var \HTMLPurifier
     */
    protected $purifier;
    /**
     * @var string
     *
     * @Assert\Type("string")
     * @Assert\NotBlank
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
     * @param \HTMLPurifier|null $purifier
     */
    public function __construct(
        $rawData,
        $applicationId,
        Certificate $certificate = null,
        Application $application = null,
        \HTMLPurifier $purifier = null
    ) {
        // Parent construct
        parent::__construct($rawData, $applicationId, $certificate, $application, $purifier);

        // Set reason
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
        $this->reason = $reason ? $this->purifier->purify((string)$reason) : null;
    }
}
