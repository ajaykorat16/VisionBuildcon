<?php


namespace App\Uploader;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class ImageUploader
{
    private $slugger;
    private $targetDirectory;

    public function __construct(SluggerInterface $slugger,string $targetDirectory)
    {
        $this->slugger = $slugger;
        $this->targetDirectory = $targetDirectory;
    }
     public function upload(UploadedFile $File) : string
     {
         $originalFileName = pathinfo($File->getClientOriginalName(), PATHINFO_FILENAME);
         $safeFileName = $this->slugger->slug($originalFileName);
         $newFileName = $safeFileName.'_'.uniqid().'.'.$File->guessExtension();

         try{
             $File->move($this->targetDirectory, $newFileName);
         }catch (FileException $e){
             throw new \RuntimeException('Could not move the file');
         }

         return $newFileName;

     }
}