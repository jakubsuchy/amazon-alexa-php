<?php

namespace Alexa\Response;

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

    protected $outputSpeech;

    // Hooks

    /**
     * Reprompt constructor.
     */
    public function __construct()
    {
        $this->outputSpeech = new OutputSpeech;
    }

    /**
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
     * @return OutputSpeech
     */
    public function getOutputSpeech()
    {
        return $this->outputSpeech;
    }

    // Mutators

    /**
     * @param OutputSpeech $outputSpeech
     */
    public function setOutputSpeech($outputSpeech)
    {
        $this->outputSpeech = $outputSpeech;
    }
}
