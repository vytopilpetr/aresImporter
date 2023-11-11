<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class CompanySearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('companyId', IntegerType::class, [
                'label' => 'Company ICO',
                'attr' => ['placeholder' => 'Enter Company ICO'],
                'constraints' => [
                    new NotBlank(['message' => 'Please enter a Company ICO.']),
                    new Type([
                        'type' => 'integer',
                        'message' => 'The Company ICO must be an integer.',
                    ]),
                ],
            ])
            ->add('search', SubmitType::class, [
                'label' => 'Import',
            ]);
    }
}
