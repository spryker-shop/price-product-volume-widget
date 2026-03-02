<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\PriceProductVolumeWidget\Widget;

use Generated\Shared\Transfer\CurrentProductPriceTransfer;
use Generated\Shared\Transfer\PriceProductVolumeCollectionTransfer;
use Spryker\Yves\Kernel\Widget\AbstractWidget;

/**
 * @method \SprykerShop\Yves\PriceProductVolumeWidget\PriceProductVolumeWidgetFactory getFactory()
 */
class CurrentProductPriceVolumeWidget extends AbstractWidget
{
    public function __construct(CurrentProductPriceTransfer $currentProductPriceTransfer)
    {
        $this->addParameter('product', $currentProductPriceTransfer)
            ->addParameter(
                'volumeProductPrices',
                $this->getPriceProductVolume($currentProductPriceTransfer),
            );
    }

    public static function getName(): string
    {
        return 'CurrentProductPriceVolumeWidget';
    }

    public static function getTemplate(): string
    {
        return '@PriceProductVolumeWidget/views/volume-price-product-widget/volume-price-product.twig';
    }

    protected function getPriceProductVolume(CurrentProductPriceTransfer $currentProductPriceTransfer): PriceProductVolumeCollectionTransfer
    {
        return $this->getFactory()
            ->createPriceProductVolumeResolver()
            ->resolveVolumeProductPrices($currentProductPriceTransfer);
    }
}
