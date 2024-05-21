<?php

namespace App\Form;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextareaType::class, [
                "attr" => array(
                    'class' => 'textarea-form',
                    'placeholder' => 'Enter comment...'
                )
            ])
            // ->add('published_at', null, [
            //     'widget' => 'single_text'
            // ])
            //             ->add('post', EntityType::class, [
            //                 'class' => Post::class,
            // 'choice_label' => 'id',
            //             ])
            //             ->add('author', EntityType::class, [
            //                 'class' => User::class,
            // 'choice_label' => 'id',
            //             ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
