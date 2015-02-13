<?php

namespace ride\library\soap;

use ride\library\http\client\Client as HttpClient;
use ride\library\http\exception\HttpException;
use ride\library\http\Response;

use \SoapClient as PhpSoapClient;

/**
 * SOAP client which uses the ride HTTP client
 */
class SoapClient extends PhpSoapClient {

    /**
     * Instance of the HTTP client
     * @var \ride\library\http\client\Client
     */
    protected $httpClient = array();

    /**
     * Construct a new SOAP client
     * @param \ride\library\http\client\Client $httpClient
     * @param string $wsdl URL to the WSDL of the service
     * @param array $options Options for the SoapClient class
     * @return null
     */
    public function __construct(HttpClient $httpClient, $wsdl, $options = array()) {
        $this->httpClient = $httpClient;

        parent::__construct($wsdl, $options);
    }

    /**
     * Performs a SOAP request
     *
     * @param string $request the xml soap request
     * @param string $location the url to request
     * @param string $action the soap action.
     * @param integer $version the soap version
     * @param integer $one_way
     * @return string the xml soap response.
     */
    public function __doRequest($request, $location, $action, $version, $one_way = 0) {
        $headers = array(
            'Connection' => 'Keep-Alive',
            'Content-Type' => 'text/xml; charset=utf-8',
            'SOAPAction' => '"' . $action . '"',
        );

        print_r($action);

        print_r(htmlspecialchars($request));

        $response = $this->httpClient->post($location, $request, $headers);

        $statusCode = $response->getStatusCode();
        if ($statusCode != Response::STATUS_CODE_OK) {
            throw new HttpException('Request returned ' . $statusCode . ': ' . $response->getStatusPhrase($statusCode) . ' - ' . $response->getBody());
        }

        return $response->getBody();
    }

}

