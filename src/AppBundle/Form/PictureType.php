<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;

class PictureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', FileType::class, [
                'label' => false,
                'required' => false
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