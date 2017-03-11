<?php

namespace Alexa\Response;

/**
 * Class Card
 *
 * Encapsulate an Alexa response card
 *
 * @package Alexa\Response
 */
class Card
{
    // Constants

    const CARD_TYPE_SIMPLE = 'Simple';
    const CARD_TYPE_STANDARD = 'Standard';
    const CARD_TYPE_LINK_ACCOUNT = 'LinkAccount';

    const ERROR_INVALID_CARD_TYPE = 'The card type \'%s\' is not one of the valid options';

    // Fields

    /**
     * @var array
     */
    public static $validCardTypes = [
        self::CARD_TYPE_SIMPLE,
        self::CARD_TYPE_STANDARD,
        self::CARD_TYPE_LINK_ACCOUNT
    ];

    /**
     * @var string
     */
    protected $type = self::CARD_TYPE_SIMPLE;
    /**
     * @var string
     */
    protected $title;
    /**
     * @var string
     */
    protected $simpleCardContent;
    /**
     * @var string
     */
    protected $standardCardText;
    /**
     * @var string
     */
    protected $smallImageUrl;
    /**
     * @var string
     */
    protected $largeImageUrl;


    // Public Methods

    /**
     * @return array
     */
    public function render()
    {
        $cardType = $this->getType();

        switch ($cardType) {
            case self::CARD_TYPE_SIMPLE:
                return [
                    'type' => $cardType,
                    'title' => $this->getTitle(),
                    'content' => $this->getSimpleCardContent()
                ];
            case self::CARD_TYPE_STANDARD:
                return [
                    'type' => $cardType,
                    'title' => $this->getTitle(),
                    'text' => $this->getStandardCardText(),
                    'image' => [
                        'smallImageUrl' => $this->getSmallImageUrl(),
                        'largeImageUrl' => $this->getLargeImageUrl()
                    ]
                ];
            case self::CARD_TYPE_LINK_ACCOUNT:
                return [
                    'type' => $cardType
                ];
            default:
                throw new \InvalidArgumentException(
                    sprintf(self::ERROR_INVALID_CARD_TYPE, $cardType)
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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getSimpleCardContent()
    {
        return $this->simpleCardContent;
    }

    /**
     * @return string
     */
    public function getStandardCardText()
    {
        return $this->standardCardText;
    }

    /**
     * @return string
     */
    public function getSmallImageUrl()
    {
        return $this->smallImageUrl;
    }

    /**
     * @return string
     */
    public function getLargeImageUrl()
    {
        return $this->largeImageUrl;
    }

    // Mutators

    /**
     * @param string $type
     *
     * @throws \InvalidArgumentException - If the card type is not in self::$validCardTypes
     */
    public function setType($type)
    {
        if (!in_array($type, self::$validCardTypes)) {
            throw new \InvalidArgumentException(
                sprintf(self::ERROR_INVALID_CARD_TYPE, $type)
            );
        }

        $this->type = (string)$type;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title ? (string)$title : null;
    }

    /**
     * @param string $simpleCardContent
     */
    public function setSimpleCardContent($simpleCardContent)
    {
        $this->simpleCardContent = $simpleCardContent ? (string)$simpleCardContent : null;
    }

    /**
     * @param string $standardCardText
     */
    public function setStandardCardText($standardCardText)
    {
        $this->standardCardText = $standardCardText ? (string)$standardCardText : null;
    }

    /**
     * @param string $smallImageUrl
     */
    public function setSmallImageUrl($smallImageUrl)
    {
        $this->smallImageUrl = $smallImageUrl ? (string)$smallImageUrl : null;
    }

    /**
     * @param string $largeImageUrl
     */
    public function setLargeImageUrl($largeImageUrl)
    {
        $this->largeImageUrl = $largeImageUrl ? (string)$largeImageUrl : null;
    }
}
