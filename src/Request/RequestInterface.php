<?php

namespace Alexa\Request;

/**
 * Interface RequestInterface
 *
 * Provided as a strong typing abstraction
 *
 * @package Alexa\Request
 */
interface RequestInterface
{
    // Accessors

    /**
     * The ID of the Alexa request
     *
     * @return string
     */
    public function getRequestId();

    /**
     * The timestamp on the request
     *
     * @return \DateTime
     */
    public function getTimestamp();

    /**
     * The request JSON parsed into an associative array
     *
     * @return array
     */
    public function getData();

    /**
     * The raw HTTP request content
     *
     * @return string
     */
    public function getRawData();

    /**
     * @return \Alexa\Request\Certificate
     */
    public function getCertificate();

    /**
     * @return \Alexa\Request\Application
     */
    public function getApplication();

    /**
     * @return Session
     */
    public function getSession();
}
