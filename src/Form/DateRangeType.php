<?php
// src/Form/DateRangeType.php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class DateRangeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $today = (new \DateTime())->format('Y-m-d');

        $builder
            ->add('startdate', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Start Date',
                'attr' => [
                    'min' => $today,
                ],
            ])
            ->add('enddate', DateType::class, [
                'widget' => 'single_text',
                'label' => 'End Date',
                'attr' => [
                    'min' => $today,
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Check Availability',
            ]);
    }
}
