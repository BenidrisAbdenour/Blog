<?php

namespace App\Form;

use App\Entity\Post;
// use App\Entity\Tag;
// use App\Entity\User;
// use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\Slugger\AsciiSlugger;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                "required" => false,
                "attr" => array(
                    'class' => 'input-form',
                    'placeholder' => 'Enter title...'
                )
            ])
            // ->add('slug')
            ->add('summary', TextareaType::class, [
                "required" => false,
                "attr" => array(
                    'class' => 'textarea-form',
                    'placeholder' => 'Enter summary...'
                )
            ])
            ->add('content', TextareaType::class, [
                "required" => false,
                "attr" => array(
                    'class' => 'textarea-form',
                    'placeholder' => 'Enter Content...'
                )
            ]);

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {

            $post = $event->getData();
            if (null == $post->getSlug() && null != $post->getTitle()) {
                $slugger = new AsciiSlugger();
                $post->setSlug($slugger->slug($post->getTitle())->lower());
            }
        })
            //             ->add('published_at', null, [
            //                 'widget' => 'single_text'
            //             ])
            //             ->add('updated_at', null, [
            //                 'widget' => 'single_text'
            //             ])
            //             ->add('author', EntityType::class, [
            //                 'class' => User::class,
            // 'choice_label' => 'id',
            //             ])
            //             ->add('tags', EntityType::class, [
            //                 'class' => Tag::class,
            // 'choice_label' => 'id',
            // 'multiple' => true,
            //             ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
