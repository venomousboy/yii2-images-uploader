# yii2-images-uploader
Images uploader via base yii2 model component

## Installation

Recommended installation via [composer](http://getcomposer.org/download/):

```
composer require venomousboy/yii2-images-uploader
```

## Usage

POST method body has form-data with arguments files[] and it has type file.

```
... $path - is dir save images
... $maxUploadFiles - limit of max upload files in POST
```

Class ImagesUploaderHelper extends \yii\base\Model. For example:
 
```php
/**
* @param string $path
* @param int $maxUploadFiles
* @return array 
* @throws Exception
*/
public function uploadImages(string $path, int $maxUploadFiles): array
{
    $files = UploadedFile::getInstancesByName('files');
    if (sizeof($files)) {
        $imagesUploader = new ImagesUploader();
        $imagesUploader->handle($files, $path, $maxUploadFiles);
        if ($imagesUploader->hasErrors()) {
            throw new \Exception(
                Json::encode($imagesUploader->getErrors())
            );
        }
        return $imagesUploader->getImages();
    }

    return $files;
}
```
