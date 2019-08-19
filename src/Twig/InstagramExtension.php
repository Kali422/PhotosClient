<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class InstagramExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('buildIGAuthUrl', [$this, 'buildIGAuthUrl']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('buildIGAuthUrl', [$this, 'buildIGAuthUrl']),
        ];
    }

    public function buildIGAuthUrl()
    {
        //https://api.instagram.com/oauth/authorize/?client_id=e1d115186d9f407a9c4c4ea30a7751fa&
        //redirect_uri=http://localhost:8000/instagram&
        //response_type=code

        $host = $_ENV['IG_AUTH'];
        $params = [
            'response_type' => 'code',
            'client_id' => $_ENV['IG_CLIENT_ID'],
            'redirect_uri' => $_ENV['IG_REDIRECT_URI']
        ];

        return $host . "?" . http_build_query($params);
    }
}
