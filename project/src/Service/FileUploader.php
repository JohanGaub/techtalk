<?php

declare(strict_types=1);

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

readonly class FileUploader
{
    public function __construct(
        private string           $userDirectory,
        private SluggerInterface $slugger,
        private LoggerInterface  $logger
    ) {
    }

    /**
     * @see https://symfony.com/doc/current/controller/upload_file.html
     */
    public function upload(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = sprintf('%s-%s.%s', $safeFilename, uniqid(), $file->guessExtension());

        try {
            $file->move($this->userDirectory, $fileName);
        } catch (FileException $fileException) {
            $this->logger->error(sprintf('Failed to upload file %s: %s', $fileName, $fileException->getMessage()));
        }

        return $fileName;
    }
}
