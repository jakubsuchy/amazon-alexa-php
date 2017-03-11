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

    const ERROR_INVALID_SPEECH_TYPE = 'The provided speech type \'%s\' is not one of the valid options.';

    // Fields

    /**
     * @var array
     */
    public static $validSpeechTypes = [
        self::TYPE_TEXT,
        self::TYPE_SSML
    ];

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
        $speechType = $this->getType();

        switch($speechType) {
            case self::TYPE_TEXT:
                return [
                    'type' => $this->getType(),
                    'text' => $this->getText()
                ];
            case self::TYPE_SSML:
                return [
                    'type' => $this->getType(),
                    'ssml' => $this->getSsml()
                ];
            default:
                throw new \InvalidArgumentException(
                    sprintf(self::ERROR_INVALID_SPEECH_TYPE, $speechType)
                );
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
     *
     * @throws \InvalidArgumentException - If the speech type is not in self::$validSpeechTypes
     */
    public function setType($type)
    {
        if (!in_array($type, self::$validSpeechTypes)) {
            throw new \InvalidArgumentException(
                sprintf(self::ERROR_INVALID_SPEECH_TYPE, $type)
            );
        }

        $this->type = (string)$type;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = (string)$text;
    }

    /**
     * @param string $ssml
     */
    public function setSsml($ssml)
    {
        $this->ssml = (string)$ssml;
    }
}
