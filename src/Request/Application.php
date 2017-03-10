<?php
/**
 * @file Application.php
 *
 * The application abstraction layer to provide Application ID validation to
 * Alexa requests. Any implementations might provide their own implementations
 * via the $request->setApplicationAbstraction() function but must provide the
 * validateApplicationId() function.
 */

namespace Alexa\Request;

use Symfony\Component\Validator\Constraints as Assert;

use InvalidArgumentException;

/**
 * Class Application
 *
 * Encapsulate the Alexa application information
 *
 * @package Alexa\Request
 */
class Application
{
    // Constants

    const ERROR_APPLICATION_ID_NOT_STRING = 'The provided application ID value was not a string';
    const ERROR_APPLICATION_ID_NOT_MATCHED = 'Application ID not matched';
    // Fields

    /**
     * @var array[string]
     *
     * @Assert\Type("array")
     * @Assert\NotBlank
     */
    protected $applicationIdArray;


    // Hooks

    /**
     * Application constructor.
     *
     * @param $applicationId
     */
    public function __construct($applicationId)
    {
        if (!is_string($applicationId)) {
            throw new \InvalidArgumentException(self::ERROR_APPLICATION_ID_NOT_STRING);
        }

        $this->setApplicationIdArray(preg_split('/,/', $applicationId));
    }

    // Public Methods

    /**
     * Validate that the request Application ID matches our Application. This is required as per Amazon requirements.
     *
     * @param string $requestApplicationId - Application ID from the Request
     *                               (typically found in $data['session']['application']
     */
    public function validateApplicationId($requestApplicationId)
    {
        if (!in_array($requestApplicationId, $this->applicationIdArray)) {
            throw new InvalidArgumentException(self::ERROR_APPLICATION_ID_NOT_MATCHED);
        }
    }

    // Accessors

    /**
     * @return array
     */
    public function getApplicationId()
    {
        return $this->applicationIdArray;
    }

    // Mutators

    /**
     * @param array $applicationIdArray
     */
    public function setApplicationIdArray(array $applicationIdArray)
    {
        $this->applicationIdArray = $applicationIdArray;
    }
}
