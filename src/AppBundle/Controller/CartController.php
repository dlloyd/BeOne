<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;


class CartController extends Controller
{
  /**
   * @Method("GET")
   */
  public function addProductFormAction(Request $req,$prodId){
    $em = $this->getDoctrine()->getManager();

    $prod = $em->getRepository('AppBundle:Product')->find($prodId);

    if(!$prod || $prod->getIsHidden()){
        throw $this->createNotFoundException('Produit supprimé ou inexistant');
    }
    else{
      $form = $this->getProductCorrespondingForm($prod);
    }

    return $this->render('cart/add_product_form.html.twig',array('form' => $form->createView(),'prodId'=>$prodId));
  }



  /**
   * @Route("/addtocart/{prodId}",requirements={"prodId" = "\d+"}, name="add_to_cart")
   * @Method("POST")
   */
  public function addProductAction(Request $req,$prodId){
      if (!$req->isXmlHttpRequest()) {
          return new Response('Uniquement requête Ajax', 400);
      }

      $em = $this->getDoctrine()->getManager();
      $prod = $em->getRepository('AppBundle:Product')->find($prodId);

      if(!$prod || $prod->getIsHidden()){
          throw $this->createNotFoundException('Produit supprimé ou inexistant');
      }
      else{
        $form = $this->getProductCorrespondingForm($prod);
      }

      $form->HandleRequest($req);
      if($form->isSubmitted() && $form->isValid()){
        $session = $req->getSession();
        $data = $form->getData();
        $data['imageUrl'] = $this->getProductImageThumbUrl($prod); // create image thumb if not exists and return url_link
        $size = array_key_exists('size', $data)? $data['size']->getSubname() : '';

        $cart = $this->addProductInCart($session,$data);
        $session->set('cart',$cart);

        $item = ['id' =>$data['id'],'name'=> $data['name'],'quantity'=> $data['quantity'] ,
                  'priceUnit'=>$prod->getPriceUnit(),'imgUrl'=> $data['imageUrl'],
                  'size'=>$size,];

        return new Response(json_encode($item), 200, array('Content-Type' => 'application/json'));
      }

      return new Response('Erreur formulaire', 400);
  }




  /**
   * @Route("/cart/", name="full_cart")
   */
  public function fullViewAction(Request $req){
      $cartExist = $req->getSession()->has('cart');
      return $this->render('cart/full_cart.html.twig');
  }



  /**
   * @Route("/cart/del/{id}/{size}",requirements={"id" = "\d+"}, options = { "expose" = true }, name="remove_from_cart")
   */
  public function removeProductAction(Request $req,$id,$size){
    if (!$req->isXmlHttpRequest()) {
        return new Response('Uniquement requête Ajax', 400);
    }
    $session = $req->getSession();
        if($session->has('cart')){
            $newCart = array();
            foreach ($session->get('cart') as $item) {
                if($item['id'] != $id ){
                  if($size=='none' || $item['size']->getSubname()!= $size)
                    array_push($newCart,$item);
                }
            }
            $session->set('cart',$newCart);
          }
          else{
            return new Response('No cart found',400);
          }

        return new Response('supression réussi', 200);
  }




  /**
   * @Route("/cart/upd/", name="validation_cart")
   */
  public function updateCartBeforePaymentAction(Request $req){
    if (!$req->isXmlHttpRequest()) {
        return new Response('Uniquement requête Ajax', 400);
    }
    $updatedCart = $req->request->get('cart');
    $newCart = [];
    $session = $req->getSession();
    $sessionCart = $session->get('cart');
    foreach ($updatedCart as $updatedItem) {
      foreach ($sessionCart as $item) {
        $sizeExist = array_key_exists('size',$item);
        if($updatedItem['id']==$item['id'] ){
          if(!$sizeExist ||$updatedItem['size']==$item['size']->getSubname() ){
            $item['quantity'] = $updatedItem['quantity'];
            array_push($newCart,$item);
          }

        }
      }
    }
    $session->set('cart',$newCart);
    return new Response('mis à jour réussi', 200);
  }







//----------------------- PRIVATE METHODS -----------------------------------------------------------------------------------------------------------------------


  private function getProductCorrespondingForm($prod){
    if($prod->getFamily()->getHasSize()){
      return $this->sizableProductForm($prod);
    }
    else{
      return $this->notSizableProductForm($prod);
    }
  }



  private function sizableProductForm($prod){
    return $this->createFormBuilder(array())
          ->add('quantity', ChoiceType::class,array(
            'choices'=> ['1' =>1,'2'=>2,'3'=>3,'4'=>4,'5'=>5],
            'multiple' => false,
            'expanded' => false ))
          ->add('size', EntityType::class, array(
                  'required'=> true,
                  'class' => 'AppBundle:SizeProduct',
                  'choices' => $prod->getSizes(),
                  'choice_label' => 'subname',
                  'multiple' => false ))
          ->add('id', HiddenType::class,array(
                  'data' =>$prod->getId(),
                  'attr' => array(
                  'readonly' => true,
                  'hidden' => true,
          ),))
          ->add('priceUnit', HiddenType::class,array(
                  'data' =>$prod->getPriceUnit(),
                  'attr' => array(
                  'readonly' => true,
                  'hidden' => true,
          ),))
          ->add('name', HiddenType::class,array(
                  'data' =>$prod->getName(),
                  'attr' => array(
                  'readonly' => true,
                  'hidden' => true,
          ),))
          ->getForm();
  }

  private function notSizableProductForm($prod){
    return $this->createFormBuilder(array())
          ->add('quantity', ChoiceType::class,array(
            'choices'=> ['1' =>1,'2'=>2,'3'=>3,'4'=>4,'5'=>5],
            'multiple' => false,
            'expanded' => false ))
          ->add('id', HiddenType::class,array(
                  'data' =>$prod->getId(),
                  'attr' => array(
                  'readonly' => true,
                  'hidden' => true,
          ),))
          ->add('priceUnit', HiddenType::class,array(
                  'data' =>$prod->getPriceUnit(),
                  'attr' => array(
                  'readonly' => true,
                  'hidden' => true,
          ),))
          ->add('name', HiddenType::class,array(
                  'data' =>$prod->getName(),
                  'attr' => array(
                  'readonly' => true,
                  'hidden' => true,
          ),))
          ->getForm();
  }


  private function getProductImageThumbUrl($prod){
    $imagineCacheManager = $this->get('liip_imagine.cache.manager');
    $imagePath = '//images_products//'.$prod->getImgStarName();
    $imageUrlFiltered = $imagineCacheManager->getBrowserPath($imagePath, 'my_quick_cart_thumb');
    return $imageUrlFiltered;

  }

  private function addProductInCart($session,$data){
    $sizeExist = array_key_exists('size',$data);
    if($session->has('cart')){
        $cart = $session->get('cart');
        $newCart = array();

        foreach ($cart as $item) {
            $itemHasSize = array_key_exists('size',$item);
            if($item['id'] != $data['id']){
              if(!$sizeExist||($itemHasSize && $item['size']->getId()!=$data['size']->getId()))
                array_push($newCart,$item);
            }
        }

        $cart = $newCart;
    }
    else{
        $cart = array();
    }

    array_push($cart,$data);
    return $cart;
  }



}
