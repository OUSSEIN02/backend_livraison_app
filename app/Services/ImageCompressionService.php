<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class ImageCompressionService
{
    private ImageManager $imageManager;

    private int $quality;
    private int $maxWidth;
    private int $maxHeight;


    public function __construct()
    {
        $this->imageManager = new ImageManager(
            new Driver()
        );

        $this->quality = 80;
        $this->maxWidth = 1920;
        $this->maxHeight = 1080;
    }


    /**
     * Compresse et stocke une image en WebP
     */
    public function compressAndStore(
        UploadedFile $file,
        string $destinationPath
    ): string {


        /**
         * Intervention Image 4.x
         * Lecture du fichier uploadé
         */
        $image = $this->imageManager->decode($file);


        /**
         * Redimensionnement proportionnel
         */
        if (
            $image->width() > $this->maxWidth ||
            $image->height() > $this->maxHeight
        ) {

            $image->scale(
                width: $this->maxWidth,
                height: $this->maxHeight
            );
        }


        /**
         * Nom du fichier final
         */
        $filename = uniqid() . '.webp';


        $fullPath = $destinationPath . '/' . $filename;


        /**
         * Création du dossier
         */
        $directory = storage_path(
            'app/public/' . $destinationPath
        );


        if (!File::exists($directory)) {
            File::makeDirectory(
                $directory,
                0755,
                true
            );
        }


        /**
         * Encodage WebP
         */
        $encodedImage = $image->encodeUsingFileExtension(
            'webp',
            quality: $this->quality
        );


        /**
         * Sauvegarde
         */
        $encodedImage->save(
            storage_path(
                'app/public/' . $fullPath
            )
        );


        return $fullPath;
    }



    public function setQuality(int $quality): self
    {
        $this->quality = $quality;

        return $this;
    }



    public function setMaxDimensions(
        int $width,
        int $height
    ): self {

        $this->maxWidth = $width;
        $this->maxHeight = $height;

        return $this;
    }
}