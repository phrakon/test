<?php

namespace app\models;

/**
 * @property-read int $id
 * @property string $name
 * @property string $email
 * @property string $text
 * @property string $image
 * @property bool $is_hidden
 * @property bool $is_updated
 * @property int $created_at
 */
class Post extends \core\DbModel
{
    /**
     * @var array
     */
    public $file;
    /**
     * @var string
     */
    public $filePath = '/app/web/upload/';
    /**
     * @var string
     */
    public $fileUrl = '/upload/';

    /**
     * {@inheritDoc}
     */
    public static function tableName()
    {
        return 'post';
    }

    /**
     * {@inheritDoc}
     */
    protected function getSchemaAttributes()
    {
        return ['id', 'name', 'email', 'text', 'image', 'is_hidden', 'is_updated', 'created_at'];
    }

    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return [
            [['name', 'email', 'text'], 'checkRequired'],
            [['!created_at'], 'checkDefault', 'value' => time()],
            [['text'], 'checkTrim'],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function save($runValidation = true)
    {
        if (!empty($this->file['tmp_name'])) {
            if (!$this->uploadFile()) {
                $this->addError('file', 'Не удалось загрузить файл');
                return false;
            }
        }

        $success = parent::save($runValidation);

        $this->file = null;

        return $success;
    }

    /**
     * @param int $maxWidth
     * @param int $maxHeight
     * @return bool
     */
    protected function uploadFile($maxWidth = 640, $maxHeight = 480)
    {
        $file = $this->file;
        $tmpFile = $file['tmp_name'];
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = uniqid() . '.' . $ext;
        $fullFileName = $this->filePath . $fileName;
        $this->image = $fileName;

        [$width, $height] = getimagesize($tmpFile);

        if (!$width || !$height) { //не картинка
            return false;
        }

        if ($width > $maxWidth || $height > $maxHeight) {

            $ratio = $width / $height;

            if ($ratio > 1) {
                $newWidth = $maxWidth;
                $newHeight = $maxHeight / $ratio;
            } else {
                $newWidth = $maxWidth * $ratio;
                $newHeight = $maxHeight;
            }

            if (in_array($ext, ['jpg', 'jpeg'])) {
                $image = imagecreatefromjpeg($tmpFile);
            } elseif ($ext === 'png') {
                $image = imagecreatefrompng($tmpFile);
            } else {
                return false;
            }

            $imgResized = imagescale($image, $newWidth, $newHeight);

            if ($imgResized === false) {
                return false;
            }

            if (in_array($ext, ['jpg', 'jpeg'])) {
                return imagejpeg($imgResized, $fullFileName, 80);
            }
            return imagepng($imgResized, $fullFileName);
        }

        return move_uploaded_file($tmpFile, $fullFileName);
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->fileUrl . $this->image;
    }

    /**
     * @return false|string
     */
    public function getDate()
    {
        return date("H:i j-m-Y", $this->created_at);
    }
}
