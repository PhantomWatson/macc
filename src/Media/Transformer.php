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

        // Shrink image to max dimensions
        $maxDimension = 2000;
        $width = $image->getSize()->getWidth();
        $height = $image->getSize()->getHeight();
        if ($width > $maxDimension || $height > $maxDimension) {
            $size = new \Imagine\Image\Box(2000, 2000);
            $mode = \Imagine\Image\ImageInterface::THUMBNAIL_INSET;
            $image->thumbnail($size, $mode)->save($this->data['tmp_name']);
        }

        // Generate thumbnail filename
        $thumbFilenameParts = explode('.', $this->data['name']);
        $extension = array_pop($thumbFilenameParts);
        $thumbFilenameParts[] = 'thumb';
        $thumbFilenameParts[] = $extension;
        $thumbFilename = implode('.', $thumbFilenameParts);

        // Create thumbnail
        $size = new \Imagine\Image\Box(200, 200);
        $mode = \Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND;
        $tmpPathParts = explode(DS, $this->data['tmp_name']);
        array_pop($tmpPathParts); // remove filename from path
        $tmpPath = implode(DS, $tmpPathParts);
        $image->thumbnail($size, $mode)->save($tmpPath.DS.$thumbFilename);

        return [
            $this->data['tmp_name'] => $this->data['name'],
            $tmpPath.DS.$thumbFilename => $thumbFilename
        ];
    }
}
