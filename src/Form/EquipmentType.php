<?php

namespace App\Form;

use App\Entity\Equipment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EquipmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('image', FileType::class, [
                'label' => 'Import Image',
                'mapped' => false,
                'required' => true,
            ])
            
            ->add('availabilityStatus', ChoiceType::class, [
                'label' => 'Availability Status',
                'choices' => [
                    'Available' => 'available',
                    'Not Available' => 'not_available',
                    'Under Maintenance' => 'under_maintenance',
                ],
                'attr' => ['class' => 'form-control'],
            ])
           
            ->add('category')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Equipment::class,
        ]);
    }
}
