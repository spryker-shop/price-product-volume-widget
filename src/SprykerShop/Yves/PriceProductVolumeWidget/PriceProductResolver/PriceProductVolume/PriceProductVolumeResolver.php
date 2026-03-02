<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\PriceProductVolumeWidget\PriceProductResolver\PriceProductVolume;

use Generated\Shared\Transfer\CurrentProductPriceTransfer;
use Generated\Shared\Transfer\PriceProductVolumeCollectionTransfer;
use Generated\Shared\Transfer\PriceProductVolumeTransfer;
use SprykerShop\Yves\PriceProductVolumeWidget\Dependency\Service\PriceProductVolumeWidgetToUtilEncodingServiceInterface;

class PriceProductVolumeResolver implements PriceProductVolumeResolverInterface
{
    /**
     * @see \Spryker\Shared\Price\PriceConfig::PRICE_MODE_NET
     *
     * @var string
     */
    protected const PRICE_MODE_NET = 'NET_MODE';

    /**
     * @see \Spryker\Shared\Price\PriceConfig::PRICE_MODE_GROSS
     *
     * @var string
     */
    protected const PRICE_MODE_GROSS = 'GROSS_MODE';

    /**
     * @see \Spryker\Shared\PriceProductVolume\VolumePriceProductConfig::VOLUME_PRICE_TYPE
     *
     * @var string
     */
    protected const VOLUME_PRICE_TYPE = 'volume_prices';

    /**
     * @var string
     */
    protected const VOLUME_PRICE_QUANTITY = 'quantity';

    /**
     * @var array
     */
    protected const VOLUME_PRICE_MODE_MAPPING = [
        self::PRICE_MODE_NET => 'net_price',
        self::PRICE_MODE_GROSS => 'gross_price',
    ];

    /**
     * @var \SprykerShop\Yves\PriceProductVolumeWidget\Dependency\Service\PriceProductVolumeWidgetToUtilEncodingServiceInterface
     */
    protected $utilEncodingService;

    public function __construct(PriceProductVolumeWidgetToUtilEncodingServiceInterface $utilEncodingService)
    {
        $this->utilEncodingService = $utilEncodingService;
    }

    public function resolveVolumeProductPrices(CurrentProductPriceTransfer $currentProductPriceTransfer): PriceProductVolumeCollectionTransfer
    {
        $priceProductVolumeCollectionTransfer = new PriceProductVolumeCollectionTransfer();
        $priceData = $this->utilEncodingService->decodeJson($currentProductPriceTransfer->getPriceData(), true);

        if ($priceData && isset($priceData[static::VOLUME_PRICE_TYPE])) {
            $priceProductVolumeCollectionTransfer = $this->mapVolumeProductPriceCollection(
                $priceData[static::VOLUME_PRICE_TYPE],
                $priceProductVolumeCollectionTransfer,
                $currentProductPriceTransfer->getPriceMode(),
            );
        }

        return $priceProductVolumeCollectionTransfer;
    }

    protected function mapVolumeProductPriceCollection(
        array $volumePriceData,
        PriceProductVolumeCollectionTransfer $priceProductVolumeCollection,
        string $priceMode
    ): PriceProductVolumeCollectionTransfer {
        foreach ($volumePriceData as $volumeProductStorageData) {
            if ($this->isVolumePriceDataValid($volumeProductStorageData, $priceMode)) {
                $priceProductVolumeCollection->addVolumePrice(
                    $this->formatPriceProductVolumeTransfer($volumeProductStorageData, $priceMode),
                );
            }
        }

        return $priceProductVolumeCollection;
    }

    protected function formatPriceProductVolumeTransfer(array $priceData, string $priceMode): PriceProductVolumeTransfer
    {
        $volumePrice = new PriceProductVolumeTransfer();
        $volumePrice->setQuantity(
            $priceData[static::VOLUME_PRICE_QUANTITY],
        );
        $volumePrice->setPrice(
            $priceData[static::VOLUME_PRICE_MODE_MAPPING[$priceMode]],
        );

        return $volumePrice;
    }

    protected function isVolumePriceDataValid(array $priceData, string $priceMode): bool
    {
        if (
            !isset($priceData[static::VOLUME_PRICE_QUANTITY])
            || !is_numeric($priceData[static::VOLUME_PRICE_QUANTITY])
        ) {
            return false;
        }

        if (
            !isset($priceData[static::VOLUME_PRICE_MODE_MAPPING[$priceMode]])
            || !is_numeric($priceData[static::VOLUME_PRICE_MODE_MAPPING[$priceMode]])
        ) {
            return false;
        }

        return true;
    }
}
