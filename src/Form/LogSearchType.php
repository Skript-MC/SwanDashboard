<?php

namespace App\Form;

use App\Entity\LogSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LogSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('userId', TextType::class, [
                'required' => false
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
            ->add('messageId', TextType::class, [
                'required' => false
            ])
            ->add('oldContent', TextType::class, [
                'required' => false
            ])
            ->add('newContent', TextType::class, [
                'required' => false
            ])
            ->add('channelId', TextType::class, [
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LogSearch::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
