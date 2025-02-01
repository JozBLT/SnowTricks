<?php

namespace App\Form;

use App\Entity\Categories;
use App\Entity\Tricks;
use DateTimeImmutable;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\Slugger\AsciiSlugger;

class TricksType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'empty_data' => '',
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu',
                'empty_data' => '',
            ])->add('category', EntityType::class, [
                'class' => Categories::class,
                'choice_label' => 'name',
                'placeholder' => 'Catégorie',
                'required' => false,
                'label' => 'Catégorie',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Envoyer',
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, $this->autoSlug(...))
            ->addEventListener(FormEvents::POST_SUBMIT, $this->attachTimestamps(...))
        ;
        $this->addCollectionField($builder, 'images', FileType::class);
        $this->addCollectionField($builder, 'videos', UrlType::class);
    }

    public function autoSlug(PostSubmitEvent $event): void
    {
        $data = $event->getData();

        if (!($data instanceof Tricks)) {
            return;
        }

        $slugger = new AsciiSlugger();
        $slug = strtolower($slugger->slug($data->getTitle()));
        $data->setSlug($slug);
    }

    public function attachTimestamps(PostSubmitEvent $event): void
    {
        $data = $event->getData();

        if (!($data instanceof Tricks)) {
            return;
        }
        $data->setUpdatedAt(new DateTimeImmutable());

        if (!$data->getId()) {
            $data->setCreatedAt(new DateTimeImmutable());
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tricks::class,
            'show_thumbnail_field' => true,
        ]);
    }

    private function addCollectionField(FormBuilderInterface $builder, string $name, string $entryType): void
    {
        $builder->add($name, CollectionType::class, [
            'label' => false,
            'entry_type' => $entryType,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'required' => false,
            'mapped' => false,
        ]);
    }
}
