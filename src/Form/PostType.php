<?php

namespace App\Form;

use App\Entity\Tag;
use App\Entity\Post;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('postTitle', TextType::class, [
                'label' => 'Titre du post',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('textuelContent', TextareaType::class, [
                'label' => 'Votre texte',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('visuelContent', TextType::class, [
                'label' => 'Votre image (URL)',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('associatedTo', EntityType::class, [
                'label' => 'A quel(s) catégorie(s) appartient ce post ?',
                'class' => Tag::class,
                'multiple' => true,
            ])
            ->add('valider', SubmitType::class, [
                'label' => 'Publier',
                'attr' => [
                'class' => 'btn btn-primary']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
