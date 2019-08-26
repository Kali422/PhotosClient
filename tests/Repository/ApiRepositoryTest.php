<?php


namespace App\Tests\Repository;


use App\Repository\ApiRepostiory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpClient\Exception\ClientException;

class ApiRepositoryTest extends TestCase
{
    /**
     * @dataProvider provider
     */
    function testGetApiData($endpoint, $access_token, $expected)
    {
        if (false == $expected) {
            $this->expectException(ClientException::class);
        }
        $result = (new ApiRepostiory())->getApiData($endpoint, $access_token);
        self::assertJson(json_encode($result));
    }

    function provider()
    {
        $dotenv = new Dotenv(true);
        $dotenv->load('/var/www/html/Photos/PhotosClient/.env.test');
        $dotenv->load('/var/www/html/Photos/PhotosClient/.env');

        return [
            ['instagram', $_ENV['IG_ACCESS_TOKEN'], true],
            ['instagram/' . $_ENV['IG_MEDIA_ID'], $_ENV['IG_ACCESS_TOKEN'], true],
            ['instagram/' . $_ENV['IG_MEDIA_ID'] . '/comments', $_ENV['IG_ACCESS_TOKEN'], true],
            ['instagram', '123', false],
            ['instagram/' . $_ENV['IG_MEDIA_ID'], '123', false],
            ['instagram/' . '123', '/comments', false],
            ['googlephotos/albums', $_ENV['GP_ACCESS_TOKEN'], true],
            ['googlephotos/photos/' . $_ENV['GP_MEDIA_ID'], $_ENV['GP_ACCESS_TOKEN'], true],
            ['googlephotos/photos', $_ENV['GP_ACCESS_TOKEN'], true],
            ['googlephotos/albums/' . $_ENV['GP_ALBUM_ID'], $_ENV['GP_ACCESS_TOKEN'], true],
            ['googlephotos/albums', $_ENV['GP_ACCESS_TOKEN'] . '123', false],
            ['googlephotos/photos/' . '123', $_ENV['GP_ACCESS_TOKEN'], false],
            ['googlephotos/photos', '123', false],
            ['googlephotos/albums/' . '123', $_ENV['GP_ACCESS_TOKEN'], false]
        ];
    }


}