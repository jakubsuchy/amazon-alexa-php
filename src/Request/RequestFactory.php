<?php

namespace Alexa\Request;

use Alexa\Request\Application;
use Alexa\Request\Certificate;
use Alexa\Request\CustomSkillRequestTypes;

/**
 * Class RequestFactory
 *
 * Generate RequestInterface items
 *
 * @package Request
 */
class RequestFactory
{
    // Constants

    const ERROR_INVALID_REQUEST_TYPE = 'Unknown Request Type: %s';

    // Factory

    /**
     * fromRawData()
     *
     * Return an instance of the correct type of Request from the raw JSON string
     *
     * @param string $rawData - The raw POST value, before json_decode
     * @param string $applicationId - Your application's ID (from the dev portal)
     * @param Certificate|null $certificate - Override the auto-generated Certificate with your own
     * @param Application|null $application - Override the auto-generated Application with your own
     *
     * @return \Alexa\Request\Request
     * @throws RuntimeException
     */
    public function fromRawData(
        $rawData,
        $applicationId,
        Certificate $certificate = null,
        Application $application = null
    ) {
        // Parse data for construction
        $data = json_decode($rawData, true);

        // Generate base request
        /** @var Request $request */
        $request = $this->generateRequest($data, $rawData, $applicationId, $certificate, $application);

        // Validate received application ID matches client value
        $request->getApplication()->validateApplicationId($data['session']['application']['applicationId']);

        // Validate that the request signature matches the certificate
        $request->getCertificate()->validateRequest($rawData);

        // Return complete request
        return $request;
    }

    // Protected Methods

    /**
     * generateRequest()
     *
     * Generate a RequestInterface object of the correct type
     *
     * @param array $data
     * @param $rawData
     * @param $applicationId
     * @param Certificate|null $certificate - Override the auto-generated Certificate with your own
     * @param Application|null $application - Override the auto-generated Application with your own
     *
     * @return mixed
     * @throws \RuntimeException - If the request type is not a valid RequestInterface class
     */
    protected function generateRequest(
        array $data,
        $rawData,
        $applicationId,
        $certificate,
        $application
    ) {
        // Retrieve request type
        $requestType = $data['request']['type'];

        // Validate request type
        if (!in_array($requestType, array_keys(CustomSkillRequestTypes::$validTypes))) {
            throw new \RuntimeException(
                sprintf(self::ERROR_INVALID_REQUEST_TYPE, $requestType)
            );
        }

        // Retrieve the correct request child class
        $requestClass = CustomSkillRequestTypes::$validTypes[$requestType];

        // Generate request
        $request = new $requestClass($rawData, $applicationId, $certificate, $application);

        // Return
        return $request;
    }
}
