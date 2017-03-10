<?php

namespace Alexa\Request;

use Alexa\Request\Application;
use Alexa\Request\Certificate;
use Alexa\Request\CustomSkillRequestTypes;
use Alexa\Utility\PurifierHelper;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\RecursiveValidator;
use Symfony\Component\Validator\ValidatorBuilder;

/**
 * Class RequestFactory
 *
 * Generate RequestInterface items
 *
 * @package Request
 */
class RequestFactory
{
    // Traits

    use PurifierHelper;

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
        $applicationId,
        Certificate $certificate = null,
        Application $application = null,
        \HTMLPurifier $purifier = null
    ) {
        // Generate purifier
        $purifier = $purifier ?: $this->getPurifier();

        // Parse data for construction
        $data = json_decode($rawData, true);

        // Generate base request
        /** @var Request $request */
        $request = $this->generateRequest($data, $rawData, $applicationId, $purifier, $certificate, $application);

        // Validate received application ID matches client value
        $request->getApplication()->validateApplicationId($data['session']['application']['applicationId']);

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
     * @param $rawData
     * @param $applicationId
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
        $applicationId,
        \HTMLPurifier $purifier,
        $certificate,
        $application
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
        $request = new $requestClass($rawData, $applicationId, $certificate, $application, $purifier);

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
