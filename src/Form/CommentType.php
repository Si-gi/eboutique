<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('comment', TextareaType::class,
            [
                'attr' => ['class' => 'form-control', 'rows' =>"3"]
            ])
            ->add('submit', SubmitType::class,
                [
                    'attr' => ['class' => 'form-control btn-primary '],
                    'label' => 'Valider'
                ]);
    }

    public function getName()
    {
        return 'comment';
    }
}
