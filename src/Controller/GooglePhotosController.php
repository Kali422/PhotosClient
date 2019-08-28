<?php

namespace App\Controller;

use App\Repository\ApiRepostiory;
use App\Repository\ControllerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;


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
                $tokensArray = $this->get('session')->get('tokens');
                $tokensArray['GooglePhotos'] = $access_token;
                $this->get('session')->set('tokens', $tokensArray);
            }

        }

        $tokens = $this->get('session')->get('tokens');

        if (!isset($tokens['GooglePhotos'])) {
            return $this->render('google_photos/googlephotosNotSet.html.twig');
        } else {
            $access_token = $tokens['GooglePhotos'];
            $apiRep = new ApiRepostiory();
            try {
                $albums = $apiRep->getApiData('googlephotos/albums', $access_token);
                $conRep = new ControllerRepository();
                $albums = $conRep->castToAlbums($albums);
                return $this->render('google_photos/googlephotosAlbums.html.twig', [
                    'albums' => $albums
                ]);
            } catch (ClientExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface | TransportExceptionInterface $e) {
                return $this->render('main/badrequest.html.twig', ['e' => $e->getMessage()]);
            }
        }
    }

    /**
     * @Route("/googlephotos/all", name="google_photos_album_photos_all")
     */
    public function getGooglePhotosAll()
    {
        $access_token='';

        if (isset(($this->get('session')->get('tokens'))['GooglePhotos'])) {
            $access_token = ($this->get('session')->get('tokens'))['GooglePhotos'];
        }
        $apiRep = new ApiRepostiory();
        try {
            $photos = $apiRep->getApiData('googlephotos/photos', $access_token);
            $controllerRep = new ControllerRepository();
            $photos = $controllerRep->castToPhotos($photos);
            $photos = $controllerRep->slicePhotosArray($photos);
            return $this->render('google_photos/googlePhotosAll.html.twig', [
                "photos" => $photos
            ]);
        } catch (ClientExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface | TransportExceptionInterface $e) {
            return $this->render('main/badrequest.html.twig', ['e' => $e->getMessage()]);
        }
    }

    /**
     * @Route("/googlephotos/{albumId}", name="google_photos_album_photos")
     */
    public function getGooglePhotosPhotosInAlbum($albumId)
    {
        $access_token='';

        if (isset(($this->get('session')->get('tokens'))['GooglePhotos'])) {
            $access_token = ($this->get('session')->get('tokens'))['GooglePhotos'];
        }
        $apiRep = new ApiRepostiory();
        try {
            $photos = $apiRep->getApiData('googlephotos/albums/' . $albumId, $access_token);
            $controllerRep = new ControllerRepository();
            $photos = $controllerRep->castToPhotos($photos);
            $photos = $controllerRep->slicePhotosArray($photos);
            return $this->render('google_photos/googlePhotos.html.twig', [
                "photos" => $photos,
                'album' => $albumId
            ]);
        } catch (ClientExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface | TransportExceptionInterface $e) {
            return $this->render('main/badrequest.html.twig', ['e' => $e->getMessage()]);
        }
    }


    /**
     * @Route("googlephotos/{albumId}/{mediaId}", name="google_photos_single_media")
     */
    public function getGooglePhotoSingle($albumId, $mediaId)
    {
        $access_token='';

        if (isset(($this->get('session')->get('tokens'))['GooglePhotos'])) {
            $access_token = ($this->get('session')->get('tokens'))['GooglePhotos'];
        }
        $apiRep = new ApiRepostiory();
        try {
            $photo = $apiRep->getApiData('googlephotos/photos/' . $mediaId, $access_token);
            $controllerRep = new ControllerRepository();
            $photo = $controllerRep->castToPhoto($photo);
            return $this->render('google_photos/googlePhotoOne.html.twig', ['photo' => $photo, 'albumId' => $albumId]);
        } catch (ClientExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface | TransportExceptionInterface $e) {
            return $this->render('main/badrequest.html.twig', ['e' => $e->getMessage()]);
        }
    }

    /**
     * @Route("googlephotos/all/{mediaId}", name="google_photos_single_media_from_all")
     */
    public function getGooglePhotoSingleFromAll($mediaId)
    {
        $access_token='';
        if (isset(($this->get('session')->get('tokens'))['GooglePhotos'])) {
            $access_token = ($this->get('session')->get('tokens'))['GooglePhotos'];
        }
        $apiRep = new ApiRepostiory();
        try {
            $photo = $apiRep->getApiData('googlephotos/photos/' . $mediaId, $access_token);
            $controllerRep = new ControllerRepository();
            $photo = $controllerRep->castToPhoto($photo);
            return $this->render('google_photos/googlePhotoOne.html.twig', ['photo' => $photo, 'albumId' => 'all']);
        } catch (ClientExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface | TransportExceptionInterface $e) {
            return $this->render('main/badrequest.html.twig', ['e' => $e->getMessage()]);
        }
    }


}
