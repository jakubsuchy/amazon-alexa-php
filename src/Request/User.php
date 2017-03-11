<?php

namespace Alexa\Request;

use Symfony\Component\Validator\Constraints as Assert;

use Alexa\Utility\Purifier\HasPurifier;

/**
 * Class User
 *
 * Represents the User attached to an Alexa request
 *
 * @package Alexa\Request
 */
class User
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
    private $userId;
    /**
     * @var string
     *
     * @Assert\Type("string")
     * @Assert\NotBlank
     */
    private $accessToken;

    // Hooks

    /**
     * User constructor.
     *
     * @param array $data
     * @param \HTMLPurifier $purifier
     */
    public function __construct(array $data, \HTMLPurifier $purifier)
    {
        // Set purifier
        $this->setPurifier($purifier);

        // Set fields
        $this->setUserId($data['userId']);
        $this->setAccessToken(isset($data['accessToken']) ? $data['accessToken'] : null);
    }

    // Public Methods

    /**
     * validateAccessToken()
     *
     * Returns true if the $expectedAccessToken matches the token found in the Alexa request
     *
     * @param $expectedAccessToken
     *
     * @return bool
     */
    public function validateAccessToken($expectedAccessToken)
    {
        return $expectedAccessToken === $this->getAccessToken();
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
    protected function setUserId($userId)
    {
        $this->userId = $userId ? $this->purifier->purify((string)$userId) : null;
    }

    /**
     * @param string $accessToken
     */
    protected function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken ? $this->purifier->purify((string)$accessToken) : null;
    }
}
