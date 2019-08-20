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
            //there is nothing to show
            return $this->render('main/noPhotos.html.twig');
        } else {
            $photos = [];
            if (isset($tokens['Instagram'])) {
                $igService = new InstagramService(new InstagramFactory(), new InstagramClient());
                $igPhotos = $igService->getPhotos($tokens['Instagram']);
                $igPhotos = (new ControllerRepository())->slicePhotosArray($igPhotos);
                array_push($photos, ['name' => 'Instagram', 'photos' => $igPhotos]);
            }
            if (isset($tokens['GooglePhotos'])) {
                $gpService = new GooglePhotosService(new GooglePhotosClient(), new GooglePhotosFactory());
                $gpPhotos = $gpService->getAllPhotos($tokens['GooglePhotos']);
                $gpPhotos = (new ControllerRepository())->slicePhotosArray($gpPhotos);
                array_push($photos, ['name' => 'Google Photos', 'photos' => $gpPhotos]);
            }
            return $this->render('main/allPhotos.html.twig', ['photos' => $photos]);
        }

    }
}
