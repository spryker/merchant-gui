<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantGui\Dependency\Facade;

use Generated\Shared\Transfer\MerchantCollectionTransfer;
use Generated\Shared\Transfer\MerchantCriteriaTransfer;
use Generated\Shared\Transfer\MerchantResponseTransfer;
use Generated\Shared\Transfer\MerchantTransfer;

interface MerchantGuiToMerchantFacadeInterface
{
    public function createMerchant(MerchantTransfer $merchantTransfer): MerchantResponseTransfer;

    public function updateMerchant(MerchantTransfer $merchantTransfer): MerchantResponseTransfer;

    public function findOne(MerchantCriteriaTransfer $merchantCriteriaTransfer): ?MerchantTransfer;

    /**
     * @param string $currentStatus
     *
     * @return array<string>
     */
    public function getApplicableMerchantStatuses(string $currentStatus): array;

    public function get(MerchantCriteriaTransfer $merchantCriteriaTransfer): MerchantCollectionTransfer;
}
