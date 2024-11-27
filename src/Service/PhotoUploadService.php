<?php

namespace App\Service;

use App\DTO\DTOInterface;
use App\Entity\Photo;
use App\Manager\AutoMapper;
use App\Serializer\AccessGroup;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\String\Slugger\SluggerInterface;

final readonly class PhotoUploadService
{
    use AutoMapper;

    private const string UPLOAD_DIR = 'uploads/articles';
    private const array ALLOWED_MIME_TYPES = ['image/jpeg', 'image/png', 'image/webp'];

    public function __construct(
        private EntityManagerInterface $entityManager,
        private SluggerInterface $slugger,
        #[Autowire('%kernel.project_dir%/public')]
        private string $publicDir,
    ) {}

    public function upload(?UploadedFile $file): DTOInterface
    {
        if (!$file) {
            throw new BadRequestHttpException('No file uploaded');
        }

        $mimeType = $file->getClientMimeType();

        if (!in_array($mimeType, self::ALLOWED_MIME_TYPES, true)) {
            throw new BadRequestHttpException('Invalid file type. Allowed types: JPG, PNG, WEBP');
        }

        $uploadPath = sprintf('%s/%s', $this->publicDir, self::UPLOAD_DIR);
        if (!is_dir($uploadPath) && !mkdir($uploadPath, 0777, true) && !is_dir($uploadPath)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $uploadPath));
        }

        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = sprintf(
            '%s-%s.%s',
            $safeFilename,
            uniqid('', true),
            $file->guessExtension()
        );

        $file->move($uploadPath, $newFilename);

        $photo = new Photo();
        $photo->setFilename($originalFilename)
            ->setPath(sprintf('%s/%s', self::UPLOAD_DIR, $newFilename))
            ->setMimeType($mimeType);

        $this->entityManager->persist($photo);
        $this->entityManager->flush();

        return $this->mapToModel($photo, AccessGroup::PHOTO_READ);
    }
}
