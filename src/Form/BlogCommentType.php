<?php

namespace App\Form;

use App\Entity\Blog;
use App\Entity\BlogComment;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlogCommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description')
            ->add('blog', EntityType::class, [
                'class' => Blog::class,
                'choice_label' => function(Blog $blog){
                    return "{$blog->getTitle()}";
                },
                'multiple' => false, 
                'expanded' => false
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => function(User $user){
                    return "{$user->getUserIdentifier()}";
                },
                'multiple' => false, 
                'expanded' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BlogComment::class,
        ]);
    }
}
