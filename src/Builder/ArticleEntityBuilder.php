<?php declare(strict_types=1);

namespace App\Builder;

use App\DTO\ArticleDTO;
use App\Entity\Article;
use App\Entity\Photo;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class ArticleEntityBuilder
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    public function buildFromDTO(ArticleDTO $articleDTO): Article
    {
        $article = new Article();

        $article
            ->setTitle($articleDTO->title)
            ->setContent($articleDTO->content)
            ->setPhoto($this->getPhoto($articleDTO))
            ->setAuthor($this->getAuthor($articleDTO));

        return $article;
    }

    private function getAuthor(ArticleDTO $articleDTO): User
    {
        $userRepository = $this->em->getRepository(User::class);

        /** @var User|null $author */
        $author = $userRepository->find($articleDTO->author->id);

        if ($author === null) {
            throw new NotFoundHttpException('User not found');
        }

        return $author;
    }

    public function getPhoto(ArticleDTO $articleDTO): Photo
    {
        $photoRepository = $this->em->getRepository(Photo::class);

        /** @var Photo|null $photo */
        $photo = $photoRepository->find($articleDTO->photo->id);

        if ($photo === null) {
            throw new NotFoundHttpException('Photo not found');
        }

        return $photo;
    }
}
