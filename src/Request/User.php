<?php

namespace Alexa\Request;


use Alexa\Utility\PurifierHelper;
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
    // Traits

    use PurifierHelper;

    // Fields

    /**
     * @var \HTMLPurifier
     */
    protected $purifier;
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

    /**
     * User constructor.
     *
     * @param array $data
     * @param \HTMLPurifier|null $purifier
     */
    public function __construct(array $data, \HTMLPurifier $purifier = null)
    {
        // Set purifier
        $this->purifier = $purifier ?: $this->getPurifier();

        // Set fields
        $this->setUserId($data['userId']);
        $this->setAccessToken($data['accessToken']);
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
        $this->userId = $this->purifier->purify((string)$userId);
    }

    /**
     * @param string $accessToken
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $this->purifier->purify((string)$accessToken);
    }
}
