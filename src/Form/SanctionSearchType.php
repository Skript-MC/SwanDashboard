<?php

namespace App\Form;

use App\Entity\SanctionSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SanctionSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('memberId', TextType::class, [
                'required' => false
            ])
            ->add('moderatorId', TextType::class, [
                'required' => false
            ])
            ->add('reason', TextType::class, [
                'required' => false
            ])
            ->add('sanctionStatus', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'Terminée' => true,
                    'En cours' => false
                ]
            ])
            ->add('beforeDate', DateTimeType::class, [
                'required' => false,
                'input' => 'timestamp',
                'widget' => 'single_text'
            ])
            ->add('afterDate', DateTimeType::class, [
                'required' => false,
                'input' => 'timestamp',
                'widget' => 'single_text'
            ])
            ->add('sanctionType', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'Bannissement Discord' => 'hardban',
                    'Bannissement' => 'ban',
                    'Mute' => 'mute',
                    'Avertissement' => 'warn'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SanctionSearch::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
