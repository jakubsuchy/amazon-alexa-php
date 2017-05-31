<?php

/**
 * @file Certificate.php
 * Validate the request signature
 * Based on code from alexa-app: https://github.com/develpr/alexa-app by Kevin Mitchell
 * */

namespace Alexa\Request;

use DateTime;
use InvalidArgumentException;

class Certificate
{
    const TIMESTAMP_VALID_TOLERANCE_SECONDS = 30;
    const SIGNATURE_VALID_PROTOCOL = 'https';
    const SIGNATURE_VALID_HOSTNAME = 's3.amazonaws.com';
    const SIGNATURE_VALID_PATH = '/echo.api/';
    const SIGNATURE_VALID_PORT = 443;
    const ECHO_SERVICE_DOMAIN = 'echo-api.amazon.com';
    const ENCRYPT_METHOD = "sha1WithRSAEncryption";

    public $requestId;
    public $timestamp;
    /** @var Session */
    public $session;
    public $certificateUrl;
    public $certificateFile;
    public $certificateContent;
    public $requestSignature;
    public $requestData;

    /**
     * @param string $certificateUrl
     * @param string $signature
     * @internal param string $certificateUri
     */
    public function __construct($certificateUrl, $signature)
    {
        $this->certificateUrl = $certificateUrl;
        $this->requestSignature = $signature;
    }

    public function validateRequest($requestData)
    {
        $requestParsed = json_decode($requestData, TRUE);
        // Validate the entire request by:

        // 1. Checking the timestamp.
        $this->validateTimestamp($requestParsed['request']['timestamp']);

        // 2. Checking if the certificate URL is correct.
        $this->verifySignatureCertificateURL();

        // 3. Checking if the certificate is not expired and has the right SAN
        $this->validateCertificate();

        // 4. Verifying the request signature
        $this->validateRequestSignature($requestData);
    }

    /**
     * Check if request is within the allowed time.
     * @param int $timestamp
     */
    public function validateTimestamp($timestamp)
    {
        $now = new DateTime;
        $timestamp = new DateTime($timestamp);
        $differenceInSeconds = $now->getTimestamp() - $timestamp->getTimestamp();

        if ($differenceInSeconds > self::TIMESTAMP_VALID_TOLERANCE_SECONDS) {
            throw new InvalidArgumentException('Request timestamp was too old. Possible replay attack.');
        }
    }

    public function validateCertificate()
    {
        $this->certificateContent = $this->getCertificate();
        $parsedCertificate = $this->parseCertificate($this->certificateContent);

        if (!$this->validateCertificateDate($parsedCertificate) || !$this->validateCertificateSAN($parsedCertificate, static::ECHO_SERVICE_DOMAIN)) {
            throw new InvalidArgumentException("The remote certificate doesn't contain a valid SANs in the signature or is expired.");
        }
    }

    /*
     * @params $requestData
     * @throws InvalidArgumentException
     */
    public function validateRequestSignature($requestData)
    {
        $certKey = openssl_pkey_get_public($this->certificateContent);

        $valid = openssl_verify($requestData, base64_decode($this->requestSignature), $certKey, self::ENCRYPT_METHOD);
        if (!$valid) {
            throw new InvalidArgumentException('Request signature could not be verified');
        }
    }

    /**
     * Returns true if the certificate is not expired.
     *
     * @param array $parsedCertificate
     * @return boolean
     */
    public function validateCertificateDate(array $parsedCertificate)
    {
        $validFrom = $parsedCertificate['validFrom_time_t'];
        $validTo = $parsedCertificate['validTo_time_t'];
        $time = time();
        return ($validFrom <= $time && $time <= $validTo);
    }

    /**
     * Returns true if the configured service domain is present/valid, false if invalid/not present
     * @param array $parsedCertificate
     * @param string $amazonServiceDomain
     * @return bool
     */
    public function validateCertificateSAN(array $parsedCertificate, $amazonServiceDomain)
    {
        if (strpos($parsedCertificate['extensions']['subjectAltName'], $amazonServiceDomain) === false) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Verify URL of the certificate
     * @throws InvalidArgumentException
     * @author Emanuele Corradini <emanuele@evensi.com>
     */
    public function verifySignatureCertificateURL()
    {
        $url = parse_url($this->certificateUrl);

        if ($url['scheme'] !== static::SIGNATURE_VALID_PROTOCOL) {
            throw new InvalidArgumentException('Protocol isn\'t secure. Request isn\'t from Alexa.');
        } else if ($url['host'] !== static::SIGNATURE_VALID_HOSTNAME) {
            throw new InvalidArgumentException('Certificate isn\'t from Amazon. Request isn\'t from Alexa.');
        } else if (strpos($url['path'], static::SIGNATURE_VALID_PATH) !== 0) {
            throw new InvalidArgumentException('Certificate isn\'t in "' . static::SIGNATURE_VALID_PATH . '" folder. Request isn\'t from Alexa.');
        } else if (isset($url['port']) && $url['port'] !== static::SIGNATURE_VALID_PORT) {
            throw new InvalidArgumentException('Port isn\'t ' . static::SIGNATURE_VALID_PORT . '. Request isn\'t from Alexa.');
        }
    }


    /**
     * Parse the X509 certificate
     * @param string $certificate The certificate contents
     * @return array
     */
    public function parseCertificate($certificate)
    {
        return openssl_x509_parse($certificate);
    }

    /**
     * Return the certificate to the underlying code by fetching it from its location.
     * Override this function if you wish to cache the certificate for a specific time.
     */
    public function getCertificate()
    {
        return $this->fetchCertificate();
    }

    /**
     * Perform the actual download of the certificate
     */
    public function fetchCertificate()
    {
        if (!function_exists("curl_init")) {
            throw new InvalidArgumentException('CURL is required to download the Signature Certificate.');
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->certificateUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $st = curl_exec($ch);
        curl_close($ch);

        // Return the certificate contents;
        return $st;
    }

}
