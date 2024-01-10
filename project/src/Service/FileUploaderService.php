<?php

declare(strict_types=1);

namespace App\Service;

use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

readonly class FileUploaderService
{
    public function __construct(
        private string           $userDirectory,
        private SluggerInterface $slugger,
        private LoggerService    $loggerService
    ) {
    }

    /**
     * @see https://symfony.com/doc/current/controller/upload_file.html
     */
    public function upload(UploadedFile $file): string
    {
        $safeFileName = $this->createSafeFileName($file);

        try {
            $this->moveFile($file, $safeFileName);
        } catch (FileException $fileException) {
            /**
             * If moving the file fails, a FileException is thrown.
             * We catch this exception and log the error message along with the file name.
             */
            $this->loggerService->log(
                LogLevel::ERROR,
                'Failed to upload file %s.',
                [$safeFileName],
                $fileException,
            );

            /**
             * Instead of stopping the execution or returning an empty string,
             * we return a detailed error message to the caller.
             * This allows the caller to handle the error in a way that makes sense in their context.
             */
            return sprintf(
                'Error uploading the file %s. Error : %s',
                $safeFileName,
                $fileException->getMessage()
            );
        }

        return $safeFileName;
    }

    private function createSafeFileName(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);

        /**
         * time() method is strongly recommended to be used in combination with uniqid() method
         * to guarantee uniqueness especially under high loads.
         */
        return sprintf('%s-%s-%s.%s', $safeFilename, uniqid(), time(), $file->guessExtension());
    }

    private function moveFile(UploadedFile $file, string $safeFileName): void
    {
        $this->loggerService->log(LogLevel::INFO, 'Attempting to upload file: %s.', [$safeFileName]);

        try {
            $file->move($this->userDirectory, $safeFileName);
            $this->loggerService->log(LogLevel::INFO, 'File uploaded successfully: %s.', [$safeFileName]);
        } catch (FileException $fileException) {
            $errorMessage = sprintf(
                'Failed to upload file %s to directory %s: %s',
                $safeFileName,
                $this->userDirectory,
                $fileException->getMessage()
            );

            $this->loggerService->log(
                LogLevel::ERROR,
                'Failed to upload file %s to directory %s: %s',
                [
                    $safeFileName,
                    $this->userDirectory
                ],
                $fileException
            );

            /**
             * Include $fileException as the previous exception when creating a new FileException.
             * This ensures that the stack trace and other details from the original exception are retained.
             * The second argument is the code of the exception corresponding to the error level constants like
             * E_ERROR, E_WARNING and E_NOTICE, etc. But for most exceptions, this is usually set to '0'.
             */
            throw new FileException($errorMessage, 0, $fileException);
        }
    }
}
