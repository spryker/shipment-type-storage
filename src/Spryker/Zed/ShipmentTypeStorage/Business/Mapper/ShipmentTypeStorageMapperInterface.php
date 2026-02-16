<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ShipmentTypeStorage\Business\Mapper;

interface ShipmentTypeStorageMapperInterface
{
    /**
     * @param array<\Generated\Shared\Transfer\ShipmentTypeTransfer> $shipmentTypeTransfers
     * @param array<\Generated\Shared\Transfer\ShipmentTypeStorageTransfer> $shipmentTypeStorageTransfers
     *
     * @return array<\Generated\Shared\Transfer\ShipmentTypeStorageTransfer>
     */
    public function mapShipmentTypeTransfersToShipmentTypeStorageTransfers(
        array $shipmentTypeTransfers,
        array $shipmentTypeStorageTransfers
    ): array;
}
