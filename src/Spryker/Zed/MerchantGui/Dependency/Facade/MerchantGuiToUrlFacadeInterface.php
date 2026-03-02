<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantGui\Dependency\Facade;

use Generated\Shared\Transfer\UrlTransfer;

interface MerchantGuiToUrlFacadeInterface
{
    public function findUrlCaseInsensitive(UrlTransfer $urlTransfer): ?UrlTransfer;

    public function hasUrlCaseInsensitive(UrlTransfer $urlTransfer): bool;
}
