<?php

namespace App\Helper;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class Uploader
{
    public function __construct(private SluggerInterface $slugger) {}

    public function upload(UploadedFile $file, string $directory, string $name, string $oldFilePath = null): string
    {
        $newFileName = $this->slugger->slug($name) . '_' . uniqid() . '.' . $file->guessExtension();
        if ($oldFilePath && \file_exists($directory.$oldFilePath)) {
            unlink($directory.$oldFilePath);
        }

        return $file->move($directory, $newFileName)->getBasename();
    }
}