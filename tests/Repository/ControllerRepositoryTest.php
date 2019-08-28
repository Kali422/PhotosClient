<?php


namespace App\Tests\Repository;


use App\Repository\ControllerRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Dotenv\Dotenv;

class ControllerRepositoryTest extends TestCase
{
    private $controller;

    function setUp()
    {
        $this->controller = new ControllerRepository();

    }

    /**
     * @dataProvider provider
     */
    function testCurlApiRequest($host, $code, $redirectUrl, $clientId, $clientSecret, $expectedJson)
    {
        $result = $this->controller->curlApiRequest($host, $code, $redirectUrl, $clientId, $clientSecret);
        if ($expectedJson) {
            self::assertInstanceOf(\stdClass::class, $result);
        } else self::assertEquals(null, $result);
    }

    function provider()
    {
        $dotenv = new Dotenv(true);
        $dotenv->load('/var/www/html/Photos/PhotosClient/.env');
        $dotenv->load('/var/www/html/Photos/PhotosClient/.env.test');

        return [
            [$_ENV['IG_HOST'], '123', $_ENV['IG_REDIRECT_URI'], $_ENV['IG_CLIENT_ID'], $_ENV['IG_CLIENT_SECRET'], true],
            ['abc', '123', $_ENV['IG_REDIRECT_URI'], $_ENV['IG_CLIENT_ID'], $_ENV['IG_CLIENT_SECRET'], false],
            [$_ENV['GP_HOST'], 1, 2, 3, 4, true],
            [0,1,2,3,4,false]
        ];
    }

}