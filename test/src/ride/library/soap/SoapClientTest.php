<?php

namespace ride\library\soap;

use \ride\library\http\client\CurlClient;
use \ride\library\http\HttpFactory;

use \PHPUnit_Framework_TestCase;

class SoapClientTest extends PHPUnit_Framework_TestCase {

   public function testSpellChecker() {
       $httpClient = new CurlClient(new HttpFactory());

        try {
            $client = new SoapClient($httpClient, 'http://wsf.cdyne.com/SpellChecker/check.asmx?WSDL', array('trace' => TRUE));

            $param = new \stdClass();
            $param->BodyText = 'I dont like SOAP';

            $result = $client->CheckTextBodyV2($param);
        } catch (SoapFault $e) {
            $this->fail('Soap fault: ' . $e->getMessage());
        }

        $this->assertObjectHasAttribute('DocumentSummary', $result);
        $this->assertObjectHasAttribute('MisspelledWord', $result->DocumentSummary);
        $this->assertObjectHasAttribute('word', $result->DocumentSummary->MisspelledWord);
        $this->assertInternalType('string', $result->DocumentSummary->MisspelledWord->word);
        $this->assertEquals('dont', $result->DocumentSummary->MisspelledWord->word);

        $this->assertObjectHasAttribute('Suggestions', $result->DocumentSummary->MisspelledWord);
        $this->assertInternalType('array', $result->DocumentSummary->MisspelledWord->Suggestions);
        $this->assertContains("don't", $result->DocumentSummary->MisspelledWord->Suggestions);
    }

}
