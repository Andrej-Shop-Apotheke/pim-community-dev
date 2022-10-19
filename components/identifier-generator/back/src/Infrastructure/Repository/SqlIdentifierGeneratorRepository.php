<?php

declare(strict_types=1);

namespace Akeneo\Pim\Automation\IdentifierGenerator\Infrastructure\Repository;

use Akeneo\Pim\Automation\IdentifierGenerator\Domain\Exception\UnableToFetchIdentifierGeneratorException;
use Akeneo\Pim\Automation\IdentifierGenerator\Domain\Exception\UnableToSaveIdentifierGeneratorException;
use Akeneo\Pim\Automation\IdentifierGenerator\Domain\Model\Condition\Conditions;
use Akeneo\Pim\Automation\IdentifierGenerator\Domain\Model\Delimiter;
use Akeneo\Pim\Automation\IdentifierGenerator\Domain\Model\IdentifierGenerator;
use Akeneo\Pim\Automation\IdentifierGenerator\Domain\Model\IdentifierGeneratorCode;
use Akeneo\Pim\Automation\IdentifierGenerator\Domain\Model\IdentifierGeneratorId;
use Akeneo\Pim\Automation\IdentifierGenerator\Domain\Model\LabelCollection;
use Akeneo\Pim\Automation\IdentifierGenerator\Domain\Model\Structure;
use Akeneo\Pim\Automation\IdentifierGenerator\Domain\Model\Target;
use Akeneo\Pim\Automation\IdentifierGenerator\Domain\Repository\IdentifierGeneratorRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Ramsey\Uuid\Uuid;

/**
 * @copyright 2022 Akeneo SAS (https://www.akeneo.com)
 * @license   https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class SqlIdentifierGeneratorRepository implements IdentifierGeneratorRepository
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function save(IdentifierGenerator $identifierGenerator): void
    {
        $query = <<<SQL
INSERT INTO pim_catalog_identifier_generator (uuid, code, target, delimiter, labels, conditions, structure)
VALUES (UUID_TO_BIN(:uuid), :code, :target, :delimiter, :labels, :conditions, :structure);
SQL;

        try {
            $this->connection->executeStatement($query, [
                'uuid' => $identifierGenerator->id()->asString(),
                'code' => $identifierGenerator->code()->asString(),
                'target' => $identifierGenerator->target()->asString(),
                'delimiter' => $identifierGenerator->delimiter()->asString(),
                'labels' => json_encode($identifierGenerator->labelCollection()->normalize()),
                'conditions' => json_encode($identifierGenerator->conditions()->normalize()),
                'structure' => json_encode($identifierGenerator->structure()->normalize()),
            ]);
        } catch (Exception $e) {
            throw new UnableToSaveIdentifierGeneratorException(sprintf('Cannot save the identifier generator "%s"', $identifierGenerator->code()->asString()), 0, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $identifierGeneratorCode): ?IdentifierGenerator
    {
        if ('' === trim($identifierGeneratorCode)) {
            return null;
        }

        $stmt = $this->connection->prepare('select BIN_TO_UUID(uuid) AS uuid, code, conditions, structure, labels, target, delimiter from pim_catalog_identifier_generator where code=:code');
        $stmt->bindParam('code', $identifierGeneratorCode, \PDO::PARAM_STR);

        try {
            $result = $stmt->executeQuery()->fetchAssociative();
        } catch (\Doctrine\DBAL\Driver\Exception $e) {
            throw new UnableToFetchIdentifierGeneratorException(sprintf('Cannot fetch the identifier generator "%s"', $identifierGeneratorCode));
        }

        if (!$result) {
            return null;
        }

        return new IdentifierGenerator(
            IdentifierGeneratorId::fromString($result['uuid']),
            IdentifierGeneratorCode::fromString($result['code']),
            Conditions::fromArray(json_decode($result['conditions'], true)),
            Structure::fromNormalized(json_decode($result['structure'], true)),
            LabelCollection::fromNormalized(json_decode($result['labels'], true)),
            Target::fromString($result['target']),
            Delimiter::fromString($result['delimiter']),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getNextId(): IdentifierGeneratorId
    {
        return IdentifierGeneratorId::fromString(Uuid::uuid4()->toString());
    }

    public function count(): int
    {
        throw new \Exception('Not implemented yet');
    }
}