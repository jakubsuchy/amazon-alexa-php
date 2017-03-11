<?php

namespace Alexa\Request;

use Symfony\Component\Validator\Constraints as Assert;

use Alexa\Request\Certificate;
use Alexa\Request\Application;

use Alexa\Utility\Purifier\HasPurifier;

/**
 * Class BaseRequest
 *
 * Encapsulates an Alexa request
 *
 * @package Alexa\Request
 */
abstract class BaseRequest implements RequestInterface
{
    // Traits

    use HasPurifier;

    // Fields

    /**
     * @var string
     *
     * @Assert\Type("string")
     * @Assert\NotBlank
     */
    private $requestId;
    /**
     * @var \DateTime
     *
     * @Assert\DateTime
     * @Assert\NotBlank
     */
    private $timestamp;
    /**
     * @var array
     *
     * @Assert\Type("array")
     * @Assert\NotBlank
     */
    private $data;
    /**
     * @var string
     *
     * @Assert\Type("string")
     * @Assert\NotBlank
     */
    private $rawData;
    /**
     * @var \Alexa\Request\Certificate
     *
     * @Assert\Type("\Alexa\Request\Certificate")
     * @Assert\NotBlank
     */
    private $certificate;
    /**
     * @var \Alexa\Request\Application
     *
     * @Assert\Type("\Alexa\Request\Application")
     * @Assert\NotBlank
     */
    private $application;
    /**
     * @var Session
     *
     * @Assert\Type("\Alexa\Request\Session")
     * @Assert\NotBlank
     */
    private $session;

    // Hooks

    /**
     * Request()
     *
     * Parse the JSON onto the RequestInterface object
     *
     * @param string $rawData - The original JSON response, before json_decode
     * @param Certificate $certificate - Override the auto-generated Certificate with your own
     * @param Application $application - Override the auto-generated Application with your own
     * @param \HTMLPurifier $purifier
     */
    public function __construct(
        $rawData,
        Certificate $certificate,
        Application $application,
        \HTMLPurifier $purifier
    ) {
        // Check $rawData format
        if (!is_string($rawData)) {
            throw new \InvalidArgumentException('Alexa Request requires the raw JSON data '.
                'to validate request signature');
        }

        // Store the raw data
        $this->setRawData($rawData);

        // Decode the raw data into a JSON array
        $this->setData(json_decode($rawData, true));

        // Set values
        $this->setPurifier($purifier);
        $this->setRequestId($this->data['request']['requestId']);
        $this->setTimestamp(new \DateTime($this->data['request']['timestamp']));
        $this->setSession(new Session($this->data['session'], $this->getPurifier()));
        $this->setCertificate($certificate);
        $this->setApplication($application);
    }

    /**
     * destroyPurifiers()
     *
     * Destroy all the purifiers associated with this Request and its dependencies,
     * to improve dumpability/loggability
     *
     * @return void
     */
    public function destroyPurifiers()
    {
        $this->destroyPurifier();
        if ($this->getSession()) {
            $this->getSession()->destroyPurifier();
            $this->getSession()->getUser()->destroyPurifier();
        }
        if ($this->getApplication()) {
            $this->getApplication()->destroyPurifier();
        }
        if ($this->getCertificate()) {
            $this->getCertificate()->destroyPurifier();
        }
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
     * @return \DateTime
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
    protected function setRequestId($requestId)
    {
        $this->requestId = $requestId ? $this->getPurifier()->purify((string)$requestId) : null;
    }

    /**
     * @param \DateTime $timestamp
     */
    protected function setTimestamp(\DateTime $timestamp)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @param array $data
     */
    protected function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param string $rawData
     */
    protected function setRawData($rawData)
    {
        $this->rawData = $rawData ? (string)$rawData : null;
    }

    /**
     * @param \Alexa\Request\Certificate $certificate
     */
    protected function setCertificate(Certificate $certificate)
    {
        $this->certificate = $certificate;
    }

    /**
     * @param \Alexa\Request\Application $application
     */
    protected function setApplication(Application $application)
    {
        $this->application = $application;
    }

    /**
     * @param Session $session
     */
    protected function setSession(Session $session)
    {
        $this->session = $session;
    }
}
