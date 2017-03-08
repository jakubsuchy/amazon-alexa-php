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
    // Fields

    protected $type = 'Simple';
    protected $title = '';
    protected $content = '';

    // Public Methods

    /**
     * @return array
     */
    public function render()
    {
        return [
            'type' => $this->type,
            'title' => $this->title,
            'content' => $this->content,
        ];
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
    public function getContent()
    {
        return $this->content;
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
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }
}
