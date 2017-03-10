<?php

namespace Alexa\Request;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class IntentRequest
 * @package Alexa\Request
 */
class IntentRequest extends Request implements RequestInterface
{
    // Constants

    const KEY_SLOT_NAME = 'name';
    const KEY_SLOT_VALUE = 'value';

    // Fields

    /**
     * @var string
     *
     * @Assert\Type("string")
     * @Assert\NotBlank
     */
    protected $intentName;

    /**
     * @var array
     *
     * @Assert\Type("array")
     */
    protected $slots = [];

    // Hooks

    /**
     * IntentRequest()
     *
     * @param string $rawData - The original JSON response, before json_decode
     * @param string $applicationId - Your Alexa Dev Portal application ID
     * @param Certificate|null $certificate - Override the auto-generated Certificate with your own
     * @param Application|null $application - Override the auto-generated Application with your own
     */
    public function __construct(
        $rawData,
        $applicationId,
        Certificate $certificate = null,
        Application $application = null
    ) {
        // Parent construct
        parent::__construct($rawData, $applicationId, $certificate, $application);

        // Retrieve intent name
        $this->setIntentName($this->data['request']['intent']['name']);

        // Generate $this->slots
        $this->generateSlotData();
    }

    // Public Methods

    /**
     * getSlot()
     *
     * Returns the value for the requested intent slot, or $default if not
     * found
     *
     * @param string $name
     * @param mixed|null $default
     * @return mixed
     */
    public function getSlot($name, $default = null)
    {
        if (array_key_exists($name, $this->slots)) {
                return $this->slots[$name];
        }

        return $default;
    }

    // Protected Methods

    /**
     * generateSlotData()
     *
     * Iterate $this->data, attaching slot data to $this->slots[]
     *
     * @return void
     */
    protected function generateSlotData()
    {
        // Short-circuit on null
        if (!isset($this->data['request']['intent']['slots'])) {
            return;
        }

        // Iterate the slots, attaching each
        foreach ($this->data['request']['intent']['slots'] as $slotDefinition) {
            $this->attachSlot($slotDefinition);
        }
    }

    /**
     * attachSlot()
     *
     * Attach the data from the slot to $this->slots[$slotDefinition[self::KEY_SLOT_NAME]
     *
     * @param array $slotDefinition
     *
     * @return void
     */
    protected function attachSlot(array $slotDefinition)
    {
        if (isset($slotDefinition[self::KEY_SLOT_VALUE])) {
            $this->slots[$slotDefinition[self::KEY_SLOT_NAME]] = $slotDefinition[self::KEY_SLOT_VALUE];
        }
    }

    // Accessors

    /**
     * @return string
     */
    public function getIntentName()
    {
        return $this->intentName;
    }

    /**
     * @return array
     */
    public function getSlots()
    {
        return $this->slots;
    }

    // Mutators

    /**
     * @param string $intentName
     */
    public function setIntentName($intentName)
    {
        $this->intentName = (string)$intentName;
    }

    /**
     * @param array $slots
     */
    public function setSlots(array $slots)
    {
        $this->slots = $slots;
    }
}
