<?php

namespace Alexa\Request;

/**
 * Class Session
 *
 * Encapsulate an Alexa session
 *
 * @package Alexa\Request
 */
class Session
{
    // Fields

    protected $user;
    protected $new;
    protected $application;
    protected $sessionId;
    protected $attributes = [];

    // Hooks

    public function __construct($data)
    {
        $this->user = new User($data['user']);
        $this->sessionId = isset($data['sessionId']) ? $data['sessionId'] : null;
        $this->new = isset($data['new']) ? $data['new'] : null;
        if (!$this->new && isset($data['attributes'])) {
                $this->attributes = $data['attributes'];
        }
    }

    // Public Methods

    /**
    * Remove "SessionId." prefix from the send session id, as it's invalid
    * as a session id (at least for default session, on file).
    * @param type $sessionId
    * @return type
    */
    protected function parseSessionId($sessionId)
    {
        $prefix = 'SessionId.';
        if (substr($sessionId, 0, strlen($prefix)) == $prefix) {
            return substr($sessionId, strlen($prefix));
        } else {
            return $sessionId;
        }
    }
       
    /**
    * Open PHP SESSION using amazon provided sessionId, for storing data about the session.
    * Session cookie won't be sent.
    */
    public function openSession()
    {
        ini_set('session.use_cookies', 0); # disable session cookies
        session_id($this->parseSessionId($this->sessionId));
        return session_start();
    }
       
    /**
    * Returns attribute value of $default.
    * @param string $key
    * @param mixed $default
    * @return mixed
    */
    public function getAttribute($key, $default = false)
    {
        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        } else {
            return $default;
        }
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
     * @return null
     */
    public function getNew()
    {
        return $this->new;
    }

    /**
     * @return Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @return null
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
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @param null $new
     */
    public function setNew($new)
    {
        $this->new = $new;
    }

    /**
     * @param mixed $application
     */
    public function setApplication($application)
    {
        $this->application = $application;
    }

    /**
     * @param null $sessionId
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
    }

    /**
     * @param array $attributes
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }
}
