<?php

declare(strict_types=1);

namespace Akeneo\Category\Application\Command\CreateTemplate;

use Akeneo\Category\Application\Query\CheckTemplate;
use Akeneo\Category\Application\Query\GetCategoryTemplateByCategoryTree;
use Akeneo\Category\Application\Query\GetCategoryTreeByCategoryTemplate;
use Akeneo\Category\Application\Storage\Save\Saver\CategoryTemplateSaver;
use Akeneo\Category\Application\Storage\Save\Saver\CategoryTreeTemplateSaver;
use Akeneo\Category\Domain\Exceptions\ViolationsException;
use Akeneo\Category\Domain\Model\Enrichment\Category;
use Akeneo\Category\Domain\Query\GetCategoryInterface;
use Akeneo\Category\Domain\ValueObject\Template\TemplateCode;
use Akeneo\Category\Infrastructure\Builder\TemplateBuilder;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @copyright 2023 Akeneo SAS (https://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CreateTemplateCommandHandler
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly GetCategoryInterface $getCategory,
        private readonly GetCategoryTemplateByCategoryTree $getCategoryTemplateByCategoryTree,
        private readonly CheckTemplate $checkTemplate,
        private readonly TemplateBuilder $templateBuilder,
        private readonly CategoryTemplateSaver $categoryTemplateSaver,
        private readonly CategoryTreeTemplateSaver $categoryTreeTemplateSaver,
        private readonly GetCategoryTreeByCategoryTemplate $getCategoryTreeByCategoryTemplate,
    ) {
    }

    public function __invoke(CreateTemplateCommand $command): void
    {
        $categoryTreeId = $command->categoryTreeId;
        $templateCode = $command->templateCode;
        $templateLabelCollection = $command->labels;

        $categoryTree = $this->getCategory->byId($categoryTreeId->getValue());
        if ($categoryTree === null) {
            throw new \RuntimeException(sprintf('Category tree not found. Id: %d', $categoryTreeId->getValue()));
        }

        $violations = $this->validator->validate($command);
        if ($violations->count() > 0) {
            throw new ViolationsException($violations);
        }

        if (!$this->validateTemplateCreation($categoryTree, $templateCode)) {
            throw new \RuntimeException(\sprintf("Template for category tree '%s' cannot be activated.", $categoryTree->getCode()));
        }

        $templateToSave = $this->templateBuilder->generateTemplate(
            $categoryTreeId,
            $templateCode,
            $templateLabelCollection,
        );

        $this->categoryTemplateSaver->insert($templateToSave);
        if (($this->getCategoryTreeByCategoryTemplate)($templateToSave->getUuid()) === null) {
            $this->categoryTreeTemplateSaver->insert($templateToSave);
        }
    }

    /**
     * A template creation is considered valid if:
     *  - the current category tree has no template attached
     *  - the attached category id is the root of a category tree
     *  - the template code is free to use.
     *
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    private function validateTemplateCreation(Category $categoryTree, TemplateCode $templateCode): bool
    {
        if (($this->getCategoryTemplateByCategoryTree)($categoryTree->getId())) {
            return false;
        }

        if ($categoryTree->getParentId() !== null) {
            return false;
        }

        if ($this->checkTemplate->codeExists($templateCode)) {
            return false;
        }

        return true;
    }
}
