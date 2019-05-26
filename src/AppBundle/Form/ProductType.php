<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;




class ProductType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name',TextType::class)
                ->add('description',TextareaType::class, array(
                        'attr' => array('cols' => '40', 'rows' => '3','spellcheck'=>'true')))
                ->add('priceUnit',NumberType::class, array('scale' => 2,))

                ->add('family', EntityType::class, array(
                    'required'=> true,
                    'class' => 'AppBundle:ProductFamily',
                    'choice_label' => 'name',
                    'multiple' => false ))
                ->add('sizes', EntityType::class, array(
                    'required'=> false,
                    'class' => 'AppBundle:SizeProduct',
                    'choice_label' => 'name',
                    'expanded' => true,
                    'multiple' => true ))
                ->add('save', SubmitType::class, array('label' => 'Enregistrer'));;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Product'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_product';
    }


}