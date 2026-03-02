<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ShipmentTypeStorage;

use Spryker\Client\Kernel\AbstractFactory;
use Spryker\Client\ShipmentTypeStorage\Dependency\Client\ShipmentTypeStorageToStorageClientInterface;
use Spryker\Client\ShipmentTypeStorage\Dependency\Service\ShipmentTypeStorageToSynchronizationServiceInterface;
use Spryker\Client\ShipmentTypeStorage\Dependency\Service\ShipmentTypeStorageToUtilEncodingServiceInterface;
use Spryker\Client\ShipmentTypeStorage\Generator\ShipmentTypeStorageKeyGenerator;
use Spryker\Client\ShipmentTypeStorage\Generator\ShipmentTypeStorageKeyGeneratorInterface;
use Spryker\Client\ShipmentTypeStorage\Reader\ShipmentTypeReader;
use Spryker\Client\ShipmentTypeStorage\Reader\ShipmentTypeReaderInterface;
use Spryker\Client\ShipmentTypeStorage\Reader\ShipmentTypeStorageReader;
use Spryker\Client\ShipmentTypeStorage\Reader\ShipmentTypeStorageReaderInterface;
use Spryker\Client\ShipmentTypeStorage\Scanner\ShipmentTypeStorageKeyScanner;
use Spryker\Client\ShipmentTypeStorage\Scanner\ShipmentTypeStorageKeyScannerInterface;

/**
 * @method \Spryker\Client\ShipmentTypeStorage\ShipmentTypeStorageConfig getConfig()
 */
class ShipmentTypeStorageFactory extends AbstractFactory
{
    public function createShipmentTypeStorageReader(): ShipmentTypeStorageReaderInterface
    {
        return new ShipmentTypeStorageReader(
            $this->createShipmentTypeStorageKeyGenerator(),
            $this->getStorageClient(),
            $this->getUtilEncodingService(),
            $this->createShipmentTypeStorageKeyScanner(),
            $this->getShipmentTypeStorageExpanderPlugins(),
            $this->getSynchronizationService(),
        );
    }

    /**
     * @deprecated Exists for BC reasons. Will be removed in the next major release.
     *
     * @return \Spryker\Client\ShipmentTypeStorage\Scanner\ShipmentTypeStorageKeyScannerInterface
     */
    public function createShipmentTypeStorageKeyScanner(): ShipmentTypeStorageKeyScannerInterface
    {
        return new ShipmentTypeStorageKeyScanner(
            $this->getStorageClient(),
            $this->getConfig(),
        );
    }

    public function createShipmentTypeReader(): ShipmentTypeReaderInterface
    {
        return new ShipmentTypeReader(
            $this->createShipmentTypeStorageReader(),
            $this->getAvailableShipmentTypeFilterPlugins(),
        );
    }

    public function createShipmentTypeStorageKeyGenerator(): ShipmentTypeStorageKeyGeneratorInterface
    {
        return new ShipmentTypeStorageKeyGenerator($this->getSynchronizationService());
    }

    public function getStorageClient(): ShipmentTypeStorageToStorageClientInterface
    {
        return $this->getProvidedDependency(ShipmentTypeStorageDependencyProvider::CLIENT_STORAGE);
    }

    public function getSynchronizationService(): ShipmentTypeStorageToSynchronizationServiceInterface
    {
        return $this->getProvidedDependency(ShipmentTypeStorageDependencyProvider::SERVICE_SYNCHRONIZATION);
    }

    public function getUtilEncodingService(): ShipmentTypeStorageToUtilEncodingServiceInterface
    {
        return $this->getProvidedDependency(ShipmentTypeStorageDependencyProvider::SERVICE_UTIL_ENCODING);
    }

    /**
     * @return list<\Spryker\Client\ShipmentTypeStorageExtension\Dependency\Plugin\AvailableShipmentTypeFilterPluginInterface>
     */
    public function getAvailableShipmentTypeFilterPlugins(): array
    {
        return $this->getProvidedDependency(ShipmentTypeStorageDependencyProvider::PLUGINS_AVAILABLE_SHIPMENT_TYPE_FILTER);
    }

    /**
     * @return list<\Spryker\Client\ShipmentTypeStorageExtension\Dependency\Plugin\ShipmentTypeStorageExpanderPluginInterface>
     */
    public function getShipmentTypeStorageExpanderPlugins(): array
    {
        return $this->getProvidedDependency(ShipmentTypeStorageDependencyProvider::PLUGINS_SHIPMENT_TYPE_STORAGE_EXPANDER);
    }
}
