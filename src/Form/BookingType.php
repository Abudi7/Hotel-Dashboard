<?php

namespace App\Form;

use App\Entity\Booking;
use App\Entity\Rooms;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType; // Import TextType
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security; // Import Security

class BookingType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $this->security->getUser(); // Get the logged-in user
        
        $customerNameOptions = [
            'disabled' => true, // Make the field disabled so it cannot be edited
        ];

        if ($user !== null) {
            $customerNameOptions['data'] = $user->getUserIdentifier(); // Set the default value to the identifier of the logged-in user
        }

        $builder
            ->add('customername', TextType::class, [
                'label' => 'Customer Name',
            ])
            ->add('startdate', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Start Date',
            ])
            ->add('enddate', DateType::class, [
                'widget' => 'single_text',
                'label' => 'End Date',
            ])
            ->add('rooms', EntityType::class, [
                'class' => 'App\Entity\Rooms',
                'choice_label' => 'name', // Adjust this according to your Room entity
                'label' => 'Room',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
