<?php

namespace Alexa\Response\Card;

use Alexa\Response\Card\CardInterface;

/**
 * Class LinkAccountCard
 *
 * Represents an Alexa LinkCard
 *
 * @package Alexa\Response\Card
 */
class LinkAccountCard implements CardInterface
{
    // Constants

    const CARD_TYPE = 'LinkAccount';

    // Public Methods

    /**
     * @inheritdoc
     *
     * @return array
     */
    public function render()
    {
        return [
            'type' => self::CARD_TYPE
        ];
    }
}
