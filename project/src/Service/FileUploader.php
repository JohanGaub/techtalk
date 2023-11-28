<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    public function __construct(
        private readonly string $userDirectory,
        private readonly SluggerInterface $slugger,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * @see https://symfony.com/doc/current/controller/upload_file.html
     */
    public function upload(UploadedFile $file): string {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = sprintf('%s-%s.%s', $safeFilename, uniqid(), $file->guessExtension());

        try {
            $file->move($this->userDirectory, $fileName);
        } catch (FileException $fileException) {
            $this->logger->error(sprintf('Failed to upload file %s: %s', $fileName, $fileException->getMessage()));
//            TODO: save errors in a variable OUTPUT AND, in the controller, use addFlashmessage to show errors.
        }

        return $fileName;
    }
}