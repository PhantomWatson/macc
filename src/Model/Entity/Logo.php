<?php
namespace App\Model\Entity;

use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\I18n\FrozenTime;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

/**
 * Logo Entity
 *
 * @property FrozenTime $created
 * @property FrozenTime $modified
 * @property int $id
 * @property int $user_id
 * @property string $filename
 * @property string $filepath
 * @property User $user
 */
class Logo extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'user_id' => true,
        'filename' => true,
        'created' => true,
        'modified' => true,
        'user' => true
    ];

    /**
     * Deletes the files and records associated with any logos for the specified user other than the current logo
     *
     * @return void
     */
    public function deleteOtherLogos()
    {
        // Delete any previous logo files
        $dir = new Folder(WWW_ROOT . 'img' . DS . 'logos' . DS . $this->user_id);
        $files = $dir->find();
        foreach ($files as $file) {
            if ($file != $this->filename) {
                (new File($dir->pwd() . DS . $file))->delete();
            }
        }

        // Delete any previous records
        $logosTable = TableRegistry::getTableLocator()->get('Logos');
        $logosTable->deleteAll([
            'user_id' => $this->user_id,
            'id !=' => $this->id
        ]);
    }

    /**
     * Returns the path from webroot to the current logo
     *
     * @return string
     */
    protected function _getFilepath()
    {
        return sprintf(
            '/img/logos/%s/%s',
            $this->user_id,
            $this->filename
        );
    }
}
