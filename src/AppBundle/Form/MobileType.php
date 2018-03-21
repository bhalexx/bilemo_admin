<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use AppBundle\Form\FeatureType;
// use AppBundle\Form\PictureType;

class MobileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $manufacturers = $this->setData($options['data']['manufacturers']);
        $oss = $this->setData($options['data']['oss']);

        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'constraints' => new Length(['min' => 3]),
            ])
            ->add('manufacturer', ChoiceType::class, [
                'label' => 'Fabricant',
                'mapped' => false,
                'choices' => $manufacturers,
                'group_by' => function($value, $key, $index) {
                    return 'Fabricants disponibles';
                }
            ])
            ->add('os', ChoiceType::class, [
                'label' => "Système d'exploitation (OS)",
                'mapped' => false,
                'choices' => $oss,
                'group_by' => function($value, $key, $index) {
                    return 'OS disponibles';
                }
            ])
            ->add('price', NumberType::class, [
                'label' => 'Prix',
                'constraints' => new NotBlank(),
            ])
            ->add('stock', NumberType::class, [
                'label' => 'Stock',
                'constraints' => new NotBlank(),
            ])
            ->add('memory', NumberType::class, [
                'label' => 'Mémoire',
                'constraints' => new NotBlank(),
            ])
            ->add('color_name', TextType::class, [
                'label' => 'Nom couleur',
                'constraints' => new Length(['min' => 3]),
            ])
            ->add('color_code', TextType::class, [
                'label' => 'Code couleur',
                'constraints' => new Length(['min' => 3, 'max' => 6]),
            ])
            ->add('features', CollectionType::class, [
                'entry_type' => FeatureType::class,
                'entry_options' => [
                    'label' => false
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true
            ])
            // ->add('pictures', CollectionType::class, [
            //     'entry_type' => PictureType::class,
            //     'entry_options' => [
            //         'label' => false
            //     ],
            //     'allow_add' => true,
            //     'allow_delete' => true,
            //     'delete_empty' => true
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        //No entity
    }

    public function setData($datas)
    {
        $list = [];
        foreach ($datas as $data) {
            $list[] = [
                $data['name'] => $data['id']
            ];
        }
        return $list;
    }
}