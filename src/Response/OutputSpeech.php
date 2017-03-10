<?php

namespace Alexa\Response;

/**
 * Class OutputSpeech
 *
 * Encapsulate the spoken response
 *
 * @package Alexa\Response
 */
class OutputSpeech
{
    // Constants

    const TYPE_TEXT = 'PlainText';
    const TYPE_SSML = 'SSML';

    // Fields

    /**
     * @var string
     */
    protected $type = self::TYPE_TEXT;
    /**
     * @var string
     */
    protected $text;
    /**
     * @var string
     */
    protected $ssml;

    // Public Methods

    /**
     * @return array
     */
    public function render()
    {
        switch($this->type) {
            case self::TYPE_TEXT:
                return [
                    'type' => $this->type,
                    'text' => $this->text
                ];
            case self::TYPE_SSML:
                return [
                    'type' => $this->type,
                    'ssml' => $this->ssml
                ];
        }
    }

    // Accessors

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return string
     */
    public function getSsml()
    {
        return $this->ssml;
    }

    // Mutators

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @param string $ssml
     */
    public function setSsml($ssml)
    {
        $this->ssml = $ssml;
    }
}
