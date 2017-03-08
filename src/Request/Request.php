<?php

namespace Alexa\Request;

use \RuntimeException;
use \DateTime;

use Alexa\Request\Certificate;
use Alexa\Request\Application;

/**
 * Class Request
 *
 * Encapsulates an Alexa request
 *
 * @package Alexa\Request
 */
abstract class Request implements RequestInterface
{
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

}
