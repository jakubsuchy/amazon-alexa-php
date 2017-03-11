<?php

namespace Alexa\Utility\Purifier;

/**
 * Class HasPurifier
 *
 * Methods for working with the HTML Purifier
 *
 * @package Alexa\Utility\Purifier
 */
trait HasPurifier
{
    // Fields

    /**
     * @var \HTMLPurifier
     */
    private $purifier;

    // Public Methods

    /**
     * destroyPurifier()
     *
     * Set $this->purififer = null; facilitates Request logging
     *
     * @return void
     */
    public function destroyPurifier()
    {
        $this->setPurifier(null);
    }

    // Accessors

    /**
     * @return \HTMLPurifier
     */
    protected function getPurifier()
    {
        return $this->purifier;
    }

    // Mutators

    /**
     * @param \HTMLPurifier|null $purifier
     */
    protected function setPurifier(\HTMLPurifier $purifier = null)
    {
        $this->purifier = $purifier;
    }
}
