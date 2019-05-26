<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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



    public function navigationBarAction(Request $request,$cPath)
    {
        $em = $this->getDoctrine()->getManager();
        $productFamilies = $em->getRepository('AppBundle:ProductFamily')->findAll();
        return $this->render('default/navbar.html.twig',array('families'=>$productFamilies, 'cPath'=>$cPath));
    }
}
