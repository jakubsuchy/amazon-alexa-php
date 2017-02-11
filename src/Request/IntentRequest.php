<?php

namespace Alexa\Request;

/**
 * Class IntentRequest
 * @package Alexa\Request
 */
class IntentRequest extends Request implements RequestInterface
{
    // Fields

    /**
     * @var string
     */
    public $intentName;
    /**
     * @var array
     */
    public $slots = array();

    // Hooks

    /**
     * IntentRequest()
     *
     * @param string $rawData - The original JSON response, before json_decode
     * @param string $applicationId - Your Alexa Dev Portal application ID
     * @param Certificate|null $certificate - Override the auto-generated Certificate with your own
     * @param Application|null $application - Override the auto-generated Application with your own
     */
    public function __construct($rawData, $applicationId, $certificate = null, $application = null)
    {
        // Parent construct
        parent::__construct($rawData, $applicationId, $certificate, $application);

        // Retrieve intent name
        $this->intentName = $this->data['request']['intent']['name'];

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
        foreach ($this->data['request']['intent']['slots'] as $slot) {
            $this->attachSlot($slot);
        }
    }

    /**
     * attachSlot()
     *
     * Attach the data from the slot to $this->slots[$slot['name']
     *
     * @param array $slot
     *
     * @return void
     */
    protected function attachSlot(array $slot)
    {
        if (isset($slot['value'])) {
            $this->slots[$slot['name']] = $slot['value'];
        }
    }
}
