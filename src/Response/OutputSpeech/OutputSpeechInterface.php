<?php

namespace Alexa\Response\OutputSpeech;

/**
 * Interface OutputSpeechInterface
 *
 * Represents an Alexa OutputSpeech object
 *
 * @package Alexa\Response\OutputSpeech
 */
interface OutputSpeechInterface
{
    /**
     * render()
     *
     * Return an array representation of the OutputSpeech object for JSON encoding
     *
     * @return array
     */
    public function render();
}
