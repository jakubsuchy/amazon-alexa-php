<?php

namespace Alexa\Request;

use \RuntimeException;
use \DateTime;

use Alexa\Request\Certificate;
use Alexa\Request\Application;

/**
 * Class Request
 *
 * Encapsulate an Alexa request
 *
 * @package Alexa\Request
 */
abstract class Request implements RequestInterface
{
    // Constants

    const ERROR_INVALID_REQUEST_TYPE = 'Unknown Request Type: %s';

    // Fields

    /**
     * @var string
     */
    public $requestId;
    /**
     * @var DateTime
     */
    public $timestamp;
    /**
     * @var Session
     */
    public $session;
    /**
     * @var array
     */
    public $data;
    /**
     * @var string
     */
    public $rawData;
    /**
     * @var string
     */
    public $applicationId;

    // Hooks

    /**
     * Request()
     *
     * Parse the JSON onto the RequestInterface object
     *
     * @param string $rawData - The original JSON response, before json_decode
     * @param string $applicationId - Your Alexa Dev Portal application ID
     * @param Certificate|null $certificate - Override the auto-generated Certificate with your own
     * @param Application|null $application - Override the auto-generated Application with your own
     */
    public function __construct($rawData, $applicationId, $certificate = null, $application = null)
    {
        // Check $rawData format
        if (!is_string($rawData)) {
            throw new \InvalidArgumentException('Alexa Request requires the raw JSON data '.
                'to validate request signature');
        }

        // Store the raw data
        $this->rawData = $rawData;

        // Decode the raw data into a JSON array
        $this->data = json_decode($rawData, true);

        // Parse top-level values
        $this->requestId = $this->data['request']['requestId'];
        $this->timestamp = new DateTime($this->data['request']['timestamp']);
        $this->session = new Session($this->data['session']);
        $this->applicationId = $applicationId;

        // Create certificate from server data if not provided
        $this->certificate = $certificate ?:
            new Certificate($_SERVER['HTTP_SIGNATURECERTCHAINURL'], $_SERVER['HTTP_SIGNATURE']);

        // Create application from ID if override not provided
        $this->application = $application ?: new Application($applicationId);
    }

    // Factory

    /**
     * fromRawData()
     *
     * Return an instance of the correct type of Request from the raw JSON string
     *
     *
     * @param string $rawData - The raw POST value, before json_decode
     * @param string $applicationId - Your application's ID (from the dev portal)
     * @param Certificate|null $certificate - Override the auto-generated Certificate with your own
     * @param Application|null $application - Override the auto-generated Application with your own
     *
     * @return \Alexa\Request\Request
     * @throws RuntimeException
     */
    public static function fromRawData(
        $rawData,
        $applicationId,
        Certificate $certificate = null,
        Application $application = null
    ) {
        // Parse data for construction
        $data = json_decode($rawData, true);

        // Generate base request
        $request = static::generateRequest($data, $rawData, $applicationId, $certificate, $application);

        // Validate received application ID matches client value
        $request->application->validateApplicationId($data['session']['application']['applicationId']);

        // Validate that the request signature matches the certificate
        $request->certificate->validateRequest($rawData);

        // Return complete request
        return $request;
    }

    // Protected Methods

    /**
     * generateRequest()
     *
     * Generate a RequestInterface object of the correct type
     *
     * @param array $data
     * @param $rawData
     * @param $applicationId
     * @param Certificate|null $certificate - Override the auto-generated Certificate with your own
     * @param Application|null $application - Override the auto-generated Application with your own
     *
     * @return mixed
     * @throws \RuntimeException - If the request type is not a valid RequestInterface class
     */
    protected static function generateRequest(
        array $data,
        $rawData,
        $applicationId,
        $certificate,
        $application
    ) {
        // Retrieve request type
        $requestType = $data['request']['type'];

        // Validate request type
        if (!in_array($requestType, array_keys(CustomSkillRequestTypes::$validTypes))) {
            throw new \RuntimeException(
                sprintf(static::ERROR_INVALID_REQUEST_TYPE, $requestType)
            );
        }

        // Retrieve the correct request child class
        $requestClass = CustomSkillRequestTypes::$validTypes[$requestType];

        // Generate request
        $request = new $requestClass($rawData, $applicationId, $certificate, $application);

        // Return
        return $request;
    }
}
