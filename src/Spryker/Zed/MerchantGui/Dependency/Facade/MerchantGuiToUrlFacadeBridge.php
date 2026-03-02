<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantGui\Dependency\Facade;

use Generated\Shared\Transfer\UrlTransfer;

class MerchantGuiToUrlFacadeBridge implements MerchantGuiToUrlFacadeInterface
{
    /**
     * @var \Spryker\Zed\Url\Business\UrlFacadeInterface
     */
    protected $urlFacade;

    /**
     * @param \Spryker\Zed\Url\Business\UrlFacadeInterface $urlFacade
     */
    public function __construct($urlFacade)
    {
        $this->urlFacade = $urlFacade;
    }

    public function findUrlCaseInsensitive(UrlTransfer $urlTransfer): ?UrlTransfer
    {
        return $this->urlFacade->findUrlCaseInsensitive($urlTransfer);
    }

    public function hasUrlCaseInsensitive(UrlTransfer $urlTransfer): bool
    {
        return $this->urlFacade->hasUrlCaseInsensitive($urlTransfer);
    }
}
