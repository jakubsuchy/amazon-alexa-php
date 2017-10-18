<?php

namespace Alexa\Response\Card;

use Alexa\Response\Card\CardInterface;
use Alexa\Response\Card\CardTypes;

/**
 * Class StandardCard
 *
 * Represents an Alexa StandardCard
 *
 * @package Alexa\Response\Card
 */
class StandardCard implements CardInterface
{
    // Constants

    const ERROR_TITLE_NOT_SET = 'You must provide a title.';
    const ERROR_TEXT_NOT_SET = 'You must provide card text.';
    const ERROR_SMALL_IMAGE_URL_NOT_SET = 'You must provide a small image URL.';
    const ERROR_LARGE_IMAGE_URL_NOT_SET = 'You must provide a large image URL.';

    // Fields

    /**
     * @var string
     */
    private $title;
    /**
     * @var string
     */
    private $text;
    /**
     * @var string
     */
    private $smallImageUrl;
    /**
     * @var string
     */
    private $largeImageUrl;

    // Hooks

    /**
     * StandardCard constructor.
     *
     * @param string $title
     * @param string $text
     * @param string $smallImageUrl
     * @param string $largeImageUrl
     */
    public function __construct($title, $text, $smallImageUrl, $largeImageUrl)
    {
        $this->title = $title;
        $this->text = $text;
        $this->smallImageUrl = $smallImageUrl;
        $this->largeImageUrl = $largeImageUrl;
    }

    // Public Methods

    /**
     * @inheritdoc
     */
    public function render()
    {
        return [
            'type' => CardTypes::TYPE_STANDARD,
            'title' => $this->getTitle(),
            'text' => $this->getText(),
            'image' => [
                'smallImageUrl' => $this->getSmallImageUrl(),
                'largeImageUrl' => $this->getLargeImageUrl()
            ]
        ];
    }

    // Accessors

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
    public function getText()
    {
        return $this->text;
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
     * @param string $title
     */
    public function setTitle($title)
    {
        if (!is_string($title)) {
            throw new \InvalidArgumentException(self::ERROR_TITLE_NOT_SET);
        }

        $this->title = $title;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        if (!is_string($text)) {
            throw new \InvalidArgumentException(self::ERROR_TEXT_NOT_SET);
        }

        $this->text = $text;
    }

    /**
     * @param string $smallImageUrl
     */
    public function setSmallImageUrl($smallImageUrl)
    {
        if (!is_string($smallImageUrl)) {
            throw new \InvalidArgumentException(self::ERROR_SMALL_IMAGE_URL_NOT_SET);
        }

        $this->smallImageUrl = $smallImageUrl;
    }

    /**
     * @param string $largeImageUrl
     */
    public function setLargeImageUrl($largeImageUrl)
    {
        if (!is_string($largeImageUrl)) {
            throw new \InvalidArgumentException(self::ERROR_LARGE_IMAGE_URL_NOT_SET);
        }

        $this->largeImageUrl = $largeImageUrl;
    }
}
