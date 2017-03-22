<?php

namespace Alexa\Request;

use Symfony\Component\Validator\Constraints as Assert;

use Alexa\Utility\Purifier\HasPurifier;

/**
 * Class Application
 *
 * Represents an Alexa application
 *
 * @package Alexa\Request
 */
class Application
{
    // Constants

    const ERROR_APPLICATION_ID_NOT_STRING = 'The provided value for the Alexa application ID was not a string';
    const ERROR_APPLICATION_ID_NOT_MATCHED = 'The application ID \'%s\' found in the request does not match ' .
        'any of the expected application IDs.';

    // Traits

    use HasPurifier;

    // Fields

    /**
     * @var string
     *
     * @Assert\Type("string")
     * @Assert\NotBlank
     */
    private $applicationId;


    // Hooks

    /**
     * Application constructor.
     *
     * @param string $applicationId
     * @param \HTMLPurifier $purifier
     */
    public function __construct($applicationId, \HTMLPurifier $purifier)
    {
        // Set purifier
        $this->setPurifier($purifier);

        // Set application IDs
        $this->setApplicationId($applicationId);
    }

    // Public Methods

    /**
     * validateApplicationId()
     *
     * Confirms the application ID from the request is one in the list provided as valid
     *
     * @param array[string] $validApplicationIds
     *
     * @throws \InvalidArgumentException
     */
    public function validateApplicationId(array $validApplicationIds)
    {
        if (!in_array($this->getApplicationId(), $validApplicationIds)) {
            throw new \InvalidArgumentException(
                sprintf(self::ERROR_APPLICATION_ID_NOT_MATCHED, $this->getApplicationId())
            );
        }
    }

    // Accessors

    /**
     * @return string
     */
    public function getApplicationId()
    {
        return $this->applicationId;
    }

    // Mutators

    /**
     * @param string $applicationId
     */
    protected function setApplicationId($applicationId)
    {
        if (!is_string($applicationId)) {
            throw new \InvalidArgumentException(self::ERROR_APPLICATION_ID_NOT_STRING);
        }

        $this->applicationId = $applicationId;
    }
}
