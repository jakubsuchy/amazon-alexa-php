<?php

namespace Alexa\Request;

use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

use Alexa\Request\BaseRequest;
use Alexa\Request\Application;
use Alexa\Request\Certificate;
use Alexa\Request\CustomSkillRequestTypes;
use Alexa\Utility\Purifier\PurifierFactory;

/**
 * Class RequestFactory
 *
 * Generate RequestInterface items
 *
 * @package Alexa\Request
 */
class RequestFactory
{

    // Constants

    const ERROR_INVALID_REQUEST_TYPE = 'Unknown Request Type: %s';
    const ERROR_VALIDATION_FAILED = 'Validation failed! Errors: %s';

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
     * @param \HTMLPurifier|null $purifier
     *
     * @return \Alexa\Request\Request
     * @throws RuntimeException
     */
    public function fromRawData(
        $rawData,
        array $expectedApplicationIds,
        Certificate $certificate = null,
        Application $application = null,
        \HTMLPurifier $purifier = null
    ) {
        // Generate purifier
        if (!$purifier) {
            $purifierFactory = new PurifierFactory();
            $purifier = $purifierFactory->generatePurifier(PurifierFactory::DEFAULT_CACHE_PATH);
        }

        // Parse data for construction
        $data = json_decode($rawData, true);

        // Perform defaults
        $application = $application ?: new Application($expectedApplicationIds, $purifier);
        $certificate = $certificate ?:
            new Certificate($_SERVER['HTTP_SIGNATURECERTCHAINURL'], $_SERVER['HTTP_SIGNATURE'], $purifier);
        $purifier = $purifier ?: PurifierFactory::generatePurifier(PurifierFactory::DEFAULT_CACHE_PATH);

        // Generate base request
        /** @var BaseRequest $request */
        $request = $this->generateRequest($data, $rawData, $purifier, $certificate, $application);

        // Validate received application ID matches client value
        $requestApplicationId = isset($data['session']['application']['applicationId']) ?
            $data['session']['application']['applicationId'] :
            null;
        $request->getApplication()->validateApplicationId($requestApplicationId);

        // Validate that the request signature matches the certificate
        $request->getCertificate()->validateRequest($rawData);

        // Perform Doctrine validation
        $this->validateRequest($request);

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
     * @param string $rawData
     * @param \HtmlPurifier $purifier
     * @param Certificate|null $certificate - Override the auto-generated Certificate with your own
     * @param Application|null $application - Override the auto-generated Application with your own
     *
     * @return mixed
     * @throws \RuntimeException - If the request type is not a valid RequestInterface class
     */
    protected function generateRequest(
        array $data,
        $rawData,
        \HTMLPurifier $purifier,
        Certificate $certificate,
        Application $application
    ) {
        // Retrieve request type
        $requestType = $purifier->purify($data['request']['type']);

        // Validate request type
        if (!in_array($requestType, array_keys(CustomSkillRequestTypes::$validTypes))) {
            throw new \RuntimeException(
                sprintf(self::ERROR_INVALID_REQUEST_TYPE, $requestType)
            );
        }

        // Retrieve the correct request child class
        $requestClass = CustomSkillRequestTypes::$validTypes[$requestType];

        // Generate request
        $request = new $requestClass($rawData, $certificate, $application, $purifier);

        // Return
        return $request;
    }

    /**
     * validateRequest()
     *
     * Perform Doctrine validation on the entity
     *
     * @param RequestInterface $request
     *
     * @throws \InvalidArgumentException
     */
    protected function validateRequest(RequestInterface $request)
    {
        // Retrieve validator
        $validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->getValidator();

        // Perform validation
        /** @var ConstraintViolationListInterface $errors */
        $errors = $validator->validate($request);

        // Build exception and throw if there were errors
        if ($errors->count()) {
            $errorString = (string) $errors;

            throw new \InvalidArgumentException(
                sprintf(self::ERROR_VALIDATION_FAILED, $errorString)
            );
        }
    }
}
