<?php

namespace Alexa\Response\OutputSpeech;

use Alexa\Response\OutputSpeech\OutputSpeechInterface;
use Alexa\Response\OutputSpeech\OutputSpeechTypes;

/**
 * Class SsmlOutputSpeech
 *
 * Represents an SSML OutputSpeech Alexa response object
 *
 * @package Alexa\Response\OutputSpeech
 */
class SsmlOutputSpeech implements OutputSpeechInterface
{
    // Constants

    const ERROR_SSML_NOT_SET  = 'You must provide output speech SSML.';

    // Fields

    /**
     * @var string
     */
    private $ssml;

    // Hooks

    /**
     * SsmlOutputSpeech constructor.
     *
     * @param string $ssml
     */
    public function __construct($ssml)
    {
        $this->setSsml($ssml);
    }

    // Public Methods

    /**
     * @return array
     */
    public function render()
    {
        return [
            'type' => OutputSpeechTypes::TYPE_SSML,
            'ssml' => $this->getSsml()
        ];
    }

    // Accessors

    /**
     * @return string
     */
    public function getSsml()
    {
        return $this->ssml;
    }

    // Mutators

    /**
     * @param string $ssml
     */
    protected function setSsml($ssml)
    {
        if (!is_string($ssml)) {
            throw new \InvalidArgumentException(self::ERROR_SSML_NOT_SET);
        }

        $this->ssml = $ssml;
    }
}
