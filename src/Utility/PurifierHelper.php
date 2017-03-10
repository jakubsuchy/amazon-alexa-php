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
