<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantGui\Communication\Form\Constraint;

use Generated\Shared\Transfer\MerchantCriteriaTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Symfony\Component\Validator\Constraint;

class UniqueMerchantReference extends Constraint
{
    /**
     * @var string
     */
    public const OPTION_MERCHANT_FACADE = 'merchantFacade';

    /**
     * @var string
     */
    public const OPTION_CURRENT_MERCHANT_ID = 'currentMerchantId';

    /**
     * @var string
     */
    protected const VALIDATION_MESSAGE = 'Merchant reference is already used.';

    /**
     * @var \Spryker\Zed\MerchantGui\Dependency\Facade\MerchantGuiToMerchantFacadeInterface
     */
    protected $merchantFacade;

    /**
     * @var int|null
     */
    protected $currentMerchantId;

    public function getTargets(): string
    {
        return static::CLASS_CONSTRAINT;
    }

    public function getMessage(): string
    {
        return static::VALIDATION_MESSAGE;
    }

    public function getCurrentMerchantId(): ?int
    {
        return $this->currentMerchantId;
    }

    public function findMerchantByReference(string $merchantReference): ?MerchantTransfer
    {
        $merchantCriteriaTransfer = new MerchantCriteriaTransfer();
        $merchantCriteriaTransfer->setMerchantReference($merchantReference);

        return $this->merchantFacade->findOne($merchantCriteriaTransfer);
    }
}
