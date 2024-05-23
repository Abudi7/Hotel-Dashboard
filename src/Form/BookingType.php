<?php

namespace App\Form;

use App\Entity\Booking;
use App\Entity\Rooms;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startdate', null, [
                'widget' => 'single_text',
            ])
            ->add('enddate', null, [
                'widget' => 'single_text',
            ])
            ->add('customername')
            ->add('Rooms', EntityType::class, [
                'class' => Rooms::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
