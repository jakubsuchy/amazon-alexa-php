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
    // Fields

    /**
     * @var array
     */
    protected $applicationIdArray;

    /**
     * @var string
     */
    protected $requestApplicationId;


    // Hooks

    /**
     * Application constructor.
     *
     * @param $applicationId
     */
    public function __construct($applicationId)
    {
        $this->setApplicationIdArray(preg_split('/,/', $applicationId));
    }

    // Public Methods

    /**
     * Validate that the request Application ID matches our Application. This is required as per Amazon requirements.
     *
     * @param string $requestApplicationId - Application ID from the Request
     *                               (typically found in $data['session']['application']
     */
    public function validateApplicationId($requestApplicationId = "")
    {
        if (empty($requestApplicationId)) {
            $requestApplicationId = $this->requestApplicationId;
        }

        if (!in_array($requestApplicationId, $this->applicationIdArray)) {
            throw new InvalidArgumentException('Application Id not matched');
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

    /**
     * @return mixed
     */
    public function getRequestApplicationId()
    {
        return $this->requestApplicationId;
    }

    // Mutators

    /**
     * @param array $applicationIdArray
     */
    public function setApplicationIdArray(array $applicationIdArray)
    {
        $this->applicationIdArray = $applicationIdArray;
    }

    /**
     * @param string $requestApplicationId
     */
    public function setRequestApplicationId($requestApplicationId)
    {
        $this->requestApplicationId = $requestApplicationId;
    }
}
