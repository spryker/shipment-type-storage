<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\ShipmentTypeStorage\Business\Facade;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\EventEntityTransfer;
use Generated\Shared\Transfer\ShipmentCarrierTransfer;
use Generated\Shared\Transfer\ShipmentMethodTransfer;
use Generated\Shared\Transfer\ShipmentTypeTransfer;
use Generated\Shared\Transfer\StoreRelationTransfer;
use SprykerTest\Zed\ShipmentTypeStorage\ShipmentTypeStorageBusinessTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group ShipmentTypeStorage
 * @group Business
 * @group Facade
 * @group WriteShipmentTypeStorageCollectionByShipmentMethodEventsTest
 * Add your own group annotations below this line
 */
class WriteShipmentTypeStorageCollectionByShipmentMethodEventsTest extends Unit
{
    /**
     * @uses \Orm\Zed\Shipment\Persistence\Map\SpyShipmentMethodTableMap::COL_FK_SHIPMENT_TYPE
     *
     * @var string
     */
    protected const COL_FK_SHIPMENT_TYPE = 'spy_shipment_method.fk_shipment_type';

    /**
     * @var \SprykerTest\Zed\ShipmentTypeStorage\ShipmentTypeStorageBusinessTester
     */
    protected ShipmentTypeStorageBusinessTester $tester;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->tester->ensureShipmentTypeStorageTableIsEmpty();
        $this->tester->ensureStoreTableIsEmpty();
        $this->tester->setUpQueueAdapter();
        $this->tester->setUpShipmentTypeShipmentMethodCollectionExpanderPluginDependency();
    }

    /**
     * @return void
     */
    public function testPersistsShipmentTypeStorageData(): void
    {
        // Arrange
        $storeTransfer = $this->tester->haveStore();
        $shipmentTypeTransfer = $this->tester->haveShipmentType([
            ShipmentTypeTransfer::IS_ACTIVE => true,
            ShipmentTypeTransfer::STORE_RELATION => (new StoreRelationTransfer())->addStores($storeTransfer),
        ]);
        $shipmentMethodTransfer = $this->tester->haveShipmentMethodWithShipmentTypeRelation(
            $shipmentTypeTransfer,
            $storeTransfer,
            [ShipmentMethodTransfer::IS_ACTIVE => true],
        );

        $eventEntityTransfer = (new EventEntityTransfer())->setForeignKeys([
            static::COL_FK_SHIPMENT_TYPE => $shipmentTypeTransfer->getIdShipmentTypeOrFail(),
        ]);

        // Act
        $this->tester->getFacade()->writeShipmentTypeStorageCollectionByShipmentMethodEvents([$eventEntityTransfer]);

        // Assert
        $shipmentTypeStorageTransfer = $this->tester->findShipmentTypeStorageTransfer(
            $shipmentTypeTransfer->getIdShipmentTypeOrFail(),
            $storeTransfer->getNameOrFail(),
        );
        $this->assertNotNull($shipmentTypeStorageTransfer);
        $this->assertSame($shipmentTypeTransfer->getNameOrFail(), $shipmentTypeStorageTransfer->getName());
        $this->assertSame($shipmentTypeTransfer->getKeyOrFail(), $shipmentTypeStorageTransfer->getKey());
        $this->assertSame($shipmentTypeTransfer->getUuidOrFail(), $shipmentTypeStorageTransfer->getUuid());
        $this->assertSame($shipmentTypeTransfer->getIdShipmentTypeOrFail(), $shipmentTypeStorageTransfer->getIdShipmentType());
        $this->assertCount(1, $shipmentTypeStorageTransfer->getShipmentMethodIds());
        $this->assertContainsEquals($shipmentMethodTransfer->getIdShipmentMethodOrFail(), $shipmentTypeStorageTransfer->getShipmentMethodIds());
    }

    /**
     * @return void
     */
    public function testRemovesShipmentMethodIdWhenShipmentMethodIsDeactivated(): void
    {
        // Arrange
        $storeTransfer = $this->tester->haveStore();
        $shipmentTypeTransfer = $this->tester->haveShipmentType([
            ShipmentTypeTransfer::IS_ACTIVE => true,
            ShipmentTypeTransfer::STORE_RELATION => (new StoreRelationTransfer())->addStores($storeTransfer),
        ]);
        $shipmentMethodTransfer1 = $this->tester->haveShipmentMethodWithShipmentTypeRelation(
            $shipmentTypeTransfer,
            $storeTransfer,
            [ShipmentMethodTransfer::IS_ACTIVE => true],
        );
        $shipmentMethodTransfer2 = $this->tester->haveShipmentMethodWithShipmentTypeRelation(
            $shipmentTypeTransfer,
            $storeTransfer,
            [ShipmentMethodTransfer::IS_ACTIVE => false],
        );

        $shipmentTypeStorageTransfer = $this->tester->createShipmentTypeStorageTransfer($shipmentTypeTransfer);
        $shipmentTypeStorageTransfer->setShipmentMethodIds([
            $shipmentMethodTransfer1->getIdShipmentMethodOrFail(),
            $shipmentMethodTransfer2->getIdShipmentMethodOrFail(),
        ]);
        $this->tester->createShipmentTypeStorage($shipmentTypeStorageTransfer, $storeTransfer);

        $eventEntityTransfer = (new EventEntityTransfer())->setForeignKeys([
            static::COL_FK_SHIPMENT_TYPE => $shipmentTypeTransfer->getIdShipmentTypeOrFail(),
        ]);

        // Act
        $this->tester->getFacade()->writeShipmentTypeStorageCollectionByShipmentMethodEvents([$eventEntityTransfer]);

        // Assert
        $shipmentTypeStorageTransfer = $this->tester->findShipmentTypeStorageTransfer(
            $shipmentTypeTransfer->getIdShipmentTypeOrFail(),
            $storeTransfer->getNameOrFail(),
        );
        $this->assertNotNull($shipmentTypeStorageTransfer);
        $this->assertSame($shipmentTypeTransfer->getNameOrFail(), $shipmentTypeStorageTransfer->getName());
        $this->assertSame($shipmentTypeTransfer->getKeyOrFail(), $shipmentTypeStorageTransfer->getKey());
        $this->assertSame($shipmentTypeTransfer->getUuidOrFail(), $shipmentTypeStorageTransfer->getUuid());
        $this->assertSame($shipmentTypeTransfer->getIdShipmentTypeOrFail(), $shipmentTypeStorageTransfer->getIdShipmentType());
        $this->assertCount(1, $shipmentTypeStorageTransfer->getShipmentMethodIds());
        $this->assertContainsEquals($shipmentMethodTransfer1->getIdShipmentMethodOrFail(), $shipmentTypeStorageTransfer->getShipmentMethodIds());
        $this->assertNotContainsEquals($shipmentMethodTransfer2->getIdShipmentMethodOrFail(), $shipmentTypeStorageTransfer->getShipmentMethodIds());
    }

    /**
     * @return void
     */
    public function testRemovesShipmentMethodIdWhenShipmentMethodShipmentTypeRelationIsRemoved(): void
    {
        // Arrange
        $storeTransfer = $this->tester->haveStore();
        $shipmentTypeTransfer = $this->tester->haveShipmentType([
            ShipmentTypeTransfer::IS_ACTIVE => true,
            ShipmentTypeTransfer::STORE_RELATION => (new StoreRelationTransfer())->addStores($storeTransfer),
        ]);
        $shipmentMethodTransfer1 = $this->tester->haveShipmentMethod(
            [ShipmentMethodTransfer::IS_ACTIVE => true],
            [],
            [],
            [$storeTransfer->getIdStoreOrFail()],
        );
        $shipmentMethodTransfer2 = $this->tester->haveShipmentMethodWithShipmentTypeRelation(
            $shipmentTypeTransfer,
            $storeTransfer,
            [ShipmentMethodTransfer::IS_ACTIVE => true],
        );

        $shipmentTypeStorageTransfer = $this->tester->createShipmentTypeStorageTransfer($shipmentTypeTransfer);
        $shipmentTypeStorageTransfer->setShipmentMethodIds([
            $shipmentMethodTransfer1->getIdShipmentMethodOrFail(),
            $shipmentMethodTransfer2->getIdShipmentMethodOrFail(),
        ]);
        $this->tester->createShipmentTypeStorage($shipmentTypeStorageTransfer, $storeTransfer);

        $eventEntityTransfer = (new EventEntityTransfer())->setForeignKeys([
            static::COL_FK_SHIPMENT_TYPE => $shipmentTypeTransfer->getIdShipmentTypeOrFail(),
        ]);

        // Act
        $this->tester->getFacade()->writeShipmentTypeStorageCollectionByShipmentMethodEvents([$eventEntityTransfer]);

        // Assert
        $shipmentTypeStorageTransfer = $this->tester->findShipmentTypeStorageTransfer(
            $shipmentTypeTransfer->getIdShipmentTypeOrFail(),
            $storeTransfer->getNameOrFail(),
        );
        $this->assertNotNull($shipmentTypeStorageTransfer);
        $this->assertSame($shipmentTypeTransfer->getNameOrFail(), $shipmentTypeStorageTransfer->getName());
        $this->assertSame($shipmentTypeTransfer->getKeyOrFail(), $shipmentTypeStorageTransfer->getKey());
        $this->assertSame($shipmentTypeTransfer->getUuidOrFail(), $shipmentTypeStorageTransfer->getUuid());
        $this->assertSame($shipmentTypeTransfer->getIdShipmentTypeOrFail(), $shipmentTypeStorageTransfer->getIdShipmentType());
        $this->assertCount(1, $shipmentTypeStorageTransfer->getShipmentMethodIds());
        $this->assertContainsEquals($shipmentMethodTransfer2->getIdShipmentMethodOrFail(), $shipmentTypeStorageTransfer->getShipmentMethodIds());
        $this->assertNotContainsEquals($shipmentMethodTransfer1->getIdShipmentMethodOrFail(), $shipmentTypeStorageTransfer->getShipmentMethodIds());
    }

    /**
     * @return void
     */
    public function testRemovesShipmentMethodIdWhenShipmentMethodShipmentCarrierRelationIsUpdatedToInactiveCarrier(): void
    {
        // Arrange
        $storeTransfer = $this->tester->haveStore();
        $shipmentTypeTransfer = $this->tester->haveShipmentType([
            ShipmentTypeTransfer::IS_ACTIVE => true,
            ShipmentTypeTransfer::STORE_RELATION => (new StoreRelationTransfer())->addStores($storeTransfer),
        ]);
        $shipmentCarrierTransfer = $this->tester->haveShipmentCarrier([
            ShipmentCarrierTransfer::IS_ACTIVE => false,
        ]);
        $shipmentMethodTransfer = $this->tester->haveShipmentMethod(
            [ShipmentMethodTransfer::IS_ACTIVE => true],
            $shipmentCarrierTransfer->toArray(),
            [],
            [$storeTransfer->getIdStoreOrFail()],
        );

        $shipmentTypeStorageTransfer = $this->tester->createShipmentTypeStorageTransfer($shipmentTypeTransfer);
        $shipmentTypeStorageTransfer->addIdShipmentMethod($shipmentMethodTransfer->getIdShipmentMethodOrFail());
        $this->tester->createShipmentTypeStorage($shipmentTypeStorageTransfer, $storeTransfer);

        $eventEntityTransfer = (new EventEntityTransfer())->setForeignKeys([
            static::COL_FK_SHIPMENT_TYPE => $shipmentTypeTransfer->getIdShipmentTypeOrFail(),
        ]);

        // Act
        $this->tester->getFacade()->writeShipmentTypeStorageCollectionByShipmentMethodEvents([$eventEntityTransfer]);

        // Assert
        $shipmentTypeStorageTransfer = $this->tester->findShipmentTypeStorageTransfer(
            $shipmentTypeTransfer->getIdShipmentTypeOrFail(),
            $storeTransfer->getNameOrFail(),
        );
        $this->assertNotNull($shipmentTypeStorageTransfer);
        $this->assertSame($shipmentTypeTransfer->getNameOrFail(), $shipmentTypeStorageTransfer->getName());
        $this->assertSame($shipmentTypeTransfer->getKeyOrFail(), $shipmentTypeStorageTransfer->getKey());
        $this->assertSame($shipmentTypeTransfer->getUuidOrFail(), $shipmentTypeStorageTransfer->getUuid());
        $this->assertSame($shipmentTypeTransfer->getIdShipmentTypeOrFail(), $shipmentTypeStorageTransfer->getIdShipmentType());
        $this->assertCount(0, $shipmentTypeStorageTransfer->getShipmentMethodIds());
    }
}
