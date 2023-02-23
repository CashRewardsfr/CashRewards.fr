<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Constraints\Email;

class CreateUserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', TextType::class, [
                'label' =>  'Pseudo',
                'attr'  =>  ['placeholder' => "pseudo"],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ est requis',
                    ])
                ],
            ])
            ->add('paypal', UrlType::class, [
                'label' =>  'Adresse paypal',
                'attr'  =>  [
                    'placeholder' => 'Paypal',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ est requis',
                    ]),
                    new Email([
                        'message' => 'Cette valeur ne corespond pas à une adresse email valide' 
                    ])
                ],
            ])
            ->add('email', EmailType::class, [
                'label' =>  'Adresse e-mail',
                'attr'  =>  ['placeholder' => "Adresse Email"],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ est requis',
                    ]),
                    new Email([
                        'message' => 'Cette valeur ne corespond pas à une adresse email valide' 
                    ])
                ],
            ])
            ->add('points', NumberType::class, [
                'label' =>  'Points',
                'attr'  =>  ['placeholder' => "Points"],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ est requis',
                    ]),
                ],
            ])
            ->add('roles', ChoiceType::class, [
                'label' =>  'Rôle',
                'choices'   =>  [
                    'Administrateur' =>  'ROLE_ADMIN',
                    'Utilisateur'    =>  'ROLE_USER',
                ],
                'expanded' => true,
                'multiple' => true
            ])
            ->add('isVerified', CheckboxType::class, [
                'label' =>  'Activez manuellement le compte',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
