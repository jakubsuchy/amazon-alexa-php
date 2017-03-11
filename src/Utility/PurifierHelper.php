<?php

namespace Alexa\Utility;

/**
 * Class PurifierHelper
 *
 * Methods for working with the HTML Purifier
 *
 * @package Alexa\Utility
 */
trait PurifierHelper
{
    // Public Methods

    /**
     * destroyPurifier()
     *
     * When the purifier is no longer needed by your application, you can call ::destroyPurifier on the objects
     * to set it null.  This makes the object easier to log.
     */
    public function destroyPurifier()
    {
        $this->purifier = null;
    }

    // Protected Methods

    /**
     * getPurifier()
     *
     * Retrieve an HTMLPurifier object
     *
     * @return \HTMLPurifier
     */
    protected function getPurifier()
    {
        $config = \HTMLPurifier_Config::createDefault();
        $config->set('Cache.SerializerPath', '/tmp');
        $purifier = new \HTMLPurifier($config);

        return $purifier;
    }

}
