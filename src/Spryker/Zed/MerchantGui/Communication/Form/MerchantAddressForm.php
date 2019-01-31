<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantGui\Communication\Form;

use Spryker\Zed\Kernel\Communication\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Required;

/**
 * @method \Spryker\Zed\MerchantGui\Communication\MerchantGuiCommunicationFactory getFactory()
 * @method \Spryker\Zed\MerchantGui\MerchantGuiConfig getConfig()
 */
class MerchantAddressForm extends AbstractType
{
    public const OPTION_COUNTRY_CHOICES = 'country_choices';

    protected const FIELD_ID_MERCHANT_ADDRESS = 'id_merchant_address';
    protected const FIELD_CITY = 'city';
    protected const FIELD_ZIP_CODE = 'zip_code';
    protected const FIELD_FK_COUNTRY = 'fk_country';
    protected const FIELD_ADDRESS_1 = 'address1';
    protected const FIELD_ADDRESS_2 = 'address2';
    protected const FIELD_ADDRESS_3 = 'address3';

    protected const LABEL_CITY = 'City';
    protected const LABEL_ZIP_CODE = 'Zip Code';
    protected const LABEL_FK_COUNTRY = 'Country';
    protected const LABEL_ADDRESS_1 = 'Street';
    protected const LABEL_ADDRESS_2 = 'Number';
    protected const LABEL_ADDRESS_3 = 'Addition to address';

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(static::OPTION_COUNTRY_CHOICES);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->addIdMerchantAddressField($builder)
            ->addCountryField($builder, $options[static::OPTION_COUNTRY_CHOICES])
            ->addCityField($builder)
            ->addZipCodeField($builder)
            ->addAddress1Field($builder)
            ->addAddress2Field($builder)
            ->addAddress3Field($builder);

        $builder->addModelTransformer($this->getFactory()->createArrayObjectToObjectTransformer());
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addIdMerchantAddressField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_ID_MERCHANT_ADDRESS, HiddenType::class);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $choices
     *
     * @return $this
     */
    protected function addCountryField(FormBuilderInterface $builder, array $choices = [])
    {
        $builder->add(static::FIELD_FK_COUNTRY, ChoiceType::class, [
            'label' => static::LABEL_FK_COUNTRY,
            'placeholder' => 'Select one',
            'choices' => array_flip($choices),
            'choices_as_values' => true,
            'constraints' => $this->getRequiredConstraints(),
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addCityField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_CITY, TextType::class, [
            'label' => static::LABEL_CITY,
            'required' => true,
            'constraints' => array_merge(
                $this->getRequiredConstraints(),
                $this->getLengthConstraint(255)
            ),
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addZipCodeField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_ZIP_CODE, TextType::class, [
            'label' => static::LABEL_ZIP_CODE,
            'required' => true,
            'constraints' => array_merge(
                $this->getRequiredConstraints(),
                $this->getLengthConstraint(10)
            ),
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addAddress1Field(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_ADDRESS_1, TextType::class, [
            'label' => static::LABEL_ADDRESS_1,
            'required' => true,
            'constraints' => array_merge(
                $this->getRequiredConstraints(),
                $this->getLengthConstraint(255)
            ),
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addAddress2Field(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_ADDRESS_2, TextType::class, [
            'label' => static::LABEL_ADDRESS_2,
            'required' => false,
            'constraints' => $this->getLengthConstraint(255),
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addAddress3Field(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_ADDRESS_3, TextType::class, [
            'label' => static::LABEL_ADDRESS_3,
            'required' => false,
            'constraints' => $this->getLengthConstraint(255),
        ]);

        return $this;
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'merchant_address';
    }

    /**
     * @return array
     */
    protected function getRequiredConstraints(): array
    {
        return [
            new Required(),
            new NotBlank(),
        ];
    }

    /**
     * @param int $length
     *
     * @return array
     */
    protected function getLengthConstraint(int $length): array
    {
        return [new Length(['max' => $length])];
    }
}
