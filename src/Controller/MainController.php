<?php

namespace App\Controller;

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
                $token = $this->get('session')->get('ig_access_token');
                if (isset($token))
                {
                    $this->get('session')->remove('ig_access_token');
                    $this->addFlash('success', 'Successfully disconnected from Instagram');
                }
                else
                {
                    $this->addFlash('warning','Something went wrong, maybe you are already disconnected');
                }
                break;
            case 'googlephotos':
                $token = $this->get('session')->get('gp_access_token');
                if (isset($token))
                {
                    $this->get('session')->remove('gp_access_token');
                    $this->addFlash('success', 'Successfully disconnected from Google Photos');
                }
                else
                {
                    $this->addFlash('warning','Something went wrong, maybe you are already disconnected');
                }
                break;
            default:
                break;
        }
        return $this->render('main/disconnect.html.twig');
    }
}
