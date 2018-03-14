<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ApplicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Nom',
                'constraints' => new Length(['min' => 3]),
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email de contact',
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 3]),
                ],
            ])
            ->add('uri', UrlType::class, [
                'label' => 'URL',
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 3]),
                ],
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Rôle',
                'mapped' => false,
                'choices' => [
                    "ROLE_APPLICATION" => "ROLE_APPLICATION",
                    "ROLE_BILEMO" => "ROLE_BILEMO"
                ],
                'choice_label' => function ($value, $key, $index) {
                    return strtoupper($key);
                },
                'data' => $options['data']['roles'][0]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        //No entity
    }
}