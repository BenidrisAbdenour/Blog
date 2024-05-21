<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TagType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                "attr" => array(
                    'class' => 'input-form',
                    'placeholder' => 'Enter tag name...'
                )
            ]);
        // ->add('posts', EntityType::class, [
        //     "required" => false,
        //     'class' => Post::class,
        //     'choice_label' => 'title',
        //     'multiple' => true,
        //     "attr" => array(
        //         'class' => 'select-form',
        //     )
        // ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tag::class,
        ]);
    }
}
