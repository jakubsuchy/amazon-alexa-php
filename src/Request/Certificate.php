<?php

namespace Alexa\Request;

use Symfony\Component\Validator\Constraints as Assert;

use Alexa\Utility\Purifier\HasPurifier;

/**
 * Class Certificate
 * 
 * Represents an Amazon HTTPS certificate
 *
 * Based on code from alexa-app: https://github.com/develpr/alexa-app by Kevin Mitchell
 * 
 * @package Alexa\Request
 */
class Certificate
{
    // Traits

    use HasPurifier;

    // Constants
    
    const TIMESTAMP_VALID_TOLERANCE_SECONDS = 30;
    const SIGNATURE_VALID_PROTOCOL = 'https';
    const SIGNATURE_VALID_HOSTNAME = 's3.amazonaws.com';
    const SIGNATURE_VALID_PATH = '/echo.api/';
    const SIGNATURE_VALID_PORT = 443;
    const ECHO_SERVICE_DOMAIN = 'echo-api.amazon.com';
    const ENCRYPT_METHOD = "sha1WithRSAEncryption";

    const ERROR_EXPIRED_SIGNATURE = 'The remote certificate signature is expired.';
    const ERROR_INVALID_SAN = 'The remote certificate SAN is invalid';
    const ERROR_REQUEST_EXPIRED = 'Request timestamp was too old. Possible replay attack.';
    const ERROR_PROTOCOL_NOT_HTTPS = 'Protocol isn\'t secure. Request isn\'t from Alexa.';
    const ERROR_NON_AMAZON_CERTIFICATE = 'Certificate isn\'t from Amazon. Request isn\'t from Alexa.';
    const ERROR_INVALID_PATH = 'Certificate isn\'t in "' . self::SIGNATURE_VALID_PATH . '" folder. Request isn\'t ' .
        'from Alexa.';
    const ERROR_INVALID_PORT = 'Port isn\'t ' . self::SIGNATURE_VALID_PORT. '. Request isn\'t from Alexa.';
    const ERROR_CURL_REQUIRED = 'CURL is required to download the Signature Certificate.';
    const ERROR_INVALID_SIGNATURE = 'Request signature could not be verified';

    // Fields

    /**
     * @var string
     *
     * @Assert\Url
     * @Assert\NotBlank
     */
    private $certificateUrl;
    /**
     * @var string
     *
     * @Assert\Type("string")
     * @Assert\NotBlank
     */
    private $requestSignature;
    /**
     * @var string
     *
     * @Assert\Type("string")
     * @Assert\NotBlank
     */
    private $certificateContent;

    // Hooks

    /**
     * @param string $certificateUrl
     * @param string $signature
     * @param \HTMLPurifier $purifier
     */
    public function __construct($certificateUrl, $signature, \HTMLPurifier $purifier)
    {
        // Set purifier
        $this->setPurifier($purifier);

        // Set certificate URL and request signature
        $this->setCertificateUrl($certificateUrl);
        $this->setRequestSignature($signature);
    }

    // Public Methods

    /**
     * validateRequest()
     *
     * Verify the HTTPS certificate information provided in the raw request is a valid Amazon certificate
     *
     * @throws \InvalidArgumentException
     */
    public function validateRequest($rawRequestData)
    {
        // Parse the JSON
        $requestData = json_decode($rawRequestData, true);

        // Validate the entire request by:

        // 1. Checking the timestamp.
        $this->validateTimestamp($requestData['request']['timestamp']);

        // 2. Checking if the certificate URL is correct.
        $this->verifySignatureCertificateURL();

        // 3. Checking if the certificate is not expired and has the right SAN
        $this->validateCertificate();

        // 4. Verifying the request signature
        $this->validateRequestSignature($rawRequestData);
    }

    // Protected Methods

    /**
     * validateTimestamp()
     *
     * Check if request is within the allowed time
     *
     * @throws \InvalidArgumentException
     */
    protected function validateTimestamp($timestamp)
    {
        // Generate DateTimes
        $currentDateTime = new \DateTime();
        $timestamp = new \DateTime($timestamp);

        // Compare
        $differenceInSeconds = $currentDateTime->getTimestamp() - $timestamp->getTimestamp();
        if ($differenceInSeconds > self::TIMESTAMP_VALID_TOLERANCE_SECONDS) {
            throw new \InvalidArgumentException(self::ERROR_REQUEST_EXPIRED);
        }
    }

    /**
     * verifySignatureCertificateURL()
     *
     * Verify URL of the certificate
     *
     * @throws \InvalidArgumentException
     * @author Emanuele Corradini <emanuele@evensi.com>
     */
    protected function verifySignatureCertificateURL()
    {
        // Parse URL
        $parsedUrl = parse_url($this->certificateUrl);

        // Check HTTPS
        if ($parsedUrl['scheme'] !== self::SIGNATURE_VALID_PROTOCOL) {
            throw new \InvalidArgumentException(self::ERROR_PROTOCOL_NOT_HTTPS);
        }
        
        // Check host
        if ($parsedUrl['host'] !== self::SIGNATURE_VALID_HOSTNAME) {
            throw new \InvalidArgumentException(self::ERROR_NON_AMAZON_CERTIFICATE);
        } 
        
        // Check path
        if (strpos($parsedUrl['path'], self::SIGNATURE_VALID_PATH) !== 0) {
            throw new \InvalidArgumentException(self::ERROR_INVALID_PATH);
        }

        // Check port
        if (isset($parsedUrl['port']) && $parsedUrl['port'] !== self::SIGNATURE_VALID_PORT) {
            throw new \InvalidArgumentException(self::ERROR_INVALID_PORT);
        }
    }

    /**
     * validateCertificate()
     *
     * @throws \InvalidArgumentException
     */
    protected function validateCertificate()
    {
        // Retrieve actual certificate
        $this->certificateContent = $this->retrieveCertificate();

        // Parse
        $parsedCertificate = $this->parseCertificate($this->certificateContent);

        // Check expiration
        if (!$this->validateCertificateDate($parsedCertificate)) {
            throw new \InvalidArgumentException(self::ERROR_EXPIRED_SIGNATURE);
        }

        // Check SAN
        if (!$this->validateCertificateSan($parsedCertificate, self::ECHO_SERVICE_DOMAIN)) {
            throw new \InvalidArgumentException(self::ERROR_INVALID_SAN);
        }
    }

    /**
     * retrieveCertificate()
     *
     * Return the certificate to the underlying code by fetching it from its location.
     * Override this function if you wish to cache the certificate for a specific time.
     */
    protected function retrieveCertificate()
    {
        return $this->downloadCertificateFromAmazon();
    }

    /**
     * fetchCertificate()
     *
     * Download the certificate from the provided $certificateUrl at Amazon
     */
    protected function downloadCertificateFromAmazon()
    {
        // Check for CURL
        if (!function_exists("curl_init")) {
            throw new \InvalidArgumentException(self::ERROR_CURL_REQUIRED);
        }

        // Perform CURL
        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_URL, $this->certificateUrl);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
        $certificate = curl_exec($curlHandle);
        curl_close($curlHandle);

        // Return the certificate contents
        return $certificate;
    }

    /**
     * parseCertificate()
     *
     * Parse the X509 certificate
     *
     * @param $certificate - The certificate contents
     *
     * @return bool
     */
    protected function parseCertificate($certificate)
    {
        return openssl_x509_parse($certificate);
    }


    /**
     * validateCertificateDate()
     *
     * Returns true if the certificate is not expired.
     *
     * @param array $parsedCertificate
     * @return boolean
     */
    protected function validateCertificateDate(array $parsedCertificate)
    {
        $validFrom = $parsedCertificate['validFrom_time_t'];
        $validTo = $parsedCertificate['validTo_time_t'];
        $time = time();

        return ($validFrom <= $time && $time <= $validTo);
    }

    /**
     * validateCertificateSan()
     *
     * Returns true if the configured service domain is present/valid, false if invalid/not present
     *
     * @param array $parsedCertificate
     * @param string $amazonServiceDomain
     *
     * @return bool
     */
    protected function validateCertificateSan(array $parsedCertificate, $amazonServiceDomain)
    {
        return strpos($parsedCertificate['extensions']['subjectAltName'], $amazonServiceDomain) !== false;
    }

    /**
     * validateRequestSignature()
     *
     * @params $requestData 
     * @throws \InvalidArgumentException
     */
    protected function validateRequestSignature($requestData)
    {
        $certificateKey = openssl_pkey_get_public($this->certificateContent);

        $valid = openssl_verify($requestData, base64_decode($this->requestSignature), $certificateKey, self::ENCRYPT_METHOD);

        if (!$valid) {
            throw new \InvalidArgumentException(self::ERROR_INVALID_SIGNATURE);
        }
    }

    // Accessors

    /**
     * @return string
     */
    public function getCertificateUrl()
    {
        return $this->certificateUrl;
    }


    /**
     * @return string
     */
    public function getCertificateContent()
    {
        return $this->certificateContent;
    }

    /**
     * @return string
     */
    public function getRequestSignature()
    {
        return $this->requestSignature;
    }

    // Mutators

    /**
     * @param string $certificateUrl
     */
    protected function setCertificateUrl($certificateUrl)
    {
        $this->certificateUrl = $certificateUrl ? (string)$certificateUrl : null;
    }

    /**
     * @param string $requestSignature
     */
    protected function setRequestSignature($requestSignature)
    {
        $this->requestSignature = $requestSignature ? (string)$requestSignature : null;
    }

    /**
     * @param string $certificateContent
     */
    protected function setCertificateContent($certificateContent)
    {
        $this->certificateContent = $certificateContent ? (string)$certificateContent : null;
    }
}
