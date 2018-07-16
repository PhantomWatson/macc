<?php
namespace App\Media;

use App\Model\Entity\Picture;
use Cake\ORM\Entity;
use Cake\ORM\Table;
use Cake\Utility\Hash;
use Josegonzalez\Upload\File\Path\ProcessorInterface;
use LogicException;

class PathProcessor implements ProcessorInterface
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
     * @var Picture
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
     * Returns the basepath for the current field/data combination.
     * If a `path` is specified in settings, then that will be used as
     * the replacement pattern
     *
     * @return string
     * @throws LogicException if a replacement is not valid for the current dataset
     */
    public function basepath()
    {
        $defaultPath = 'webroot{DS}files{DS}{model}{DS}{field}{DS}';
        $path = Hash::get($this->settings, 'path', $defaultPath);
        if (strpos($path, '{primaryKey}') !== false) {
            if ($this->entity->isNew()) {
                throw new LogicException('{primaryKey} substitution not allowed for new entities');
            }
            if (is_array($this->table->getPrimaryKey())) {
                throw new LogicException('{primaryKey} substitution not valid for composite primary keys');
            }
        }

        $replacements = [
            '{primaryKey}' => $this->entity->get($this->table->getPrimaryKey()),
            '{model}' => $this->table->getAlias(),
            '{table}' => $this->table->getTable(),
            '{field}' => $this->field,
            '{time}' => time(),
            '{microtime}' => microtime(),
            '{DS}' => DIRECTORY_SEPARATOR,
            '{user_id}' => $this->entity->user_id
        ];
        return str_replace(
            array_keys($replacements),
            array_values($replacements),
            $path
        );
    }

    public function filename()
    {
        $filenameParts = explode('.', $this->data['name']);
        $extension = end($filenameParts);
        $extension = strtolower($extension);
        return time().'.'.$extension;
    }
}
