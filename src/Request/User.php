<?php

namespace Alexa\Request;

/**
 * Class User
 *
 * Encapsulate an Alexa user
 *
 * @package Alexa\Request
 */
class User
{
    // Fields

    /**
     * @var string
     */
    protected $userId;
    /**
     * @var string
     */
    protected $accessToken;

    // Hooks

    public function __construct($data)
    {
        $this->userId = isset($data['userId']) ? $data['userId'] : null;
        $this->accessToken = isset($data['accessToken']) ? $data['accessToken'] : null;
    }

    // Accessors

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    // Mutators

    /**
     * @param string $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @param string $accessToken
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }
}
