<?php

namespace App\Controller;

use App\Bridge\GooglePhotos\GooglePhotosClient;
use App\Bridge\Instagram\InstagramClient;
use App\Repository\GooglePhotosFactory;
use App\Repository\GooglePhotosService;
use App\Repository\InstagramFactory;
use App\Repository\InstagramService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GooglePhotosController extends AbstractController
{
    /**
     * @Route("/googlephotos", name="google_photos")
     */
    public function getGooglePhotosAlbums(Request $request)
    {
        //$access_token = $this->get('session')->get('gp_access_token');
        if ($code = $request->get('code')) {
            $apiHost = "https://www.googleapis.com/oauth2/v4/token";
            $apiData = [
                'code' => $code,
                'client_id' => "573612275516-0kn63ht3sldlc0ooouo5j48sc43gbpgl.apps.googleusercontent.com",
                'client_secret' => "JteiGtMkFkW_aKHaxtl8nEB5",
                'redirect_uri' => 'http://localhost:8000/googlephotos',
                'grant_type' => 'authorization_code'
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiHost);
            curl_setopt($ch, CURLOPT_POST, count($apiData));
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($apiData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $jsonData = json_decode(curl_exec($ch));
            curl_close($ch);

            if (isset($jsonData->access_token)) {
                $access_token = $jsonData->access_token;
                $this->get('session')->set('gp_access_token', $access_token);
            }

        }

        $access_token = $this->get('session')->get('gp_access_token');

        if (!isset($access_token)) {
            return $this->render('google_photos/googlephotosNotSet.html.twig');
        } else {
            $service = new GooglePhotosService(new GooglePhotosClient(), new GooglePhotosFactory());
            $albums = $service->getAlbums($access_token);
            return $this->render('google_photos/googlephotosAlbums.html.twig', [
                'albums' => $albums
            ]);
        }
    }

    /**
     * @Route("/googlephotos/all", name="google_photos_album_photos_all")
     */
    public function getGooglePhotosAll()
    {
        $access_token = $this->get('session')->get('gp_access_token');
        $service = new GooglePhotosService(new GooglePhotosClient(), new GooglePhotosFactory());
        $photos=$service->getAllPhotos($access_token);
        $photos = (new InstagramService(new InstagramFactory(), new InstagramClient()))->slicePhotosArray($photos);
        return $this->render('google_photos/googlePhotosAll.html.twig', [
            "photos" => $photos
        ]);
    }

    /**
     * @Route("/googlephotos/{albumId}", name="google_photos_album_photos")
     */
    public function getGooglePhotosPhotosInAlbum($albumId)
    {
        $access_token = $this->get('session')->get('gp_access_token');
        $service = new GooglePhotosService(new GooglePhotosClient(), new GooglePhotosFactory());
        $photos = $service->getPhotos($access_token, $albumId);
        $photos = (new InstagramService(new InstagramFactory(), new InstagramClient()))->slicePhotosArray($photos);
        return $this->render('google_photos/googlePhotos.html.twig', [
            "photos" => $photos,
            'album' => $albumId
        ]);
    }


    /**
     * @Route("googlephotos/{albumId}/{mediaId}", name="google_photos_single_media")
     */
    public function getGooglePhotoSingle($albumId ,$mediaId)
    {
        $access_token = $this->get('session')->get('gp_access_token');
        $service = new GooglePhotosService(new GooglePhotosClient(), new GooglePhotosFactory());
        $photo = $service->getOnePhoto($access_token, $mediaId);
        return $this->render('google_photos/googlePhotoOne.html.twig', ['photo' => $photo, 'albumId'=>$albumId]);
    }

    /**
     * @Route("googlephotos/all/{mediaId}", name="google_photos_single_media_from_all")
     */
    public function getGooglePhotoSingleFromAll($mediaId)
    {
        $access_token = $this->get('session')->get('gp_access_token');
        $service = new GooglePhotosService(new GooglePhotosClient(), new GooglePhotosFactory());
        $photo = $service->getOnePhoto($access_token, $mediaId);
        return $this->render('google_photos/googlePhotoOne.html.twig', ['photo' => $photo, 'albumId'=>'all']);
    }


}
