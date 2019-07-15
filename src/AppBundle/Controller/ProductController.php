<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Entity\Product;
use AppBundle\Form\ProductType;
use AppBundle\Form\ImageType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\Response;




class ProductController extends Controller
{
  /**
  * @Route("/admin/products",name="products_list")
  */
  public function productsListAction(){
    $em = $this->getDoctrine()->getManager();
    $prods = $em->getRepository('AppBundle:Product')->findAllActives();
    return $this->render('product/list.html.twig',array('products'=>$prods));

  }


  public function filterBySizeAction(){
    $em = $this->getDoctrine()->getManager();
    $sizes = $em->getRepository('AppBundle:SizeProduct')->findAll();
    return $this->render('product/filter-by-size.html.twig',array('sizes'=>$sizes));
  }


  /**
  * @Route("/prods/fam/{id}", requirements={"id" = "\d+"}, defaults={"id" = 1}, name="products_by_fam_list")
  */
  public function productsByFamilyAction($id){
    $em = $this->getDoctrine()->getManager();
    $prods = $em->getRepository('AppBundle:Product')->findProductsByFamily($id);
    $currentFam = $em->getRepository('AppBundle:ProductFamily')->find($id);
    $fams = $em->getRepository('AppBundle:ProductFamily')->findAll();
    return $this->render('product/family.html.twig',array('products'=>$prods,'families'=>$fams,'currentFamily'=>$currentFam,));

  }

  /**
     * @Route("/prod/{id}", requirements={"id" = "\d+"}, name="get_product")
     */
  public function showSingleProductAction($id){
    $em = $this->getDoctrine()->getManager();
    $product = $em->getRepository('AppBundle:Product')->find($id);

    if(!$product || $product->getIsHidden()){
    	throw new NotFoundHttpException("Page not found");
    }

    $prods = $em->getRepository('AppBundle:Product')->findAll();
    $similars = array();

    foreach ($prods as $prod) {
      if($prod->getId()!=$id && $prod->getFamily()== $product->getFamily()){
        array_push($similars,$prod);
      }

    }

    return $this->render('product/single_product.html.twig',array('product'=>$product,'similarProducts'=>$similars ));
  }


  /**
  * @Route("/quickviewv/prod/{id}",requirements={"id"="\d+"}, name="show_single_product")
  */
  public function quickViewProductAction($id){
    $em = $this->getDoctrine()->getManager();
    $prod = $em->getRepository('AppBundle:Product')->find($id);
    if(!$prod || $prod->getIsHidden()){
    		throw new NotFoundHttpException("Page not found");
    }

    return $this->render('product/quick_view.html.twig',array('product'=>$prod ));
  }


/**
 * @Route("/admin/add/prodfam/{famCode}", name="add_product")
 */
  public function createAction(Request $req,$famCode){
    $prod = new Product();
    $em = $this->getDoctrine()->getManager();
    $fam = $em->getRepository('AppBundle:ProductFamily')->findOneBy(['code'=>$famCode]);
  	$form = $this->createForm(ProductType::class, $prod);
    if($fam->getHasSize()){
      $form->add('sizes', EntityType::class, array(
          'required'=> true,
          'class' => 'AppBundle:SizeProduct',
          'choice_label' => 'name',
          'expanded' => true,
          'multiple' => true ));
    }
  	$form->HandleRequest($req);
  	if($form->isSubmitted() && $form->isValid()){
         $em = $this->getDoctrine()->getManager();
         $prod->setIsHidden(false);
         $prod->setFamily($fam);
         $em->persist($prod);
         $em->flush();
         $req->getSession()->getFlashBag()->add('notice','Produit ajouté');
         return $this->redirectToRoute('add_product_image',array('id'=>$prod->getId()));
      }

      return $this->render('product/create.html.twig',array('form' => $form->createView(),'family'=>$fam));
  }


  /**
 * @Route("/admin/del/prod/{id}", requirements={"id" = "\d+"}, name="delete_product")
 */
public function deleteProductAction($id){
  $em = $this->getDoctrine()->getManager();
  $prod = $em->getRepository('AppBundle:Product')->find($id);

  if(!$prod){
     throw new $this->createNotFoundException("Produit inexistant");


  }
  $prod->setIsHidden(true);
    $em->merge($prod);
    $em->flush();

  return $this->redirectToRoute('products_list');
}


  /**
   * @Route("/admin/add/img/product/{id}", requirements={"id" = "\d+"}, name="add_product_image")
   */
  public function addImageProductAction(Request $req,$id){
  	$em = $this->getDoctrine()->getManager();
  	$prod = $em->getRepository('AppBundle:Product')->find($id);
    $images = array();

  	$form = $this->createFormBuilder($images)
          ->add('images',CollectionType::class, array(
                  'label' => false,
                  'entry_type' => ImageType::class,
                  'entry_options' => array('label' => false),
                  'allow_add' => true,
              ))
          ->add('save', SubmitType::class, array('disabled'=>true,'label' => 'Enregistrer'))
          ->getForm();

    $form->HandleRequest($req);
  	if($form->isSubmitted() && $form->isValid()){
         $em = $this->getDoctrine()->getManager();

         foreach ($form['images']->getData() as $img) {
            $img->setProduct($prod);
            $img->setIsStar(false);
            $img->setIsStarBack(false);
            $prod->addImage($img);
          }

         $em->merge($prod);
         $em->flush();

         //$req->getSession()->getFlashBag()->add('notice','Image(s) ajoutée(s)');
         $imageInfos = [$img->getId(),$img->getName()];

         return new Response(json_encode($imageInfos), 200, array('Content-Type' => 'application/json'));
      }


      return $this->render('product/prod-images.html.twig', array(
          'form' => $form->createView(), 'product'=>$prod,
      ));
  }


  /**
 * @Route("/admin/update/prod/{id}", requirements={"id" = "\d+"}, name="update_product")
 */
  public function updateProductAction(Request $req,$id){
    $em = $this->getDoctrine()->getManager();
    $prod = $em->getRepository('AppBundle:Product')->find($id);

    $form = $this->createForm(ProductType::class, $prod);

    if($prod->getFamily()->getHasSize()){
      $form->add('sizes', EntityType::class, array(
          'required'=> true,
          'class' => 'AppBundle:SizeProduct',
          'choice_label' => 'name',
          'expanded' => true,
          'multiple' => true ));
    }

    $form->HandleRequest($req);
    if($form->isSubmitted() && $form->isValid()){
         $em = $this->getDoctrine()->getManager();
         $em->merge($prod);
         $em->flush();
         $req->getSession()->getFlashBag()->add('notice','Produit mis à jour');
         return $this->redirectToRoute('products_list');
      }

      return $this->render('product/update.html.twig', array(
          'form' => $form->createView(),'product'=>$prod,
      ));

  }


  /**
   * @Route("/admin/product/img/del/{imgId}", requirements={"imgId" = "\d+"},options = { "expose" = true }, name="delete_product_image")
   */
  public function deleteImageProductAction($imgId){
    $em = $this->getDoctrine()->getManager();
    $img = $em->getRepository('AppBundle:Image')->find($imgId);

    if(!$img){
       return new Response("Image inexistante", 400);
    }
      $em->remove($img);
      $em->flush();
      return new Response("Image supprimée", 200);

  }



 /**
 * @Route("/admin/product/imgstar/{imgId}", requirements={"imgId" = "\d+"}, name="change_product_image_star")
 */
  public function changeProductImageStarAction($imgId){
    $em = $this->getDoctrine()->getManager();
    $img = $em->getRepository('AppBundle:Image')->find($imgId);

    if(!$img){
        return new Response("Image inexistante", 400);
      }

    $prod = $img->getProduct() ;
    foreach ($prod->getImages() as $image) {
          if($image->getIsStar())
            $image->setIsStar(false);
    }
    $img->setIsStar(true);
    $prod->setImgStarName($img->getName());
    $em->merge($prod);
    $em->merge($img);
    $em->flush();

    return new Response("Image star changée", 200);

  }

  /**
  * @Route("/admin/product/imgstarback/{imgId}", requirements={"imgId" = "\d+"},options = { "expose" = true }, name="change_product_image_star_back")
  */
   public function changeProductImageStarBackAction($imgId){
     $em = $this->getDoctrine()->getManager();
     $img = $em->getRepository('AppBundle:Image')->find($imgId);

     if(!$img){
           return new Response("Image inexistante", 400);
       }

     $prod = $img->getProduct() ;
     foreach ($prod->getImages() as $image) {
           if($image->getIsStarBack())
             $image->setIsStarBack(false);
     }
       $img->setIsStarBack(true);
       $prod->setImgStarBackName($img->getName());
       $em->merge($prod);
       $em->merge($img);
       $em->flush();
   return new Response("Image star arrière-plan changée", 200);

   }

}
