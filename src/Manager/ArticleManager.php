<?php declare(strict_types=1);

namespace App\Manager;

use App\Builder\ArticleEntityBuilder;
use App\DTO\ArticleDTO;
use App\DTO\DTOInterface;
use App\Entity\Article;
use App\Serializer\AccessGroup;
use Doctrine\ORM\EntityManagerInterface;
use Random\RandomException;
use ReflectionException;

final readonly class ArticleManager
{
    use AutoMapper;

    public function __construct(
        private ArticleEntityBuilder $articleEntityBuilder,
        private EntityManagerInterface $em
    ) {
    }

    /**
     * @throws ReflectionException
     * @throws RandomException
     */
    public function create(ArticleDTO $articleDTO): DTOInterface
    {
        $article = $this->articleEntityBuilder->buildFromDTO($articleDTO);

        $this->em->persist($article);
        $this->em->flush();

        return $this->mapToModel($article, AccessGroup::ARTICLE_READ);
    }

    public function remove(Article $article): void
    {
        $this->em->remove($article);
        $this->em->flush();
    }
}
