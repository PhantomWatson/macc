<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Network\Exception\NotFoundException;

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
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $tags = $this->Tags->find('all')
            ->find('forMembers')
            ->select(['id', 'name', 'slug'])
            ->order(['Tags.name' => 'ASC']);

        $this->set([
            'pageTitle' => 'Art Tags',
            'tags' => $tags
        ]);
    }

    /**
     * View method
     *
     * @param string $slug
     * @return \Cake\Network\Response
     * @throws \Cake\Network\Exception\NotFoundException
     */
    public function view($slug = null)
    {
        $tag = $this->Tags->find('slug', [
            'slug' => $slug,
            'slugField' => 'Tags.slug'
        ])->contain([
            'Users' => function ($q) {
                return $q->find('members')->select(['id', 'name', 'slug']);
            }
        ]);

        if ($tag->isEmpty()) {
            throw new NotFoundException('Sorry, we couldn\'t find a "'.str_replace('-', ' ', $slug).'" tag');
        }

        $tag = $tag->first();

        $this->set([
            'pageTitle' => ucwords($tag->name),
            'tag' => $tag
        ]);
    }
}
