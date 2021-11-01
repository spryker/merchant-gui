<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantGui\Communication\Form;

use Generated\Shared\Transfer\UrlTransfer;
use Spryker\Zed\Kernel\Communication\Form\AbstractType;
use Spryker\Zed\MerchantGui\Communication\Form\Constraint\UniqueEmail;
use Spryker\Zed\MerchantGui\Communication\Form\Constraint\UniqueMerchantReference;
use Spryker\Zed\MerchantGui\Communication\Form\MerchantUrlCollection\MerchantUrlCollectionFormType;
use Spryker\Zed\MerchantGui\Communication\Form\Transformer\MerchantUrlCollectionDataTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @method \Spryker\Zed\MerchantGui\Communication\MerchantGuiCommunicationFactory getFactory()
 * @method \Spryker\Zed\MerchantGui\MerchantGuiConfig getConfig()
 */
class MerchantCreateForm extends AbstractType
{
    /**
     * @var string
     */
    public const OPTION_CURRENT_ID = 'current_id';

    /**
     * @var string
     */
    protected const FIELD_ID_MERCHANT = 'id_merchant';

    /**
     * @var string
     */
    protected const FIELD_NAME = 'name';

    /**
     * @var string
     */
    protected const FIELD_REGISTRATION_NUMBER = 'registration_number';

    /**
     * @var string
     */
    protected const FIELD_EMAIL = 'email';

    /**
     * @var string
     */
    protected const FIELD_MERCHANT_REFERENCE = 'merchant_reference';

    /**
     * @var string
     */
    protected const FIELD_IS_ACTIVE = 'is_active';

    /**
     * @var string
     */
    protected const FIELD_URL_COLLECTION = 'urlCollection';

    /**
     * @var string
     */
    protected const FIELD_STORE_RELATION = 'storeRelation';

    /**
     * @var string
     */
    protected const LABEL_NAME = 'Name';

    /**
     * @var string
     */
    protected const LABEL_REGISTRATION_NUMBER = 'Registration number';

    /**
     * @var string
     */
    protected const LABEL_EMAIL = 'Email';

    /**
     * @var string
     */
    protected const LABEL_MERCHANT_REFERENCE = 'Merchant Reference';

    /**
     * @var string
     */
    protected const LABEL_URL = 'Merchant URL';

    /**
     * @var string
     */
    protected const LABEL_IS_ACTIVE = 'Is Active';

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
     * @param array<string, mixed> $options
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
            ->addMerchantReferenceField($builder, $options[static::OPTION_CURRENT_ID])
            ->addIsActiveField($builder)
            ->addUrlCollectionField($builder)
            ->addStoreRelationForm($builder);

        $this->executeMerchantFormExpanderPlugins($builder, $options);
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
    protected function addIsActiveField(FormBuilderInterface $builder)
    {
        $builder
            ->add(static::FIELD_IS_ACTIVE, CheckboxType::class, [
                'label' => static::LABEL_IS_ACTIVE,
                'required' => false,
            ]);

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
    protected function addMerchantReferenceField(FormBuilderInterface $builder, ?int $currentMerchantId = null)
    {
        $builder
            ->add(static::FIELD_MERCHANT_REFERENCE, TextType::class, [
                'label' => static::LABEL_MERCHANT_REFERENCE,
                'required' => true,
                'constraints' => [
                    new Length([
                        'max' => 255,
                    ]),
                    new NotBlank(),
                    new UniqueMerchantReference([
                        UniqueMerchantReference::OPTION_CURRENT_MERCHANT_ID => $currentMerchantId,
                        UniqueMerchantReference::OPTION_MERCHANT_FACADE => $this->getFactory()->getMerchantFacade(),
                    ]),
                ],
            ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addStoreRelationForm(FormBuilderInterface $builder)
    {
        $builder->add(
            static::FIELD_STORE_RELATION,
            $this->getFactory()->getStoreRelationFormTypePlugin()->getType(),
            [
                'label' => false,
                'required' => false,
            ],
        );

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addUrlCollectionField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_URL_COLLECTION, CollectionType::class, [
            'entry_type' => MerchantUrlCollectionFormType::class,
            'allow_add' => true,
            'label' => static::LABEL_URL,
            'required' => true,
            'allow_delete' => true,
            'entry_options' => [
                'label' => false,
                'data_class' => UrlTransfer::class,
            ],
        ]);

        $builder->get(static::FIELD_URL_COLLECTION)
            ->addModelTransformer(new MerchantUrlCollectionDataTransformer());

        return $this;
    }

    /**
     * @return array<\Symfony\Component\Validator\Constraint>
     */
    protected function getTextFieldConstraints(): array
    {
        return [
            new NotBlank(),
            new Length(['max' => 255]),
        ];
    }

    /**
     * @return array<\Symfony\Component\Validator\Constraint>
     */
    protected function getPhoneFieldConstraints(): array
    {
        return [
            new NotBlank(),
            new Length(['max' => 255]),
        ];
    }

    /**
     * @param int|null $currentId
     *
     * @return array<\Symfony\Component\Validator\Constraint>
     */
    protected function getEmailFieldConstraints(?int $currentId = null): array
    {
        return [
            new NotBlank(),
            new Email(),
            new Length(['max' => 255]),
            new UniqueEmail([
                UniqueEmail::OPTION_MERCHANT_FACADE => $this->getFactory()->getMerchantFacade(),
                UniqueEmail::OPTION_CURRENT_ID_MERCHANT => $currentId,
            ]),
        ];
    }

    /**
     * @param array $choices
     *
     * @return array<\Symfony\Component\Validator\Constraint>
     */
    protected function getSalutationFieldConstraints(array $choices = []): array
    {
        return [
            new NotBlank(),
            new Length(['max' => 64]),
            new Choice(['choices' => array_keys($choices)]),
        ];
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return $this
     */
    protected function executeMerchantFormExpanderPlugins(FormBuilderInterface $builder, array $options)
    {
        foreach ($this->getFactory()->getMerchantFormExpanderPlugins() as $formExpanderPlugin) {
            $builder = $formExpanderPlugin->expand($builder, $options);
        }

        return $this;
    }
}
