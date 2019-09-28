<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Picture Entity.
 *
 * @property int $id
 * @property int $user_id
 * @property \App\Model\Entity\User $user
 * @property bool $is_primary
 * @property string $filename
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 */
class Picture extends Entity
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
        '*' => true,
        'id' => false,
    ];

    protected function _getThumbnailFilename()
    {
        $filenameParts = explode('.', $this->_properties['filename']);
        $extension = array_pop($filenameParts);
        $filenameParts[] = 'thumb';
        $filenameParts[] = $extension;
        return implode('.', $filenameParts);
    }

    /**
     * Returns a string to explain why an image failed to upload that includes an HTML list of error messages
     *
     * @return string|null
     */
    public function getUploadErrorList()
    {
        $errors = $this->getErrors();

        if (empty($errors)) {
            return null;
        }

        $exceptionMsg = 'There was an error uploading that picture. Please try again.';
        $exceptionMsg .= '<ul>';
        foreach ($errors as $field => $fieldErrors) {
            foreach ($fieldErrors as $label => $message) {
                $exceptionMsg .= "<li>$message</li>";
            }
        }
        $exceptionMsg .= '</ul>';

        return $exceptionMsg;
    }
}
