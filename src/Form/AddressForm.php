<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $inputClass = 'block w-full rounded-md border-gray-300 shadow-sm focus:border-[#D66853] focus:ring-[#D66853] text-sm p-2';

        $builder
            ->add('streetNumber', null, [
                'label' => 'Numéro de rue',
                'attr' => ['class' => $inputClass],
            ])
            ->add('street', null, [
                'label' => 'Rue',
                'attr' => ['class' => $inputClass],
            ])
            ->add('zipcode', null, [
                'label' => 'Code postal',
                'attr' => ['class' => $inputClass],
            ])
            ->add('country', null, [
                'label' => 'Pays',
                'attr' => ['class' => $inputClass],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
