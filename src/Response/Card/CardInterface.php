<?php

namespace Alexa\Response\Card;

/**
 * Interface CardInterface
 *
 * Represents an Alexa response card
 *
 * @package Alexa\Response\Card
 */
interface CardInterface
{
    /**
     * render()
     *
     * Return the card as an associate array for conversion to JSON
     *
     * @return array
     */
    public function render();
}
