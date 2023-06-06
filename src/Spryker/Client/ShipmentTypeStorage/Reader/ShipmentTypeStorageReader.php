<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ShipmentTypeStorage\Reader;

use Generated\Shared\Transfer\ShipmentTypeStorageCollectionTransfer;
use Generated\Shared\Transfer\ShipmentTypeStorageConditionsTransfer;
use Generated\Shared\Transfer\ShipmentTypeStorageCriteriaTransfer;
use Generated\Shared\Transfer\ShipmentTypeStorageTransfer;
use Spryker\Client\ShipmentTypeStorage\Dependency\Client\ShipmentTypeStorageToStorageClientInterface;
use Spryker\Client\ShipmentTypeStorage\Dependency\Service\ShipmentTypeStorageToUtilEncodingServiceInterface;
use Spryker\Client\ShipmentTypeStorage\Generator\ShipmentTypeStorageKeyGeneratorInterface;

class ShipmentTypeStorageReader implements ShipmentTypeStorageReaderInterface
{
    /**
     * @var string
     */
    protected const KEY_ID = 'id';

    /**
     * @var \Spryker\Client\ShipmentTypeStorage\Generator\ShipmentTypeStorageKeyGeneratorInterface
     */
    protected ShipmentTypeStorageKeyGeneratorInterface $shipmentTypeStorageKeyGenerator;

    /**
     * @var \Spryker\Client\ShipmentTypeStorage\Dependency\Client\ShipmentTypeStorageToStorageClientInterface
     */
    protected ShipmentTypeStorageToStorageClientInterface $storageClient;

    /**
     * @var \Spryker\Client\ShipmentTypeStorage\Dependency\Service\ShipmentTypeStorageToUtilEncodingServiceInterface
     */
    protected ShipmentTypeStorageToUtilEncodingServiceInterface $utilEncodingService;

    /**
     * @param \Spryker\Client\ShipmentTypeStorage\Generator\ShipmentTypeStorageKeyGeneratorInterface $shipmentTypeStorageKeyGenerator
     * @param \Spryker\Client\ShipmentTypeStorage\Dependency\Client\ShipmentTypeStorageToStorageClientInterface $storageClient
     * @param \Spryker\Client\ShipmentTypeStorage\Dependency\Service\ShipmentTypeStorageToUtilEncodingServiceInterface $utilEncodingService
     */
    public function __construct(
        ShipmentTypeStorageKeyGeneratorInterface $shipmentTypeStorageKeyGenerator,
        ShipmentTypeStorageToStorageClientInterface $storageClient,
        ShipmentTypeStorageToUtilEncodingServiceInterface $utilEncodingService
    ) {
        $this->shipmentTypeStorageKeyGenerator = $shipmentTypeStorageKeyGenerator;
        $this->storageClient = $storageClient;
        $this->utilEncodingService = $utilEncodingService;
    }

    /**
     * @param \Generated\Shared\Transfer\ShipmentTypeStorageCriteriaTransfer $shipmentTypeStorageCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\ShipmentTypeStorageCollectionTransfer
     */
    public function getShipmentTypeStorageCollection(
        ShipmentTypeStorageCriteriaTransfer $shipmentTypeStorageCriteriaTransfer
    ): ShipmentTypeStorageCollectionTransfer {
        $shipmentTypeStorageConditionsTransfer = $shipmentTypeStorageCriteriaTransfer->getShipmentTypeStorageConditionsOrFail();
        if ($shipmentTypeStorageConditionsTransfer->getUuids() !== []) {
            return $this->getShipmentTypeStorageByUuids($shipmentTypeStorageConditionsTransfer);
        }

        if ($shipmentTypeStorageConditionsTransfer->getShipmentTypeIds() !== []) {
            return $this->getShipmentTypeStorageByShipmentTypeIds($shipmentTypeStorageConditionsTransfer);
        }

        return new ShipmentTypeStorageCollectionTransfer();
    }

    /**
     * @param \Generated\Shared\Transfer\ShipmentTypeStorageConditionsTransfer $shipmentTypeStorageConditionsTransfer
     *
     * @return \Generated\Shared\Transfer\ShipmentTypeStorageCollectionTransfer
     */
    protected function getShipmentTypeStorageByUuids(
        ShipmentTypeStorageConditionsTransfer $shipmentTypeStorageConditionsTransfer
    ): ShipmentTypeStorageCollectionTransfer {
        $shipmentTypeStorageCollectionTransfer = new ShipmentTypeStorageCollectionTransfer();

        $storageMappingKeys = $this->shipmentTypeStorageKeyGenerator->generateShipmentTypeStorageUuidMappingKeys(
            $shipmentTypeStorageConditionsTransfer->getUuids(),
            $shipmentTypeStorageConditionsTransfer->getStoreNameOrFail(),
        );
        if ($storageMappingKeys === []) {
            return $shipmentTypeStorageCollectionTransfer;
        }

        $storageMappingData = array_filter($this->storageClient->getMulti($storageMappingKeys));
        if ($storageMappingData === []) {
            return $shipmentTypeStorageCollectionTransfer;
        }

        $shipmentTypeStorageConditionsTransfer->setShipmentTypeIds(
            $this->extractShipmentTypeIdsFromStorageMappingData($storageMappingData),
        );

        return $this->getShipmentTypeStorageByShipmentTypeIds($shipmentTypeStorageConditionsTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\ShipmentTypeStorageConditionsTransfer $shipmentTypeStorageConditionsTransfer
     *
     * @return \Generated\Shared\Transfer\ShipmentTypeStorageCollectionTransfer
     */
    protected function getShipmentTypeStorageByShipmentTypeIds(
        ShipmentTypeStorageConditionsTransfer $shipmentTypeStorageConditionsTransfer
    ): ShipmentTypeStorageCollectionTransfer {
        $shipmentTypeStorageCollectionTransfer = new ShipmentTypeStorageCollectionTransfer();

        $storageKeys = $this->shipmentTypeStorageKeyGenerator->generateShipmentTypeStorageKeys(
            $shipmentTypeStorageConditionsTransfer->getShipmentTypeIds(),
            $shipmentTypeStorageConditionsTransfer->getStoreNameOrFail(),
        );
        if ($storageKeys === []) {
            return $shipmentTypeStorageCollectionTransfer;
        }

        $shipmentTypeStorageData = array_filter($this->storageClient->getMulti($storageKeys));
        if ($shipmentTypeStorageData === []) {
            return $shipmentTypeStorageCollectionTransfer;
        }

        /** @var array<string, mixed>|string $shipmentTypeStorageDataItem */
        foreach ($shipmentTypeStorageData as $shipmentTypeStorageDataItem) {
            if (!is_array($shipmentTypeStorageDataItem)) {
                $shipmentTypeStorageDataItem = $this->utilEncodingService->decodeJson($shipmentTypeStorageDataItem, true);
            }
            if (!$shipmentTypeStorageDataItem) {
                continue;
            }

            $shipmentTypeStorageCollectionTransfer->addShipmentTypeStorage(
                (new ShipmentTypeStorageTransfer())->fromArray($shipmentTypeStorageDataItem, true),
            );
        }

        return $shipmentTypeStorageCollectionTransfer;
    }

    /**
     * @param array<string, array<string, string>|string> $storageMappingData
     *
     * @return list<int>
     */
    protected function extractShipmentTypeIdsFromStorageMappingData(array $storageMappingData): array
    {
        $shipmentTypeIds = [];
        foreach ($storageMappingData as $storageMappingDataItem) {
            if (!is_array($storageMappingDataItem)) {
                $storageMappingDataItem = $this->utilEncodingService->decodeJson($storageMappingDataItem, true);
            }

            if ($storageMappingDataItem === [] || !isset($storageMappingDataItem[static::KEY_ID])) {
                continue;
            }

            $shipmentTypeIds[] = (int)$storageMappingDataItem[static::KEY_ID];
        }

        return $shipmentTypeIds;
    }
}
