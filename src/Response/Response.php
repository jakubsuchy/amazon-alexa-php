<?php

namespace Alexa\Response;

use Alexa\Response\Card\CardInterface;
use Alexa\Response\Card\LinkAccountCard;
use Alexa\Response\Card\SimpleCard;
use Alexa\Response\Card\StandardCard;
use Alexa\Response\OutputSpeech\OutputSpeechInterface;
use Alexa\Response\OutputSpeech\PlainTextOutputSpeech;
use Alexa\Response\OutputSpeech\SsmlOutputSpeech;

/**
 * Class Response
 *
 * Represents an Alexa Response
 *
 * @package Alexa\Response
 */
class Response
{
    // Fields

    /**
     * @var string
     */
    private $version = '1.0';
    /**
     * @var array
     */
    private $sessionAttributes = [];
    /**
     * @var OutputSpeechInterface
     */
    private $outputSpeech;
    /**
     * @var CardInterface
     */
    private $card;
    /**
     * @var Reprompt
     */
    private $reprompt;
    /**
     * @var bool
     */
    private $shouldEndSession = false;

    // Public Methods

    /**
     * respond()
     *
     * Set output speech as $text
     *
     * @param string $text
     *
     * @return \Alexa\Response\Response
     */
    public function respond($text)
    {
        $this->setOutputSpeech(new PlainTextOutputSpeech($text));

        return $this;
    }
        
    /**
     * respondSSML()
     *
     * Set up response with SSML
     *
     * @param string $ssml
     *
     * @return \Alexa\Response\Response
     */
    public function respondSSML($ssml)
    {
        $this->setOutputSpeech(new SsmlOutputSpeech($ssml));

        return $this;
    }

    /**
     * reprompt()
     *
     * Set up reprompt with given text
     *
     * @param string $text
     *
     * @return \Alexa\Response\Response
     */
    public function reprompt($text)
    {
        $outputSpeech = new PlainTextOutputSpeech($text);
        $this->setReprompt(new Reprompt($outputSpeech));

        return $this;
    }
        
    /**
     * repromptSSML()
     *
     * Set up reprompt with given ssml
     *
     * @param string $ssml
     *
     * @return \Alexa\Response\Response
     */
    public function repromptSSML($ssml)
    {
        $outputSpeech = new SsmlOutputSpeech($ssml);
        $this->setReprompt(new Reprompt($outputSpeech));

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
        $simpleCard = new SimpleCard($title, $content);
        $this->setCard($simpleCard);
        
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
        $standardCard = new StandardCard($title, $cardText, $smallImageUrl, $largeImageUrl);
        $this->setCard($standardCard);

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
        $this->setCard(new LinkAccountCard());

        return $this;
    }

    /**
     * endSession()
     *
     * Set if this response should end the session
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
     * addSessionAttribute()
     *
     * Add a session attribute that will be passed along with future requests in the conversation
     *
     * @param string $key
     * @param mixed $value
     */
    public function addSessionAttribute($key, $value)
    {
        $this->sessionAttributes[$key] = $value;
    }

    /**
     * render()
     *
     * Return the response as an array for JSON encoding
     *
     * @return array
     */
    public function render()
    {
        $outputArray = [
            'version' => $this->version,
            'response' => [
                'shouldEndSession' => $this->shouldEndSession()
            ]
        ];

        if (count($this->getSessionAttributes())) {
            $outputArray['sessionAttributes'] = $this->getSessionAttributes();
        }

        if ($this->getOutputSpeech()) {
            $outputArray['response']['outputSpeech'] = $this->getOutputSpeech()->render();
        }


        if ($this->getCard()) {
            $outputArray['response']['card'] = $this->getCard()->render();
        }


        if ($this->getReprompt()) {
            $outputArray['response']['reprompt'] = $this->getReprompt()->render();
        }

        // Return
        return $outputArray;
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
     * @return OutputSpeechInterface
     */
    public function getOutputSpeech()
    {
        return $this->outputSpeech;
    }

    /**
     * @return CardInterface
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
    protected function setVersion($version)
    {
        $this->version = $version ? (string)$version : null;
    }

    /**
     * @param array $sessionAttributes
     */
    protected function setSessionAttributes(array $sessionAttributes)
    {
        $this->sessionAttributes = $sessionAttributes;
    }

    /**
     * @param OutputSpeechInterface $outputSpeech
     */
    protected function setOutputSpeech(OutputSpeechInterface $outputSpeech)
    {
        $this->outputSpeech = $outputSpeech;
    }

    /**
     * @param CardInterface $card
     */
    protected function setCard(CardInterface $card)
    {
        $this->card = $card;
    }

    /**
     * @param Reprompt $reprompt
     */
    protected function setReprompt(Reprompt $reprompt)
    {
        $this->reprompt = $reprompt;
    }

    /**
     * @param bool $shouldEndSession
     */
    protected function setShouldEndSession($shouldEndSession)
    {
        $this->shouldEndSession = (bool)$shouldEndSession;
    }
}
