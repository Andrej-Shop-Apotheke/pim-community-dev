<?php

declare(strict_types=1);

namespace Akeneo\Connectivity\Connection\Infrastructure\Persistence\Dbal\Query;

use Akeneo\Connectivity\Connection\Domain\Settings\Model\Read\Connection;
use Akeneo\Connectivity\Connection\Domain\Settings\Model\ValueObject\ConnectionType;
use Akeneo\Connectivity\Connection\Domain\Settings\Persistence\Query\SelectConnectionsQuery;
use Doctrine\DBAL\Connection as DbalConnection;

/**
 * @author Romain Monceau <romain@akeneo.com>
 * @copyright 2019 Akeneo SAS (http://www.akeneo.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
class DbalSelectConnectionsQuery implements SelectConnectionsQuery
{
    private DbalConnection $dbalConnection;

    public function __construct(DbalConnection $dbalConnection)
    {
        $this->dbalConnection = $dbalConnection;
    }

    /**
     * @param string[] $types
     * @return Connection[]
     */
    public function execute(array $types = []): array
    {
        $selectSQL = <<<SQL
SELECT code, label, flow_type, image, auditable
FROM akeneo_connectivity_connection
WHERE type IN (:types)
ORDER BY created ASC
SQL;

        $dataRows = $this->dbalConnection
            ->executeQuery(
                $selectSQL,
                [
                    'types' => $types,
                ],
                [
                    'types' => DbalConnection::PARAM_STR_ARRAY,
                ]
            )
            ->fetchAllAssociative();

        $connections = [];
        foreach ($dataRows as $dataRow) {
            $connections[] = new Connection(
                $dataRow['code'],
                $dataRow['label'],
                $dataRow['flow_type'],
                $dataRow['image'],
                (bool) $dataRow['auditable']
            );
        }

        return $connections;
    }
}
