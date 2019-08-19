<?php

namespace App\Controller;

use App\Bridge\Instagram\InstagramClient;
use App\Repository\InstagramFactory;
use App\Repository\InstagramService;
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
            $apiData = array(
                'client_id' => "e1d115186d9f407a9c4c4ea30a7751fa",
                'client_secret' => "f62f9c322b384c9cadf01e394c1052c2",
                'grant_type' => 'authorization_code',
                'redirect_uri' => "http://localhost:8000/instagram",
                'code' => $code
            );

            $apiHost = 'https://api.instagram.com/oauth/access_token';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiHost);
            curl_setopt($ch, CURLOPT_POST, count($apiData));
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($apiData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $jsonData = curl_exec($ch);
            curl_close($ch);


            $user = json_decode($jsonData);
            if (isset($user->access_token)) {
                $access_token = $user->access_token;
                $this->get('session')->set('ig_access_token', $access_token);
            }


        }

        $access_token = $this->get('session')->get('ig_access_token');

        if (null == $access_token) {
            return $this->render('instagram/instagramNotSet.html.twig');
        } else {
            //wyswietlzdjecia

            $service = new InstagramService(new InstagramFactory(), new InstagramClient());
            $photos = $service->getPhotos($access_token);
            $photos = $service->slicePhotosArray($photos);
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
        $access_token = $this->get('session')->get('ig_access_token');
        $photo = $service->getOnePhoto($access_token, $photoId);
        $comments = $service->getComments($access_token, $photoId);
        return $this->render('instagram/intagramPhoto.html.twig', [
            'photo' => $photo,
            'comments' => $comments
        ]);
    }

}
