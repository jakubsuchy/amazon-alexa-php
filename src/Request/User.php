<?php

namespace Alexa\Request;


use Symfony\Component\Validator\Constraints as Assert;

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
     *
     * @Assert\Type("string")
     * @Assert\NotBlank
     */
    protected $userId;
    /**
     * @var string
     *
     * @Assert\Type("string")
     * @Assert\NotBlank
     */
    protected $accessToken;

    // Hooks

    public function __construct($data)
    {
        $this->setUserId(isset($data['userId']) ? $data['userId'] : null);
        $this->setAccessToken(isset($data['accessToken']) ? $data['accessToken'] : null);
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
        $this->userId = (string)$userId;
    }

    /**
     * @param string $accessToken
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = (string)$accessToken;
    }
}
