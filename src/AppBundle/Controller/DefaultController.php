<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $newProducts = $em->getRepository('AppBundle:Product')->findLatestProducts(4);
        $mixedProducts = $em->getRepository('AppBundle:Product')->findMixedProducts(4);
        return $this->render('default/index.html.twig',array('newProducts'=>$newProducts,'mixedProducts'=>$mixedProducts,));
    }

    /**
     * @Route("/validatecook", name="cookies_consented")
     */
    public function cookiesConsentAction(Request $request)
    {
      if (!$request->isXmlHttpRequest()) {
          return new Response('Uniquement requête Ajax', 400);
      }
      $session = $request->getSession();
      $session->set('cookieConsented',true);
      return new Response('cookies consented', 200);
    }



    public function navigationBarAction(Request $request,$cPath)
    {
        $em = $this->getDoctrine()->getManager();
        $productFamilies = $em->getRepository('AppBundle:ProductFamily')->findAll();
        return $this->render('default/navbar.html.twig',array('families'=>$productFamilies, 'cPath'=>$cPath));
    }
}
