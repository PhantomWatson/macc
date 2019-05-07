<?php
namespace App\Model\Table;

use App\Model\Entity\Tag;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Tags Model
 *
 * @property \Cake\ORM\Association\BelongsTo $ParentTags
 * @property \Cake\ORM\Association\HasMany $ChildTags
 * @property \Cake\ORM\Association\BelongsToMany $Users
 */
class TagsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('tags');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Tree');
        $this->addBehavior('Xety/Cake3Sluggable.Sluggable', [
            'field' => 'name'
        ]);

        $this->belongsTo('ParentTags', [
            'className' => 'Tags',
            'foreignKey' => 'parent_id'
        ]);
        $this->hasMany('ChildTags', [
            'className' => 'Tags',
            'foreignKey' => 'parent_id'
        ]);
        $this->belongsToMany('Users', [
            'foreignKey' => 'tag_id',
            'targetForeignKey' => 'user_id',
            'joinTable' => 'tags_users'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric']);

        $validator
            ->add('lft', 'valid', ['rule' => 'numeric']);

        $validator
            ->add('rght', 'valid', ['rule' => 'numeric']);

        $validator
            ->allowEmptyString('name');

        $validator
            ->add('listed', 'valid', ['rule' => 'boolean'])
            ->requirePresence('listed', 'create');

        $validator
            ->add('selectable', 'valid', ['rule' => 'boolean'])
            ->requirePresence('selectable', 'create');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['parent_id'], 'ParentTags'));
        return $rules;
    }

    public function getThreaded()
    {
        $results = $this->find('threaded')
            ->select(['id', 'name', 'parent_id', 'selectable'])
            ->where(['listed' => 1])
            ->order(['name' => 'ASC'])
            ->toArray();
        return $this->sortThreaded($results);
    }

    /**
     * Takes the result of find('threaded') and sorts so that branches (with children)
     * come before leaves; Assumes that everything is already alphabetized
     *
     * @param array $threaded
     * @return array
     */
    public function sortThreaded($threaded)
    {
        $branches = [];
        $leaves = [];
        foreach ($threaded as $item) {
            if (empty($item['children'])) {
                $leaves[] = $item;
            } else {
                $item['children'] = $this->sortThreaded($item['children']);
                $branches[] = $item;
            }
        }
        return array_merge($branches, $leaves);
    }

    /**
     * Finds all tags without slugs and gives them slugs
     */
    public function addSlugs()
    {
        /** @var Tag[] $tags */
        $tags = $this->find('all')->select(['id', 'name'])->where(['slug' => '']);
        foreach ($tags as $tag) {
            $tag->setDirty('name', true);
            $this->save($tag);
        }
    }

    public function findForMembers(Query $query, array $options)
    {
        return $query->matching('Users.Memberships', function ($q) {
            /** @var Query $q */

            return $q->where(['Memberships.expires >=' => date('Y-m-d H:i:s')])->where([
                function ($exp, $q) {
                    /** @var QueryExpression $exp */

                    return $exp->isNull('canceled');
                }
            ]);
        })->distinct(['Tags.id']);
    }
}
