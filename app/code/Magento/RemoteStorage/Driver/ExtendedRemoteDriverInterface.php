<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\RemoteStorage\Driver;

use Magento\Framework\Filesystem\ExtendedDriverInterface;

interface ExtendedRemoteDriverInterface extends ExtendedDriverInterface
{
    /**
     * To check if directory exists
     *
     * @param string $path
     * @return bool
     */
    public function isDirectoryExists($path): bool;
}
