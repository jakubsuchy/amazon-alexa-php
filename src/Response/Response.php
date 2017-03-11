<?php

namespace Alexa\Response;

/**
 * Class Response
 *
 * Represents and Alexa Response
 *
 * @package Alexa\Response
 */
class Response
{
    // Fields

    /**
     * @var string
     */
    protected $version = '1.0';
    /**
     * @var array
     */
    protected $sessionAttributes = [];
    /**
     * @var OutputSpeech
     */
    protected $outputSpeech;
    /**
     * @var Card
     */
    protected $card;
    /**
     * @var Reprompt
     */
    protected $reprompt;
    /**
     * @var bool
     */
    protected $shouldEndSession = false;

    // Public Methods

    /**
     * Set output speech as text
     *
     * @param string $text
     *
     * @return \Alexa\Response\Response
     */
    public function respond($text)
    {
        $this->outputSpeech = new OutputSpeech;
        $this->outputSpeech->setText($text);

        return $this;
    }
        
    /**
     * Set up response with SSML.
     * @param string $ssml
     * @return \Alexa\Response\Response
     */
    public function respondSSML($ssml) {
        $this->outputSpeech = new OutputSpeech;
        $this->outputSpeech->setType(OutputSpeech::TYPE_SSML);
        $this->outputSpeech->setSsml($ssml);

        return $this;
    }

    /**
     * Set up reprompt with given text
     * @param string $text
     * @return \Alexa\Response\Response
     */
    public function reprompt($text)
    {
        $this->reprompt = new Reprompt;
        $this->reprompt->getOutputSpeech()->setText($text);

        return $this;
    }
        
    /**
     * Set up reprompt with given ssml
     * @param string $ssml
     * @return \Alexa\Response\Response
     */
    public function repromptSSML($ssml)
    {
        $this->reprompt = new Reprompt;
        $this->reprompt->getOutputSpeech()->setType(OutputSpeech::TYPE_SSML);
        $this->reprompt->getOutputSpeech()->setSsml($ssml);

        return $this;
    }

    /**
     * withCard()
     *
     * Create a SimpleCard
     *
     * @param string $title
     * @param string $content
     *
     * @return \Alexa\Response\Response
     */
    public function withCard($title, $content)
    {
        $this->card = new Card;
        $this->card->setType(Card::CARD_TYPE_SIMPLE);
        $this->card->setTitle($title);
        $this->card->setSimpleCardContent($content);
        
        return $this;
    }

    /**
     * withStandardCard()
     *
     * Create a StandardCard with image URLs
     *
     * @param string $title
     * @param $cardText
     * @param $smallImageUrl
     * @param $largeImageUrl
     *
     * @return Response
     */
    public function withStandardCard($title, $cardText, $smallImageUrl, $largeImageUrl)
    {
        $this->card = new Card;
        $this->card->setType(Card::CARD_TYPE_STANDARD);
        $this->card->setTitle($title);
        $this->card->setStandardCardText($cardText);
        $this->card->setSmallImageUrl($smallImageUrl);
        $this->card->setLargeImageUrl($largeImageUrl);

        return $this;
    }

    /**
     * withLinkAccountCard()
     *
     * Create a LinkAccount card
     *
     * @return Response
     */
    public function withLinkAccountCard()
    {
        $this->card = new Card;
        $this->card->setType(Card::CARD_TYPE_LINK_ACCOUNT);

        return $this;
    }

    /**
     * Set if it should end the session
     *
     * @param bool $shouldEndSession
     *
     * @return \Alexa\Response\Response
     */
    public function endSession($shouldEndSession = true)
    {
        $this->setShouldEndSession($shouldEndSession);

        return $this;
    }
        
    /**
     * Add a session attribute that will be passed in every requests.
     * @param string $key
     * @param mixed $value
     */
    public function addSessionAttribute($key, $value)
    {
        $this->sessionAttributes[$key] = $value;
    }

    /**
     * Return the response as an array for JSON-ification
     * @return type
     */
    public function render()
    {
        return [
            'version' => $this->version,
            'sessionAttributes' => $this->sessionAttributes,
            'response' => [
                'outputSpeech' => $this->outputSpeech ? $this->outputSpeech->render() : null,
                'card' => $this->card ? $this->card->render() : null,
                'reprompt' => $this->reprompt ? $this->reprompt->render() : null,
                'shouldEndSession' => $this->shouldEndSession ? true : false
            ]
        ];
    }

    // Accessors

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return array
     */
    public function getSessionAttributes()
    {
        return $this->sessionAttributes;
    }

    /**
     * @return OutputSpeech
     */
    public function getOutputSpeech()
    {
        return $this->outputSpeech;
    }

    /**
     * @return Card
     */
    public function getCard()
    {
        return $this->card;
    }

    /**
     * @return Reprompt
     */
    public function getReprompt()
    {
        return $this->reprompt;
    }

    /**
     * @return bool
     */
    public function shouldEndSession()
    {
        return $this->shouldEndSession;
    }

     // Mutators

    /**
     * @param string $version
     */
    public function setVersion($version)
    {
        $this->version = $version ? (string)$version : null;
    }

    /**
     * @param array $sessionAttributes
     */
    public function setSessionAttributes(array $sessionAttributes)
    {
        $this->sessionAttributes = $sessionAttributes;
    }

    /**
     * @param OutputSpeech $outputSpeech
     */
    public function setOutputSpeech(OutputSpeech $outputSpeech)
    {
        $this->outputSpeech = $outputSpeech;
    }

    /**
     * @param Card $card
     */
    public function setCard(Card $card)
    {
        $this->card = $card;
    }

    /**
     * @param Reprompt $reprompt
     */
    public function setReprompt(Reprompt $reprompt)
    {
        $this->reprompt = $reprompt;
    }

    /**
     * @param bool $shouldEndSession
     */
    public function setShouldEndSession($shouldEndSession)
    {
        $this->shouldEndSession = (bool)$shouldEndSession;
    }
}
