<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ShipmentTypeStorage\Dependency\Facade;

use Generated\Shared\Transfer\ShipmentMethodCollectionTransfer;
use Generated\Shared\Transfer\ShipmentMethodCriteriaTransfer;

class ShipmentTypeStorageToShipmentFacadeBridge implements ShipmentTypeStorageToShipmentFacadeInterface
{
    /**
     * @var \Spryker\Zed\Shipment\Business\ShipmentFacadeInterface
     */
    protected $shipmentFacade;

    /**
     * @param \Spryker\Zed\Shipment\Business\ShipmentFacadeInterface $shipmentFacade
     */
    public function __construct($shipmentFacade)
    {
        $this->shipmentFacade = $shipmentFacade;
    }

    public function getShipmentMethodCollection(
        ShipmentMethodCriteriaTransfer $shipmentMethodCriteriaTransfer
    ): ShipmentMethodCollectionTransfer {
        return $this->shipmentFacade->getShipmentMethodCollection($shipmentMethodCriteriaTransfer);
    }
}
