<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class GooglePhotosExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('buildGPAuthUrl', [$this, 'buildGPAuthUrl']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('buildGPAuthUrl', [$this, 'buildGPAuthUrl']),
        ];
    }

    public function buildGPAuthUrl()
    {
        //https://accounts.google.com/o/oauth2/v2/auth?response_type=code&
        //client_id=573612275516-0kn63ht3sldlc0ooouo5j48sc43gbpgl.apps.googleusercontent.com&
        //redirect_uri=http://localhost:8000/googlephotos&scope=https://www.googleapis.com/auth/photoslibrary.readonly"#}
        $host = $_ENV['GP_AUTH'];
        $params = [
            'response_type' => 'code',
            'client_id' => $_ENV['GP_CLIENT_ID'],
            'redirect_uri' => $_ENV['GP_REDIRECT_URI'],
            'scope' => $_ENV['GP_SCOPE']
        ];

        return $host . "?" . http_build_query($params);

    }

}
