<?php

namespace App\Form;

use App\Entity\Horario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;

class HorarioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fecha', DateType::class, [
                'widget' => 'single_text',
                'html5' =>  'false',
                'empty_data' => false,
                'attr' =>   ['class' => 'js-datepicker'],
            ])
            ->add('horaEntrada', TimeType::class,[
                'widget' => 'single_text',
                'html5' => 'false',
                'empty_data' => 'false',
                'attr' => ['class' => 'js-timepicker'],
            ])
            ->add('horaSalida', TimeType::class,[
                'widget' => 'single_text',
                'html5' => 'false',
                'attr' => ['class' => 'js-timepicker'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Horario::class,
        ]);
    }
}
