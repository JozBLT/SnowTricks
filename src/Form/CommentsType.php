<?php

namespace App\Form;

use App\Entity\Comments;
use DateTimeImmutable;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextareaType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'block w-1/2 mx-auto mt-1 rounded-lg border-gray-300 shadow-sm',
                    'style' => 'border-width: 1px; border-color: #D1D5DB; padding: 1rem;',
                    'placeholder' => 'Écrivez votre commentaire ici...',
                    'rows' => 4
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Commenter',
                'attr' => [
                    'class' => 'px-4 py-2 bg-blue-500 text-white font-semibold rounded-lg hover:bg-blue-600'
                ]
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, $this->attachTimestamps(...));
    }

    public function attachTimestamps(PostSubmitEvent $event): void
    {
        $data = $event->getData();

        if (!($data instanceof Comments)) {
            return;
        }
        $data->setCreatedAt(new DateTimeImmutable());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comments::class,
        ]);
    }
}
