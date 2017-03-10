<?php

namespace Alexa\Request;

use Symfony\Component\Validator\Constraints as Assert;

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
     *
     * @Assert\Type("string")
     * @Assert\NotBlank
     */
    protected $requestId;
    /**
     * @var DateTime
     *
     * @Assert\DateTime
     * @Assert\NotBlank
     */
    protected $timestamp;
    /**
     * @var array
     *
     * @Assert\Type("array")
     * @Assert\NotBlank
     */
    protected $data;
    /**
     * @var string
     *
     * @Assert\Type("string")
     * @Assert\NotBlank
     */
    protected $rawData;
    /**
     * @var string
     *
     * @Assert\Type("string")
     * @Assert\NotBlank
     */
    protected $applicationId;
    /**
     * @var \Alexa\Request\Certificate
     *
     * @Assert\Type("\Alexa\Request\Certificate")
     * @Assert\NotBlank
     */
    protected $certificate;
    /**
     * @var \Alexa\Request\Application
     *
     * @Assert\Type("\Alexa\Request\Application")
     * @Assert\NotBlank
     */
    protected $application;
    /**
     * @var Session
     *
     * @Assert\Type("\Alexa\Request\Session")
     * @Assert\NotBlank
     */
    protected $session;

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
    public function __construct(
        $rawData,
        $applicationId,
        Certificate $certificate = null,
        Application $application = null
    ) {
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


    // Accessors

    /**
     * @return string
     */
    public function getRequestId()
    {
        return $this->requestId;
    }

    /**
     * @return DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getRawData()
    {
        return $this->rawData;
    }

    /**
     * @return string
     */
    public function getApplicationId()
    {
        return $this->applicationId;
    }

    /**
     * @return \Alexa\Request\Certificate
     */
    public function getCertificate()
    {
        return $this->certificate;
    }

    /**
     * @return \Alexa\Request\Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @return Session
     */
    public function getSession()
    {
        return $this->session;
    }

    // Mutators

    /**
     * @param string $requestId
     */
    public function setRequestId($requestId)
    {
        $this->requestId = (string)$requestId;
    }

    /**
     * @param DateTime $timestamp
     */
    public function setTimestamp(\DateTime $timestamp)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param string $rawData
     */
    public function setRawData($rawData)
    {
        $this->rawData = (string)$rawData;
    }

    /**
     * @param string $applicationId
     */
    public function setApplicationId($applicationId)
    {
        $this->applicationId = (string)$applicationId;
    }

    /**
     * @param \Alexa\Request\Certificate $certificate
     */
    public function setCertificate(Certificate $certificate)
    {
        $this->certificate = $certificate;
    }

    /**
     * @param \Alexa\Request\Application $application
     */
    public function setApplication(Application $application)
    {
        $this->application = $application;
    }

    /**
     * @param Session $session
     */
    public function setSession(Session $session)
    {
        $this->session = $session;
    }
}
