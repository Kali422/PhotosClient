<?php

namespace App\Controller;

use App\Repository\ApiRepostiory;
use App\Repository\ControllerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main")
     */
    public function index()
    {
        return $this->render('main/index.html.twig');
    }


    /**
     * @Route("/disconnect", name="disconnect")
     */
    public function disconnect(Request $request)
    {
        $what = $request->get('what');
        switch ($what) {
            case 'instagram':
                $token = ($this->get('session')->get('tokens'))['Instagram'];
                if (isset($token)) {
                    $tokensArray = $this->get('session')->get('tokens');
                    unset($tokensArray['Instagram']);
                    $this->get('session')->set('tokens', $tokensArray);
                    $this->addFlash('success', 'Successfully disconnected from Instagram');
                } else {
                    $this->addFlash('warning', 'Something went wrong, maybe you are already disconnected');
                }
                break;
            case 'googlephotos':
                $token = ($this->get('session')->get('tokens'))['GooglePhotos'];
                if (isset($token)) {
                    $tokensArray = $this->get('session')->get('tokens');
                    unset($tokensArray['GooglePhotos']);
                    $this->get('session')->set('tokens', $tokensArray);
                    $this->addFlash('success', 'Successfully disconnected from Google Photos');
                } else {
                    $this->addFlash('warning', 'Something went wrong, maybe you are already disconnected');
                }
                break;
            default:
                break;
        }
        return $this->render('main/disconnect.html.twig');
    }

    /**
     * @Route("/all", name="displayAll")
     */
    public function displayAll()
    {
        $tokens = $this->get('session')->get('tokens');
        if (false == isset($tokens['GooglePhotos']) && false == isset($tokens['Instagram'])) {
            return $this->render('main/noPhotos.html.twig');
        } else {
            $apiRep = new ApiRepostiory();
            $controllerRep = new ControllerRepository();
            $photos = [];
            $IGphotos = [];
            $GPphotos = [];
            if (isset($tokens['Instagram'])) {
                $access_token = $tokens['Instagram'];
                $IGphotos = $apiRep->getApiData('instagram', $access_token);
                $IGphotos = $controllerRep->castToPhotos($IGphotos);
                $IGphotos = $controllerRep->slicePhotosArray($IGphotos);
                array_push($photos, ['name' => 'Instagram', 'photos' => $IGphotos]);
            }
            if (isset($tokens['GooglePhotos'])) {
                $access_token = ($this->get('session')->get('tokens'))['GooglePhotos'];
                $GPphotos = $apiRep->getApiData('googlephotos/photos', $access_token);
                $GPphotos = $controllerRep->castToPhotos($GPphotos);
                $GPphotos = $controllerRep->slicePhotosArray($GPphotos);
                array_push($photos, ['name' => 'Google Photos', 'photos' => $GPphotos]);
            }
            return $this->render('main/allPhotos.html.twig', ['photos' => $photos]);
        }

    }
}
