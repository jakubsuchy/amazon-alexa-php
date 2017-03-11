<?php

namespace Alexa\Utility\Purifier;

/**
 * Class PurifierFactory
 *
 * Generate \HtmlPurifier objects
 *
 * @package Alexa\Utility\Purifier
 */
class PurifierFactory
{
    // Constants

    const DEFAULT_CACHE_PATH = '/tmp';

    // Public Methods

    /**
     * generatePurifier()
     *
     * Generate an \HTMLPurifier with the provided path
     *
     * @param \HTMLPurifier $purifier
     *
     * @return \HTMLPurifier
     */
    public static function generatePurifier($path)
    {
        // Configure default instance
        $config = \HTMLPurifier_Config::createDefault();
        $config->set('Cache.SerializerPath', $path);
        $purifier = new \HTMLPurifier($config);

        return $purifier;
    }
}
