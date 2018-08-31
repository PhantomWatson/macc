<?php
namespace App\Controller;

use App\Model\Entity\Tag;
use Cake\Database\Expression\QueryExpression;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\Query;
use Cake\Utility\Hash;

/**
 * Tags Controller
 *
 * @property \App\Model\Table\TagsTable $Tags
 */
class TagsController extends AppController
{

    public function initialize()
    {
        parent::initialize();
        $this->Auth->allow([
            'index',
            'view'
        ]);
    }

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        // Get all tag IDs associated with members
        $tags = $this->Tags->find('all')
            ->find('forMembers')
            ->select(['id', 'parent_id'])
            ->toArray();
        $memberTagIds = Hash::extract($tags, '{n}.id');

        // Get all of those tags' parent IDs
        $tagParentIds = Hash::extract($tags, '{n}.parent_id');

        // Collect all parent tags that lead from member tags to the tag tree root
        $tagIds = $memberTagIds;
        while (!empty($tagParentIds)) {
            // Search for unrecognized parents
            $parentsToFind = [];
            foreach ($tagParentIds as $tagId) {
                if (!in_array($tagId, $tagIds)) {
                    $parentsToFind[] = $tagId;
                }
            }
            if (empty($parentsToFind)) {
                break;
            }

            // Add these parent tag IDs to the full list
            $additionalTags = $this->Tags->find('all')
                ->where([
                    function ($exp, $q) use ($parentsToFind) {
                        /** @var QueryExpression $exp */

                        return $exp->in('id', $parentsToFind);
                    }
                ])
                ->select(['id', 'parent_id'])
                ->order(['Tags.name' => 'ASC'])
                ->toArray();
            $tagIds = array_merge(Hash::extract($additionalTags, '{n}.id'), $tagIds);

            // Set up next round of searching for parents
            $tagParentIds = Hash::extract($additionalTags, '{n}.parent_id');
        }

        $tags = $this->Tags->find('all')
            ->find('threaded')
            ->where([
                function ($exp, $q) use ($tagIds) {
                    /** @var QueryExpression $exp */

                    return $exp->in('id', $tagIds);
                }
            ])
            ->select(['id', 'name', 'slug', 'parent_id'])
            ->order(['Tags.name' => 'ASC'])
            ->all();

        $this->set([
            'pageTitle' => 'Art Tags',
            'tags' => $tags,
            'memberTagIds' => $memberTagIds
        ]);
    }

    /**
     * View method
     *
     * @param string $slug
     * @return void
     * @throws \Cake\Http\Exception\NotFoundException
     */
    public function view($slug = null)
    {
        $tag = $this->Tags->find('slug', [
            'slug' => $slug,
            'slugField' => 'Tags.slug'
        ])->contain([
            'Users' => function ($q) {
                /** @var Query $q */

                return $q->find('members')->select(['id', 'name', 'slug']);
            }
        ]);

        if ($tag->isEmpty()) {
            throw new NotFoundException('Sorry, we couldn\'t find a "'.str_replace('-', ' ', $slug).'" tag');
        }

        $tag = $tag->first();

        /** @var Tag $tag */
        $this->set([
            'pageTitle' => ucwords($tag->name),
            'tag' => $tag
        ]);
    }
}
