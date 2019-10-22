<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantGui\Communication\Form;

use Generated\Shared\Transfer\MerchantCriteriaFilterTransfer;
use Spryker\Zed\Kernel\Communication\Form\AbstractType;
use Spryker\Zed\MerchantGui\Dependency\Facade\MerchantGuiToMerchantFacadeInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Required;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @method \Spryker\Zed\MerchantGui\Communication\MerchantGuiCommunicationFactory getFactory()
 * @method \Spryker\Zed\MerchantGui\MerchantGuiConfig getConfig()
 */
class MerchantForm extends AbstractType
{
    public const OPTION_CURRENT_ID = 'current_id';

    protected const FIELD_ID_MERCHANT = 'id_merchant';
    protected const FIELD_NAME = 'name';
    protected const FIELD_REGISTRATION_NUMBER = 'registration_number';
    protected const FIELD_EMAIL = 'email';
    protected const FIELD_MERCHANT_REFERENCE = 'merchant_reference';

    protected const LABEL_NAME = 'Name';
    protected const LABEL_REGISTRATION_NUMBER = 'Registration number';
    protected const LABEL_EMAIL = 'Email';
    protected const LABEL_MERCHANT_REFERENCE = 'Merchant reference';

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setRequired(static::OPTION_CURRENT_ID);
    }

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return 'merchant';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this
            ->addIdMerchantField($builder)
            ->addNameField($builder)
            ->addEmailField($builder, $options[static::OPTION_CURRENT_ID])
            ->addRegistrationNumberField($builder)
            ->addMerchantReference($builder, $options[static::OPTION_CURRENT_ID]);

        $this->executeMerchantProfileFormExpanderPlugins($builder, $options);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addIdMerchantField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_ID_MERCHANT, HiddenType::class);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addNameField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_NAME, TextType::class, [
            'label' => static::LABEL_NAME,
            'required' => false,
            'constraints' => $this->getTextFieldConstraints(),
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addRegistrationNumberField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_REGISTRATION_NUMBER, TextType::class, [
            'label' => static::LABEL_REGISTRATION_NUMBER,
            'required' => false,
            'constraints' => [
                new Length(['max' => 255]),
            ],
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param int|null $currentId
     *
     * @return $this
     */
    protected function addEmailField(FormBuilderInterface $builder, ?int $currentId = null)
    {
        $builder->add(static::FIELD_EMAIL, EmailType::class, [
            'label' => static::LABEL_EMAIL,
            'required' => false,
            'constraints' => $this->getEmailFieldConstraints($currentId),
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param int|null $currentMerchantId
     *
     * @return $this
     */
    protected function addMerchantReference(FormBuilderInterface $builder, ?int $currentMerchantId = null)
    {
        $builder
            ->add(static::FIELD_MERCHANT_REFERENCE, TextType::class, [
                'label' => static::LABEL_MERCHANT_REFERENCE,
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 255,
                    ]),
                    new Callback([
                        'callback' => $this->getExistingMerchantReferenceValidationCallback($currentMerchantId),
                    ]),
                ],
            ]);

        return $this;
    }

    /**
     * @return \Symfony\Component\Validator\Constraint[]
     */
    protected function getTextFieldConstraints(): array
    {
        return [
            new Required(),
            new NotBlank(),
            new Length(['max' => 255]),
        ];
    }

    /**
     * @return \Symfony\Component\Validator\Constraint[]
     */
    protected function getPhoneFieldConstraints(): array
    {
        return [
            new Required(),
            new NotBlank(),
            new Length(['max' => 255]),
        ];
    }

    /**
     * @param int|null $currentId
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    protected function getEmailFieldConstraints(?int $currentId = null): array
    {
        $merchantFacade = $this->getFactory()->getMerchantFacade();

        return [
            new Required(),
            new NotBlank(),
            new Email(),
            new Length(['max' => 255]),
            new Callback([
                'callback' => $this->getExistingEmailValidationCallback($currentId, $merchantFacade),
            ]),
        ];
    }

    /**
     * @param array $choices
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    protected function getSalutationFieldConstraints(array $choices = []): array
    {
        return [
            new Required(),
            new NotBlank(),
            new Length(['max' => 64]),
            new Choice(['choices' => array_keys($choices)]),
        ];
    }

    /**
     * @param int|null $currentId
     * @param \Spryker\Zed\MerchantGui\Dependency\Facade\MerchantGuiToMerchantFacadeInterface $merchantFacade
     *
     * @return callable
     */
    protected function getExistingEmailValidationCallback(?int $currentId, MerchantGuiToMerchantFacadeInterface $merchantFacade): callable
    {
        return function ($email, ExecutionContextInterface $context) use ($merchantFacade, $currentId) {
            $merchantCriteriaFilterTransfer = new MerchantCriteriaFilterTransfer();
            $merchantCriteriaFilterTransfer->setEmail($email);
            $merchantTransfer = $merchantFacade->findOne($merchantCriteriaFilterTransfer);
            if ($merchantTransfer !== null && ($currentId === null || $merchantTransfer->getIdMerchant() !== $currentId)) {
                $context->addViolation('Email is already used.');
            }
        };
    }

    /**
     * @param int|null $currentId
     *
     * @return callable
     */
    protected function getExistingMerchantReferenceValidationCallback(?int $currentId): callable
    {
        return function (string $merchantReference, ExecutionContextInterface $context) use ($currentId) {
            $merchantCriteriaFilterTransfer = new MerchantCriteriaFilterTransfer();
            $merchantCriteriaFilterTransfer->setMerchantReference($merchantReference);
            $merchantTransfer = $this->getFactory()->getMerchantFacade()->findOne($merchantCriteriaFilterTransfer);

            if ($merchantTransfer === null) {
                return;
            }

            if ($currentId && $merchantTransfer->getIdMerchant() === $currentId) {
                return;
            }

            $context->addViolation('Merchant reference is already used.');
        };
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return $this
     */
    protected function executeMerchantProfileFormExpanderPlugins(FormBuilderInterface $builder, array $options)
    {
        foreach ($this->getFactory()->getMerchantProfileFormExpanderPlugins() as $formExpanderPlugin) {
            $builder = $formExpanderPlugin->expand($builder, $options);
        }

        return $this;
    }
}
