<?php

namespace Alexa\Request;

use Symfony\Component\Validator\Constraints as Assert;

use Alexa\Utility\Purifier\HasPurifier;

/**
 * Class Session
 *
 * Encapsulate an Alexa session
 *
 * @package Alexa\Request
 */
class Session
{
    // Traits

    use HasPurifier;

    // Constants

    const SESSION_ID_PREFIX = 'SessionId.';

    // Fields

    /**
     * @var User
     *
     * @Assert\Type("\Alexa\Request\User")
     * @Assert\NotBlank
     */
    private $user;
    /**
     * @var bool
     *
     * @Assert\Type("bool")
     * @Assert\NotNull
     */
    private $new;
    /**
     * @var string
     *
     * @Assert\Type("string")
     * @Assert\NotBlank
     */
    private $sessionId;
    /**
     * @var array
     *
     * @Assert\Type("array")
     * @Assert\NotBlank
     */
    private $attributes = [];

    // Hooks

    /**
     * Session constructor.
     *
     * @param array $data
     * @param \HTMLPurifier $purifier
     */
    public function __construct(array $data, \HTMLPurifier $purifier)
    {
        // Set purifier
        $this->setPurifier($purifier);

        // Set fields
        $this->setUser(new User($data['user'], $this->purifier));
        $this->setSessionId($data['sessionId']);
        $this->setNew($data['new']);

        if (!$this->isNew() && isset($data['attributes'])) {
            // Consumers should purify their attributes arrays
            $this->setAttributes($data['attributes']);
        }
    }

    // Public Methods

    /**
     * openPhpSession()
     *
     * Open PHP session using Amazon-provided sessionId, for storing data about the session.
     * Session cookie won't be sent.
     */
    public function openPhpSession()
    {
        // Disable session cookies
        ini_set('session.use_cookies', 0);

        // Start session
        session_id($this->parseSessionId($this->sessionId));

        return session_start();
    }

    /**
     * getAttribute()
     *
    * Returns attribute value of $default.
     *
    * @param string $key
    * @param mixed $default
    * @return mixed
    */
    public function getAttribute($key, $default = false)
    {
        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }

        return $default;
    }

    // Protected Methods

    /**
     * parseSessionId()
     *
     * Remove "SessionId." prefix from the send session id, as it's invalid
     * as a session ID (at least for default session, on file)
     *
     * @param string $sessionId
     *
     * @return string
     */
    protected function parseSessionId($sessionId)
    {
        if (substr($sessionId, 0, strlen(self::SESSION_ID_PREFIX)) === self::SESSION_ID_PREFIX) {
            return substr($sessionId, strlen(self::SESSION_ID_PREFIX));
        }

        return $sessionId;
    }

    // Accessors

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->new;
    }

    /**
     * @return string
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    // Mutators

    /**
     * @param User $user
     */
    protected function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @param bool $new
     */
    protected function setNew($new)
    {
        $this->new = (bool)$new;
    }

    /**
     * @param null $sessionId
     */
    protected function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId ? $this->purifier->purify((string)$sessionId) : null;
    }

    /**
     * @param array $attributes
     */
    protected function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
    }
}
