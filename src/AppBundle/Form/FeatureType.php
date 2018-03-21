<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class FeatureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 2]),
                ]
            ])
            ->add('value', TextType::class, [
                'label' => 'Valeur',
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 2]),
                ]
            ])
            ->add('Supprimer', ButtonType::class, array(
              'attr' => array('class' => 'collection-remove btn-outline-danger btn-sm'))
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        //No entity
    }
}