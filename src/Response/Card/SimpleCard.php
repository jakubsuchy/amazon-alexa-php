<?php

namespace Alexa\Response\Card;

use Alexa\Response\Card\CardInterface;

/**
 * Class SimpleCard
 *
 * Represents an Alexa SimpleCard
 *
 * @package Alexa\Response\Card
 */
class SimpleCard implements CardInterface
{
    // Constants

    const CARD_TYPE = 'Simple';

    const ERROR_TITLE_NOT_SET = 'You must provide a title for the card';
    const ERROR_CONTENT_NOT_SET = 'You must provide the content for the card';

    // Fields

    /**
     * @var string
     */
    private $title;
    /**
     * @var string
     */
    private $content;

    // Hooks

    /**
     * SimpleCard constructor.
     *
     * @param string $title
     * @param string $content
     */
    public function __construct($title, $content)
    {
        $this->setTitle($title);
        $this->setContent($content);
    }

    // Public Methods

    /**
     * @inheritdoc
     *
     * @return array
     */
    public function render()
    {
        return [
            'type' => self::CARD_TYPE,
            'title' => $this->getTitle(),
            'content' => $this->getContent()
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
    public function getContent()
    {
        return $this->content;
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
     * @param string $content
     */
    public function setContent($content)
    {
        if (!is_string($content)) {
            throw new \InvalidArgumentException(self::ERROR_CONTENT_NOT_SET);
        }

        $this->content = $content;
    }
}
