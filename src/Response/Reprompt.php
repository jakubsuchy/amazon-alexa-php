<?php

namespace Alexa\Response;

use Alexa\Response\OutputSpeech\OutputSpeechInterface;

/**
 * Class Reprompt
 *
 * Encapsulate a Reprompt
 *
 * @package Alexa\Response
 */
class Reprompt
{
    // Fields

    /**
     * @var OutputSpeechInterface
     */
    private $outputSpeech;

    // Hooks

    /**
     * Reprompt constructor.
     *
     * @param OutputSpeechInterface $outputSpeech
     */
    public function __construct(OutputSpeechInterface $outputSpeech)
    {
        $this->outputSpeech = $outputSpeech;
    }

    /**
     * render()
     *
     * Render the reprompt as an array for JSON encoding
     *
     * @return array
     */
    public function render()
    {
        return [
            'outputSpeech' => $this->outputSpeech->render()
        ];
    }

    // Accessors

    /**
     * @return OutputSpeechInterface
     */
    public function getOutputSpeech()
    {
        return $this->outputSpeech;
    }

    // Mutators

    /**
     * @param OutputSpeechInterface $outputSpeech
     */
    protected function setOutputSpeech($outputSpeech)
    {
        $this->outputSpeech = $outputSpeech;
    }
}
