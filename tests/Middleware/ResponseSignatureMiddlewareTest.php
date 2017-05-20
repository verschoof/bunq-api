<?php

namespace Bunq\Tests\Middleware;

use Bunq\Certificate\DefaultCertificate;
use Bunq\Middleware\ResponseSignatureMiddleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

final class ResponseSignatureMiddlewareTest extends TestCase
{
    /**
     * @var ResponseSignatureMiddleware()
     */
    private $middleware;

    public function setUp()
    {
        $publicKey = '-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAsP0BRYxC0A9v0C9aJ2pf
fKyeCQ9ao7gvd0xkVlx0lFy5/UroMHUp2AmI4dS6s+wEu1Xzl5i4XpqngZKWXp0p
4FluQGq+rUtdxSVXwgrUUqGGvCAfk0N0izCa5R9/7RoQaFpJ+Qa9Z/LoE1UiiEld
IGi9dy/jFLV9KglT8Z08Fa/tss1i8QJsnQ51/Q1B4RjoZTAoPGtKJRzvNUdAeogd
HYorGLMxw9ixfQnQt8Anqyor6U7sgN/xfnj68KCP9I2eVA6pO0IZgpUzv8FcNKjx
kxtpEjh028OqCJOenun8lZf+iy1VmsICjG82fZnR9a3sK697doVza9Y1ZnibVQ5w
ywIDAQAB
-----END PUBLIC KEY-----';

        $certificate      = DefaultCertificate::fromString($publicKey);
        $this->middleware = new ResponseSignatureMiddleware($certificate);
    }

    /**
     * @test
     */
    public function itValidatesSignature()
    {
        $headers = [
            'Date'                        => 'Sat, 20 May 2017 10:40:41 GMT',
            'Server'                      => 'Apache',
            'Access-Control-Allow-Origin' => '*',
            'Cache-Control'               => 'max-age=31536000',
            'X-Bunq-Client-Response-Id'   => 'd93e60ce-8de7-4c8c-b911-9614e63ef1db',
            'X-Bunq-Client-Request-Id'    => '7c54ccde-89ab-4546-ad91-c6af28ed535a',
            'X-Bunq-Server-Signature'     => 'YfMLJ/Rh1n46epfA/qY9dG5dwWk2sIY2W+ceSOR0yH17ifX9Ppx7KtSSgng3NNXdQksBN0LMa2N2BU/uN6fONP+p3K7YpAOV6nGJfF4DqIi4jcL6OpngLLSNG1EX5PJx3FsFHhr7fwp0Bn0pJcgiJqFW6sea9ChoZsMpCUaeCC4jU2MM4LooQM8yBOUYg70+LLlzHhj3C1GhiEzDnw1aEaLDpRGdMK2JeCi0XuAAwB+JvT7TaOwjSvdgT4dHJY8Vo55A3cTpi8niIqQlaUy37DxPmHbSFW9gbki9tUn0+zmT6xJhZutzCGa/W/bqnGSMtsOnybbBFsabiGHOOlBb8g==',
            'X-Frame-Options'             => 'SAMEORIGIN',
            'ETag'                        => '94930c03a34cd0b94ac1361bcc9f8a7163df238c599eb2ba9163afde1ef229f7',
            'Transfer-Encoding'           => 'chunked',
            'Content-Type'                => 'application/json',
            'Strict-Transport-Security'   => 'max-age=31536000;',
        ];

        $body = '{"Response":[{"Payment":{"id":17495,"created":"2017-04-17 11:01:15.347501","updated":"2017-04-17 11:01:15.347501","monetary_account_id":2030,"amount":{"currency":"EUR","value":"-11.05"},"description":"invoice 1629","type":"BUNQ","merchant_reference":null,"alias":{"iban":"NL37BUNQ9900020189","is_light":false,"display_name":"Linville Co\u00f6peratie","avatar":{"uuid":"465f0bb5-b103-4c0d-ba97-a91b37b7f405","image":[{"attachment_public_uuid":"26031605-c8e9-462a-8117-6edeb61f5d7c","height":1024,"width":1024,"content_type":"image\/png"}],"anchor_uuid":null},"label_user":{"uuid":"79963d66-1561-48a8-8233-a89e4a46a9d3","display_name":"Linville Co\u00f6peratie","country":"NL","avatar":{"uuid":"646d3802-881e-415c-96f9-35397ce7cc9a","image":[{"attachment_public_uuid":"0d10b1c4-b249-4c49-a442-5379bae0a030","height":640,"width":640,"content_type":"image\/png"}],"anchor_uuid":"79963d66-1561-48a8-8233-a89e4a46a9d3"},"public_nick_name":"Linville Co\u00f6peratie"},"country":"NL"},"counterparty_alias":{"iban":"NL37BUNQ9900000013","is_light":false,"display_name":"Catrice (nickname)","avatar":{"uuid":"0f56a33b-a451-48b4-8d4a-3d8a34c15fa1","image":[{"attachment_public_uuid":"5c598364-ab1b-42d7-9a79-91f872f333c1","height":1024,"width":1024,"content_type":"image\/png"}],"anchor_uuid":null},"label_user":{"uuid":"8de50b32-8b97-481f-85a7-aa4e751803f2","display_name":"Catrice (nickname)","country":"NL","avatar":{"uuid":"3552dc00-b8b8-4edc-9163-515647c75d4a","image":[{"attachment_public_uuid":"eca34771-55cc-42c3-b7b4-8f1a978b6624","height":480,"width":480,"content_type":"image\/jpeg"}],"anchor_uuid":"8de50b32-8b97-481f-85a7-aa4e751803f2"},"public_nick_name":"Catrice (nickname)"},"country":"NL"},"attachment":[],"geolocation":null,"batch_id":null,"allow_chat":true,"scheduled_id":null,"address_billing":null,"address_shipping":null}}]}';

        $response = new Response(200, $headers, $body);

        $result = $this->middleware->__invoke($response);

        $this->assertEquals($response, $result);
    }

    /**
     * @test
     * @expectedException \Exception
     * @expectedExceptionMessage Server signature does not match response
     */
    public function itThrowsAnExpectionWhenSignatureDoesNotMatch()
    {
        $headers = [
            'Date'                        => 'Sat, 20 May 2017 10:40:41 GMT',
            'Server'                      => 'Apache',
            'Access-Control-Allow-Origin' => '*',
            'Cache-Control'               => 'max-age=31536000',
            'X-Bunq-Client-Response-Id'   => 'd93e60ce-8de7-4c8c-b911-9614e63ef1db',
            'X-Bunq-Client-Request-Id'    => '7c54ccde-89ab-4546-ad91-c6af28ed535a',
            'X-Bunq-Server-Signature'     => 'YfMLJ/Rh1n46epfA/qY9dG5dwWk2sIY2W+ceSOR0yH17ifX9Ppx7KtSSgng3NNXdQksBN0LMa2N2BU/uN6fONP+p3K7YpAOV6nGJfF4DqIi4jcL6OpngLLSNG1EX5PJx3FsFHhr7fwp0Bn0pJcgiJqFW6sea9ChoZsMpCUaeCC4jU2MM4LooQM8yBOUYg70+LLlzHhj3C1GhiEzDnw1aEaLDpRGdMK2JeCi0XuAAwB+JvT7TaOwjSvdgT4dHJY8Vo55A3cTpi8niIqQlaUy37DxPmHbSFW9gbki9tUn0+zmT6xJhZutzCGa/W/bqnGSMtsOnybbBFsabiGHOOlBb8g==',
            'X-Frame-Options'             => 'SAMEORIGIN',
            'ETag'                        => '94930c03a34cd0b94ac1361bcc9f8a7163df238c599eb2ba9163afde1ef229f7',
            'Transfer-Encoding'           => 'chunked',
            'Content-Type'                => 'application/json',
            'Strict-Transport-Security'   => 'max-age=31536000;',
        ];

        $body = '{"Response":[{"Payment":{"id":17495,"created":"2017-04-17 11:01:15.347501","updated":"2017-04-17 11:01:15.347501","monetary_account_id":2030,"amount":{"currency":"EUR","value":"-11.05"},"description":"invoice 1629","type":"BUNQ","merchant_reference":null,"alias":{"iban":"NL37BUNQ9900020189","is_light":false,"display_name":"Man in the middle attack!","avatar":{"uuid":"465f0bb5-b103-4c0d-ba97-a91b37b7f405","image":[{"attachment_public_uuid":"26031605-c8e9-462a-8117-6edeb61f5d7c","height":1024,"width":1024,"content_type":"image\/png"}],"anchor_uuid":null},"label_user":{"uuid":"79963d66-1561-48a8-8233-a89e4a46a9d3","display_name":"Linville Co\u00f6peratie","country":"NL","avatar":{"uuid":"646d3802-881e-415c-96f9-35397ce7cc9a","image":[{"attachment_public_uuid":"0d10b1c4-b249-4c49-a442-5379bae0a030","height":640,"width":640,"content_type":"image\/png"}],"anchor_uuid":"79963d66-1561-48a8-8233-a89e4a46a9d3"},"public_nick_name":"Linville Co\u00f6peratie"},"country":"NL"},"counterparty_alias":{"iban":"NL37BUNQ9900000013","is_light":false,"display_name":"Catrice (nickname)","avatar":{"uuid":"0f56a33b-a451-48b4-8d4a-3d8a34c15fa1","image":[{"attachment_public_uuid":"5c598364-ab1b-42d7-9a79-91f872f333c1","height":1024,"width":1024,"content_type":"image\/png"}],"anchor_uuid":null},"label_user":{"uuid":"8de50b32-8b97-481f-85a7-aa4e751803f2","display_name":"Catrice (nickname)","country":"NL","avatar":{"uuid":"3552dc00-b8b8-4edc-9163-515647c75d4a","image":[{"attachment_public_uuid":"eca34771-55cc-42c3-b7b4-8f1a978b6624","height":480,"width":480,"content_type":"image\/jpeg"}],"anchor_uuid":"8de50b32-8b97-481f-85a7-aa4e751803f2"},"public_nick_name":"Catrice (nickname)"},"country":"NL"},"attachment":[],"geolocation":null,"batch_id":null,"allow_chat":true,"scheduled_id":null,"address_billing":null,"address_shipping":null}}]}';

        $response = new Response(200, $headers, $body);

        $result = $this->middleware->__invoke($response);

        $this->assertEquals($response, $result);
    }
}

