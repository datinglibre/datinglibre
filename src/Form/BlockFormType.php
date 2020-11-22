<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\BlockReason;
use App\Repository\BlockReasonRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlockFormType extends AbstractType
{
    private BlockReasonRepository $blockReasonRepository;

    public function __construct(BlockReasonRepository $blockReasonRepository)
    {
        $this->blockReasonRepository = $blockReasonRepository;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
    }

    public function buildForm(FormBuilderInterface $blockForm, array $options)
    {
        $blockForm->add('reason', EntityType::class, [
            'placeholder' => '',
            'class' => BlockReason::class,
            'choice_label' => 'name',
            'label' => 'block.reason',
            'choice_translation_domain' => 'blocks',
            'choices' => $this->blockReasonRepository->findAll(),
            'required' => true
        ]);

        $blockForm->add('submit', SubmitType::class, ['label' => 'block.block']);
    }
}
