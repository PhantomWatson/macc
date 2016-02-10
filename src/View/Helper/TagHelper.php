<?php
namespace App\View\Helper;

use Cake\View\Helper;

class TagHelper extends Helper
{
    public function availableTagsForJs($availableTags)
    {
        $arrayForJson = [];
        foreach ($availableTags as $tag) {
            $arrayForJson[] = [
                'id' => $tag['id'],
                'name' => $tag['name'],
                'selectable' => (boolean) $tag['selectable'],
                'children' => $this->availableTagsForJs($tag['children'])
            ];
        }
        return $arrayForJson;
    }

    public function selectedTagsForJs($selectedTags)
    {
        $arrayForJson = [];
        foreach ($selectedTags as $tag) {
            $arrayForJson[] = [
                'id' => $tag['id'],
                'name' => $tag['name']
            ];
        }
        return $arrayForJson;
    }
}
