<?php

namespace App\Controller;

use App\Repository\ApiRepostiory;
use App\Repository\ControllerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class InstagramController extends AbstractController
{
    /**
     * @Route("/instagram", name="instagram")
     * @param Request $request
     * @return Response
     */
    public function getInstagramPhotos(Request $request)
    {
        if ($code = $request->get('code')) {

            $jsonData = (new ControllerRepository())->curlApiRequest($_ENV['IG_HOST'], $code, $_ENV['IG_REDIRECT_URI'], $_ENV['IG_CLIENT_ID'], $_ENV['IG_CLIENT_SECRET']);

            if (isset($jsonData->access_token)) {
                $access_token = $jsonData->access_token;
                $tokensArray = $this->get('session')->get('tokens');
                $tokensArray['Instagram'] = $access_token;
                $this->get('session')->set('tokens', $tokensArray);
            }


        }

        $tokens = $this->get('session')->get('tokens');

        if (false == isset($tokens['Instagram'])) {
            return $this->render('instagram/instagramNotSet.html.twig');
        } else {
            $access_token = $tokens['Instagram'];
            $apiRep = new ApiRepostiory();
            try {
                $photos = $apiRep->getApiData('instagram', $access_token);
                $controllerRep = new ControllerRepository();
                $photos = $controllerRep->castToPhotos($photos);
                $photos = $controllerRep->slicePhotosArray($photos);
                return $this->render('instagram/instagram.html.twig', [
                    'photos' => $photos
                ]);
            } catch (ClientExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface | TransportExceptionInterface $e) {
                return $this->render('main/badrequest.html.twig', ['e' => $e->getMessage()]);
            }
        }
    }

    /**
     * @Route("/instagram/{photoId}", name="instagramOnePhoto")
     */
    public function getInstagramPhoto($photoId)
    {
        $access_token = ($this->get('session')->get('tokens'))['Instagram'];
        $apiRep = new ApiRepostiory();
        $controllerRep = new ControllerRepository();
        try {
            $photo = $apiRep->getApiData('instagram/' . $photoId, $access_token);
            $photo = $controllerRep->castToPhoto($photo);
            $comments = $apiRep->getApiData('instagram/' . $photoId . "/comments", $access_token);
            $comments = $controllerRep->castToComments($comments);
            return $this->render('instagram/intagramPhoto.html.twig', [
                'photo' => $photo,
                'comments' => $comments
            ]);
        } catch (ClientExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface | TransportExceptionInterface $e) {
            return $this->render('main/badrequest.html.twig', ['e' => $e->getMessage()]);
        }
    }

}
