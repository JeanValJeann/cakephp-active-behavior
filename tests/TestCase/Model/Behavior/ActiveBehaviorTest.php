<?php
namespace Active\Test\TestCase\Model\Behavior;

use Cake\ORM\TableRegistry;
use Active\Model\Behavior\ActiveBehavior;
use Cake\TestSuite\TestCase;

class ActiveBehaviorTest extends TestCase
{

    public $fixtures = ['plugin.Active.Imgs'];

    public function setUp()
    {
        parent::setUp();
        
        $table = TableRegistry::get('Imgs');
        $table->addBehavior('Active.Active', []);

        $this->Table = $table;
        $this->Behavior = $table->behaviors()->Active;
    }

    public function tearDown()
    {
        parent::tearDown();
        TableRegistry::clear();
        unset($this->Behavior);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Field "fake_active_field" does not exist in table "imgs"
     */
    public function testInitializeWithoutActiveFieldInTable()
    {
        $this->Behavior = new ActiveBehavior($this->Table, ['active_field' => 'fake_active_field']);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Field "fake_field" does not exist in table "imgs"
     */
    public function testInitializeWithoutFieldInTable()
    {
        $this->Behavior = new ActiveBehavior($this->Table, ['group' => 'fake_field']);
    }

    public function testSaveWhenAuthorizeMultipleIsFalseAndOneEntityHavingActive()
    {
        $this->Table->removeBehavior('Active');
        $this->Table->addBehavior('Active.Active', [
            'group' => 'product_id',
            'multiple' => false
        ]);

        $data = [
            'product_id' => 1,
            'active' => 1
        ];
        $entity = $this->Table->newEntity($data);
        $res = $this->Table->save($entity);

        $entity = $this->Table->get(1);

        $this->assertTrue((bool)$res);
        $this->assertSame(1, $res->get('active'));
        $this->assertSame(0, $entity->get('active'));
    }

    public function testSaveWhenAlwaysOneActiveIsTrueAndPassTheOnlyOneEntityHAvingActiveTrueToFalse()
    {
        $this->Table->removeBehavior('Active');
        $this->Table->addBehavior('Active.Active', [
            'group' => 'product_id',
            'keep_active' => true,
        ]);

        $entity = $this->Table->get(1);
        $entity->set('active', 0);
        $res = $this->Table->save($entity);

        $this->assertSame(1, $res->get('active'));
    }

    public function testDeleteWhenAlwaysOneActiveIsTrueTheEntityHavingActive()
    {
        $this->Table->removeBehavior('Active');
        $this->Table->addBehavior('Active.Active', [
            'group' => 'product_id',
            'keep_active' => true,
        ]);

        $entity = $this->Table->get(1);
        $res = $this->Table->delete($entity);

        $entity = $this->Table->get(2);

        $this->assertTrue((bool)$res);
        $this->assertSame(1, $entity->get('active'));
    }

    public function testSaveWhenFieldValueChange()
    {
        $this->Table->removeBehavior('Active');
        $this->Table->addBehavior('Active.Active', [
            'group' => 'product_id',
        ]);
        $entity = $this->Table->get(1);
        $entity->set('product_id', 2);
        $entity->set('active', 1);
        $res = $this->Table->save($entity);

        $this->assertSame(1, $res->get('active'));

        $res2 = $this->Table->get(2);

        $this->assertSame(1, $res2->get('active'));
        
        $res3 = $this->Table->get(3);

        $this->assertSame(0, $res3->get('active'));
    }
}