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

    const ERROR_APPLICATION_ID_NOT_MATCHED = 'The application ID \'%\' found in the request does not match ' .
        'any of the expected application IDs.';

    // Traits

    use HasPurifier;

    // Fields

    /**
     * @var array[string]
     *
     * @Assert\Type("array")
     * @Assert\NotBlank
     */
    private $expectedApplicationIds;


    // Hooks

    /**
     * Application constructor.
     *
     * @param $expectedApplicationIds
     * @param \HTMLPurifier $purifier
     */
    public function __construct(array $expectedApplicationIds, \HTMLPurifier $purifier)
    {
        // Set purifier
        $this->setPurifier($purifier);

        // Set application IDs
        $this->setExpectedApplicationIds($expectedApplicationIds);
    }

    // Public Methods

    /**
     * validateApplicationId()
     *
     * Confirms the provided application ID is one of the list provided as valid
     *
     * @param $requestApplicationId
     *
     * @throws \InvalidArgumentException
     */
    public function validateApplicationId($requestApplicationId)
    {
        if (!in_array($requestApplicationId, $this->getExpectedApplicationIds())) {
            throw new \InvalidArgumentException(self::ERROR_APPLICATION_ID_NOT_MATCHED);
        }
    }

    // Accessors

    /**
     * @return array
     */
    public function getExpectedApplicationIds()
    {
        return $this->expectedApplicationIds;
    }

    // Mutators

    /**
     * @param array $expectedApplicationIds
     */
    protected function setExpectedApplicationIds(array $expectedApplicationIds)
    {
        $this->expectedApplicationIds = $expectedApplicationIds;
    }
}
