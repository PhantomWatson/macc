<?php
namespace App\Media;

use Cake\ORM\Entity;
use Cake\ORM\Table;
use Imagine\Image\Box;
use Josegonzalez\Upload\File\Transformer\TransformerInterface;

class Transformer implements TransformerInterface
{
    /**
     * Table instance.
     *
     * @var \Cake\ORM\Table
     */
    protected $table;

    /**
     * Entity instance.
     *
     * @var \Cake\ORM\Entity
     */
    protected $entity;

    /**
     * Array of uploaded data for this field
     *
     * @var array
     */
    protected $data;

    /**
     * Name of field
     *
     * @var string
     */
    protected $field;

    /**
     * Settings for processing a path
     *
     * @var array
     */
    protected $settings;

    /**
     * Constructor
     *
     * @param \Cake\ORM\Table  $table the instance managing the entity
     * @param \Cake\ORM\Entity $entity the entity to construct a path for.
     * @param array            $data the data being submitted for a save
     * @param string           $field the field for which data will be saved
     * @param array            $settings the settings for the current field
     */
    public function __construct(Table $table, Entity $entity, $data, $field, $settings)
    {
        $this->table = $table;
        $this->entity = $entity;
        $this->data = $data;
        $this->field = $field;
        $this->settings = $settings;
    }

    /**
     * Creates a set of files from the initial data and returns them as key/value
     * pairs, where the path on disk maps to name which each file should have.
     * Example:
     *
     *   [
     *     '/tmp/path/to/file/on/disk' => 'file.pdf',
     *     '/tmp/path/to/file/on/disk-2' => 'file-preview.png',
     *   ]
     *
     * @return array key/value pairs of temp files mapping to their names
     */
    public function transform()
    {
        $imagine = new \Imagine\Gd\Imagine();
        $image = $imagine->open($this->data['tmp_name']);
        $tmpPathParts = explode(DS, $this->data['tmp_name']);
        array_pop($tmpPathParts); // remove filename from path
        $tmpPath = implode(DS, $tmpPathParts);

        // Shrink image to max dimensions
        $maxDimension = 2000;
        $width = $image->getSize()->getWidth();
        $height = $image->getSize()->getHeight();
        if ($width > $maxDimension || $height > $maxDimension) {
            // Save resized image with microtime-prefixed name in tmp dir
            $size = new \Imagine\Image\Box(2000, 2000);
            $mode = \Imagine\Image\ImageInterface::THUMBNAIL_INSET;
            $tmpFullsize = $tmpPath.DS.microtime().$this->data['name'];
            $image->thumbnail($size, $mode)->save($tmpFullsize);
        } else {
            $tmpFullsize = $this->data['tmp_name'];
        }
        $thumbFilename = $this->generateThumbnailFilename($this->data['name']);

        // Create thumbnail
        $size = new \Imagine\Image\Box(200, 200);
        $mode = \Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND;
        $image->thumbnail($size, $mode)->save($tmpPath.DS.$thumbFilename);

        return [
            $tmpFullsize => $this->data['name'],
            $tmpPath.DS.$thumbFilename => $thumbFilename
        ];
    }

    /**
     * Returns a filename with ".thumb" inserted before the extension,
     * e.g. "picture.jpg" => "picture.thumb.jpg"
     *
     * @param string $fullsizeFilename
     * @return string
     */
    public static function generateThumbnailFilename($fullsizeFilename)
    {
        $thumbFilenameParts = explode('.', $fullsizeFilename);
        $extension = array_pop($thumbFilenameParts);
        $thumbFilenameParts[] = 'thumb';
        $thumbFilenameParts[] = $extension;
        return implode('.', $thumbFilenameParts);
    }
}
