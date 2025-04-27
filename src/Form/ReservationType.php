<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\Reservation;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('reservedAt', null, [
                'widget' => 'single_text',
                'disabled' => true,
            ])
            ->add('status', ChoiceType::class, [
                'choices'  => [
                    'En attente' => Reservation::STATUS_EN_ATTENTE,
                    'Confirmée' => Reservation::STATUS_CONFIRMEE,
                    'Annulée' => Reservation::STATUS_ANNULEE,
                ],
            ])
            ->add('quantity', null, [
                'disabled' => true,
            ])
            // ->add('user', EntityType::class, [
            //     'class' => User::class,
            //     'choice_label' => 'id',
            //     'disabled' => true,
            // ])
            // ->add('book', EntityType::class, [
            //     'class' => Book::class,
            //     'choice_label' => 'id',
            //     'disabled' => true,
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
