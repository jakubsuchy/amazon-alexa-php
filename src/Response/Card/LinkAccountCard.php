<?php

namespace Alexa\Response\Card;

use Alexa\Response\Card\CardInterface;
use Alexa\Response\Card\CardTypes;

/**
 * Class LinkAccountCard
 *
 * Represents an Alexa LinkCard
 *
 * @package Alexa\Response\Card
 */
class LinkAccountCard implements CardInterface
{
    // Public Methods

    /**
     * @inheritdoc
     *
     * @return array
     */
    public function render()
    {
        return [
            'type' => CardTypes::TYPE_LINK_ACCOUNT
        ];
    }
}
