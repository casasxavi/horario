<?php

namespace App\Form;

use App\Entity\Evento;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class EventoType extends AbstractType
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
        ->add('hora', TimeType::class,[
            'widget' => 'single_text',
            'html5' => 'false',
            'empty_data' => 'false',
            'attr' => ['class' => 'js-timepicker'],
        ])
        ->add('descripcion', TextareaType::class, array(
            "label" => "Descripción:",
            "attr" => array(
                "class" => "form-control",
                "cols" => 50,
                "rows" => 4,
                )
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Evento::class,
        ]);
    }
}
