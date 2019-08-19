<?php

namespace App\Controller;

use App\Bridge\GooglePhotosClient;
use App\Bridge\InstagramClient;
use App\Repository\ControllerRepository;
use App\Repository\GooglePhotos\GooglePhotosFactory;
use App\Repository\GooglePhotos\GooglePhotosService;
use App\Repository\Instagram\InstagramFactory;
use App\Repository\Instagram\InstagramService;
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
        if ($code = $request->get('code')) {
            $jsonData = (new ControllerRepository())->curlApiRequest($_ENV['GP_HOST'], $code, $_ENV['GP_REDIRECT_URI'], $_ENV['GP_CLIENT_ID'], $_ENV['GP_CLIENT_SECRET']);

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
    public function getGooglePhotoSingle($albumId='all',$mediaId)
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
