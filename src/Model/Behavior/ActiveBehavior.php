<?php
namespace Active\Model\Behavior;

use ArrayObject;
use Cake\Event\Event;
use RuntimeException;
use Cake\ORM\Behavior;

class ActiveBehavior extends Behavior
{
    /**
     * Table using Behavior
     * @var Cake\ORM\Table
     */
    protected $_table;

    /**
     * Entity being managed
     * @var Cake\ORM\Entity
     */
    protected $_entity;

    /**
     * Configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'active_field' => 'active', // Field table to write active value
        'group' => '', // Field table to group the behavior on
        'keep_active' => true, // Always keep one record active
        'multiple' => false // Authorize multiple record to be active
    ];

    /**
     * Initialize configuration.
     *
     * @param array $config Configuration array.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->_table = $this->getTable();

        $config = $this->_config;

        $this->active_field = $config['active_field'];
        $this->group = $config['group'];
        $this->keep_active = $config['keep_active'];
        $this->multiple = $config['multiple'];

        if (!$this->_table->hasField($this->active_field)) {
            throw new RuntimeException(sprintf(
                'Field "%s" does not exist in table "%s"',
                $this->active_field,
                $this->_table->getTable()
            ));
        }
        if ($this->group) {
            if (!$this->_table->hasField($this->group)) {
                throw new RuntimeException(sprintf(
                    'Field "%s" does not exist in table "%s"',
                    $this->group,
                    $this->_table->getTable()
                ));
            }
        }
    }

    /**
     * Find entities according to group config value
     * @return Cake\ORM\Query
     */
    protected function _find()
    {
        $query = $this->_table->find();
        if ($this->group) $query->where([$this->group => $this->_entity->get($this->group)]);
        return $query;
    }

    /**
     * Get all active entities according to grop config value exepting managed entity
     * @return array of Cake\ORM\Entity
     */
    protected function _getActiveEntities($id = null)
    {
        $query = $this->_find();
        if ($id) $query->where(['id !=' => $this->_entity->id]);
        return $query
            ->where([$this->active_field => 1])
            ->toArray();
    }

    /**
     * Modifies the entity before it is saved in the database.
     *
     * @param \Cake\Event\Event $event The beforeSave event that was fired
     * @param $entity The entity that is going to be saved
     * @param \ArrayObject $options the options passed to the save method
     * @return void
     */
    public function beforeSave(Event $event, $entity, ArrayObject $options)
    {
        $this->_entity = $entity;
        if (
            $this->keep_active &&
            count($this->_getActiveEntities($this->_entity->id)) === 0
        ) {
            $entity->set($this->active_field, 1);
        }
    }

    /**
     * Modifies the entity after it is saved in the database.
     *
     * @param \Cake\Event\Event $event The beforeSave event that was fired
     * @param $entity The entity that is going to be saved
     * @param \ArrayObject $options the options passed to the save method
     * @return void
     */
    public function afterSave(Event $event, $entity, ArrayObject $options)
    {
        if (!$this->multiple) {
            if ($entity->get($this->active_field) === 1) {
                foreach ($this->_getActiveEntities($this->_entity->id) as $entity_to_set_inactive) {
                    $entity_to_set_inactive->set($this->active_field, 0);
                    $this->_table->save($entity_to_set_inactive);
                }
            }
        }
        if ($this->group) {
            if (!empty($entity->extractOriginalChanged([$this->group]))) {
                if ($this->keep_active) {
                    $this->_entity = $this->_table
                        ->find()
                        ->where([$this->group => $entity->extractOriginalChanged([$this->group])[$this->group]])
                        ->first();
                    $former_entities = $this->_find()->toArray();
                    $former_active_entities = $this->_getActiveEntities();
                    if (
                        count($former_entities) > 0 &&
                        count($former_active_entities) === 0
                    ) {
                        $entity = $former_entities[0]->set('active', 1);
                        $this->_table->save($entity);
                    }
                }
            }
        }
    }

    /**
     * Modifies the entity after it is deleted in the database.
     *
     * @param \Cake\Event\Event $event The beforeSave event that was fired
     * @param $entity The entity that is going to be saved
     * @param \ArrayObject $options the options passed to the save method
     * @return void
     */
    public function afterDelete(Event $event, $entity, ArrayObject $options)
    {
        $this->_entity = $entity;
        if ($this->keep_active) {
            if (empty($this->_getActiveEntities())) {
                $entity = $this->_find()->first();
                if (!empty($entity)) {
                    $entity->set($this->active_field, 1);
                    $this->_table->save($entity);
                }
            }
        }
    }
}
