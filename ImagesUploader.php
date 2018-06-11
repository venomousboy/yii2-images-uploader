<?php

namespace venomousboy\yii2-images-uploader;

use yii\base\Model;
use yii\web\UploadedFile;

class ImagesUploader extends Model
{
    public $file;

    private $path;
    private $maxUploadFiles;
    private $images = [];

    public function rules()
    {
        return [
            [['file'], 'file', 'extensions' => 'gif, jpg, png'],
        ];
    }

    /**
     * @param UploadedFile[] $files
     * @param string $path
     * @param int $allowCountUploadFiles
     */
    public function handle(array $files, string $path, int $allowCountUploadFiles): void
    {
        $this->path = $path;
        $this->maxUploadFiles = $allowCountUploadFiles;
        $this->allowLimitUploadFiles($files);
        if (!$this->hasErrors()) {
            $this->validateImages($files);
            if (!$this->hasErrors()) {
                $this->uploads($files);
            }
        }
    }

    /**
     * @param UploadedFile[] $files
     */
    private function uploads(array $files): void
    {
        $errors = [];
        foreach ($files as $file) {
            if (!$this->upload($file)) {
                $errors['file'][$file->name] = 'File not saved';
            } else {
                $this->images[] = $this->compositeFileWithExtension($file);
            }
        }
        $this->addErrors($errors);
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    private function compositeFileWithExtension(UploadedFile $file): string
    {
        return str_replace('/tmp/', '', $file->tempName) . '.' .
            str_replace('image/', '', $file->type);
    }

    /**
     * @param UploadedFile $file
     * @return bool
     */
    private function upload(UploadedFile $file): bool
    {
        return $file->saveAs(
            \Yii::$app->basePath . $this->path .
            $this->compositeFileWithExtension($file)
        );
    }

    /**
     * @param UploadedFile[] $files
     */
    private function allowLimitUploadFiles(array $files): void
    {
        $errors = [];
        if (sizeof($files) > $this->maxUploadFiles) {
            $errors['file'][] = 'Max upload images';
            $this->addErrors($errors);
        }
    }

    /**
     * @param UploadedFile[] $files
     */
    private function validateImages(array $files): void
    {
        $errors = [];
        foreach ($files as $file) {
            $this->file = $file;
            $this->validate();
            if ($this->hasErrors()) {
                $errors['file'][] = $this->getFirstError('file');
            }
        }
        $this->clearErrors();
        $this->addErrors($errors);
    }

    /**
     * @return array
     */
    public function getImages(): array
    {
        return $this->images;
    }
}
