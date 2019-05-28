<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Purchase;


class PurchaseController extends Controller
{

    /**
     * @Route("/payment/", options = { "expose" = true }, name="payment_page")
     */
    public function paymentPageAction(Request $req){
      $em = $this->getDoctrine()->getManager();

      $session = $req->getSession();

      if(!$session->has('cart')){
          return $this->redirectToRoute('homepage');
      }
      else{
        $amount = $this->getTotalCart($session->get('cart'));
        $form = $this->paymentForm();

        if ($req->isMethod('POST')) {
          $form->handleRequest($req);

          if ($form->isSubmitted() && $form->isValid() ) {
            try{
              \Stripe\Stripe::setApiKey($this->getParameter('stripe_secret_key'));

              $charge = \Stripe\Charge::create(array(
                'amount'   => $amount*100,
                'currency' => 'eur',
                'source' => $form->get('token')->getData(),
                'receipt_email' => $form->get('email')->getData()
              ));
              $purchase = $this->createPurchase($form->getData(),$charge['billing_details']['address']['postal_code'],$session->get('cart'));
              $em->persist($purchase);
              $em->flush();
              $session->remove('cart');
              return $this->redirectToRoute('payment_success',['spec_code_purchase'=>$purchase->getCode()]);
            }
            catch(\Stripe\Error\Base $e){

              $this->addFlash('warning','Paiement refusé');
            }
          }
        }

        return $this->render('purchase/payment.html.twig',[
          'form' => $form->createView(),
          'stripe_public_key' => $this->getParameter('stripe_public_key'),
          'amount' => $amount,
        ]);
      }

    }

    /**
     * @Route("/payment/success", name="payment_success")
     */
    public function paymentSuccessAction(Request $req){
      $purchaseCode = $req->query->get('spec_code_purchase');
      $em = $this->getDoctrine()->getManager();
      $purchase = $em->getRepository('AppBundle:Purchase')->findOneBy(['code'=>$purchaseCode]);
      if($purchase){
        return $this->render('purchase/success.html.twig',array('purchase'=>$purchase,));
      }
      else{
        return $this->redirectToRoute('homepage');
      }
    }

    /**
     * @Route("/admin/purchase/{$code}", name="admin_get_purchase_by_code")
     */
    public function getPurchaseByCodeAction($code){
      $em = $this->getDoctrine()->getManager();
      $purchase = $em->getRepository('AppBundle:Purchase')->findOneBy(['code'=>$code]);
      if($purchase){
        return $this->render('purchase/purchase.html.twig',array('purchase'=>$purchase,));
      }
      else{
        //return $this->redirectToRoute('homepage');
      }
    }

    /**
     * @Route("/admin/purchases/not_delivered", name="admin_get_purchases_not_delivered")
     */
    public function getNotDeliveredPurchasesAction(){
      $em = $this->getDoctrine()->getManager();
      $purchases = $em->getRepository('AppBundle:Purchase')->findAll();
      return $this->render('purchase/list.html.twig',['purchases'=>$purchases]);
    }

    /**
     * @Route("/admin/purchase/{id}",requirements={"id" = "\d+"}, name="admin_validate_purchase_delivery")
     */
    public function validatePurchaseDelivery($id){
      $em = $this->getDoctrine()->getManager();
      $purchase = $em->getRepository('AppBundle:Purchase')->find($id);
      $purchase->setIsDelivered(true);
      $em->merge($purchase);
      $em->flush();
      return new Response('Commande mise au statut délivré',200);
    }





//------------------------ PRIVATE FUNCTIONS-----------------------------------------------------------------------------------------------------

    private function getTotalCart($cart){
      $totalAmount = 0;
      foreach ($cart as $item) {
        $totalAmount += $item['priceUnit']*$item['quantity'] ;
      }
      return $totalAmount;
    }



    private function paymentForm(){
      return $this->get('form.factory')
        ->createNamedBuilder('payment-form')
        ->add('firstname',TextType::class)
        ->add('country',ChoiceType::class,array(
          'choices'=> ['France'=>'FR','Etats-Unis'=>'US'],
          'multiple' => false,
          'expanded' => false ))
        ->add('city',TextType::class)
        ->add('address1',TextType::class)
        ->add('address2',TextType::class,['required'=>false])
        ->add('lastname',TextType::class)
        ->add('email',EmailType::class)
        ->add('token', HiddenType::class, [
          'constraints' => [new Assert\NotBlank()],
        ])
        ->add('submit', SubmitType::class, array('label' => 'Valider'))
        ->getForm();
   }


   private function createPurchase($formData,$postalCode,$cartContent){
     $purchase = new Purchase();
     $purchase->setIsDelivered(false);
     $purchase->setProducts($cartContent);
     $purchase->setCustomerFirstname($formData['firstname']);
     $purchase->setCustomerLastname($formData['lastname']);
     $purchase->setCustomerEmail($formData['email']);
     $purchase->setCustomerCountry($formData['country']);
     $purchase->setCustomerCity($formData['city']);
     $purchase->setCustomerAddress1($formData['address1']);
     $purchase->setCustomerAddress2($formData['address2']);
     $purchase->setCustomerPostalCode($postalCode);

     return $purchase;
   }




}
