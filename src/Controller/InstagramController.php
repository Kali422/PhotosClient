<?php

namespace App\Controller;

use App\Bridge\InstagramClient;
use App\Repository\ControllerRepository;
use App\Repository\Instagram\InstagramFactory;
use App\Repository\Instagram\InstagramService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
            $service = new InstagramService(new InstagramFactory(), new InstagramClient());
            $photos = $service->getPhotos($access_token);
            $photos = (new ControllerRepository())->slicePhotosArray($photos);
            return $this->render('instagram/instagram.html.twig', [
                'photos' => $photos
            ]);
        }
    }

    /**
     * @Route("/instagram/{photoId}", name="instagramOnePhoto")
     */
    public function getInstagramPhoto($photoId)
    {
        $service = new InstagramService((new InstagramFactory()), (new InstagramClient()));
        $access_token = ($this->get('session')->get('tokens'))['Instagram'];
        $photo = $service->getOnePhoto($access_token, $photoId);
        $comments = $service->getComments($access_token, $photoId);
        return $this->render('instagram/intagramPhoto.html.twig', [
            'photo' => $photo,
            'comments' => $comments
        ]);
    }

}
