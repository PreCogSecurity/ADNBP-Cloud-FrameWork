<?php
/**
 * Tests for DataStore::createEntities() and related schema logic.
 *
 * Uses a FakeStore injected via reflection so that the real
 * ProtoBuf Gateway (which requires the App Engine SDK) is never called.
 */

use PHPUnit\Framework\TestCase;

/**
 * A lightweight stand-in for Store that avoids the App Engine SDK.
 */
class FakeStore
{
    /** @var array Entities that have been upserted */
    public $upsertedEntities = [];

    /**
     * Mimics Store::createEntity — builds a real Entity from a Schema.
     */
    public function createEntity($arr_data = null)
    {
        // Build a minimal Schema and use its createEntity
        $schema = new Schema('TestEntity');
        $obj_entity = $schema->createEntity();
        if (null !== $arr_data) {
            foreach ($arr_data as $str_property => $mix_value) {
                $obj_entity->__set($str_property, $mix_value);
            }
        }
        return $obj_entity;
    }

    /**
     * Records upserted entities without touching the SDK.
     */
    public function upsert($entities)
    {
        if ($entities instanceof Entity) {
            $entities = [$entities];
        }
        foreach ($entities as $e) {
            $this->upsertedEntities[] = $e;
        }
    }
}

class DataStoreTest extends TestCase
{
    /** @var Core */
    private $core;

    protected function setUp(): void
    {
        $this->core = new Core();
    }

    /**
     * Helper: build a DataStore and replace its Store with FakeStore.
     *
     * @param array|null $schema  Schema definition array
     * @param string|null $namespace
     * @return DataStore
     */
    private function buildDataStore(array $schema = null, string $namespace = null): DataStore
    {
        $params = ['TestEntity', $namespace, $schema];
        $ds = new DataStore($this->core, $params);

        // Inject FakeStore via reflection
        $ref = new ReflectionObject($ds);
        $prop = $ref->getProperty('store');
        $prop->setAccessible(true);
        $prop->setValue($ds, new FakeStore());

        return $ds;
    }

    // ------------------------------------------------------------------
    // createEntities — error-path tests
    // ------------------------------------------------------------------

    public function testCreateEntitiesReturnsEmptyArrayOnNonArrayInput(): void
    {
        $ds = $this->buildDataStore(['name' => ['string']]);
        $result = $ds->createEntities('not-an-array');

        $this->assertIsArray($result);
        $this->assertEmpty($result);
        $this->assertTrue($ds->error);
    }

    public function testCreateEntitiesReturnsFalseWhenErrorFlagAlreadySet(): void
    {
        $ds = $this->buildDataStore(['name' => ['string']]);

        // Set error via reflection
        $ref = new ReflectionObject($ds);
        $errProp = $ref->getProperty('error');
        $errProp->setAccessible(true);
        $errProp->setValue($ds, true);

        $result = $ds->createEntities([['name' => 'test']]);
        $this->assertFalse($result);
    }

    public function testCreateEntitiesReturnsEmptyArrayOnNullInput(): void
    {
        $ds = $this->buildDataStore(['name' => ['string']]);

        // null is not an array, so creates error
        $result = $ds->createEntities(null);
        $this->assertIsArray($result);
        $this->assertTrue($ds->error);
    }

    // ------------------------------------------------------------------
    // createEntities — success-path tests
    // ------------------------------------------------------------------

    public function testCreateEntitiesWithSingleRecord(): void
    {
        $ds = $this->buildDataStore(['name' => ['string'], 'age' => ['integer']]);
        $result = $ds->createEntities([['name' => 'Alice', 'age' => 30]]);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('Alice', $result[0]['name']);
        $this->assertEquals(30, $result[0]['age']);
        $this->assertEquals(1, count($ds->store->upsertedEntities));
    }

    public function testCreateEntitiesWithMultipleRecords(): void
    {
        $ds = $this->buildDataStore(['name' => ['string']]);
        $data = [
            ['name' => 'Alice'],
            ['name' => 'Bob'],
            ['name' => 'Charlie'],
        ];
        $result = $ds->createEntities($data);

        $this->assertCount(3, $result);
        $this->assertEquals('Alice', $result[0]['name']);
        $this->assertEquals('Bob', $result[1]['name']);
        $this->assertEquals('Charlie', $result[2]['name']);
        $this->assertCount(3, $ds->store->upsertedEntities);
    }

    public function testCreateEntitiesWrapsSingleRecordInArray(): void
    {
        $ds = $this->buildDataStore(['name' => ['string']]);

        // Pass a flat array (not array-of-arrays)
        $result = $ds->createEntities(['name' => 'Solo']);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('Solo', $result[0]['name']);
    }

    public function testCreateEntitiesWithKeyField(): void
    {
        $ds = $this->buildDataStore([
            'id' => ['key'],
            'name' => ['string'],
        ]);

        $result = $ds->createEntities([['id' => 'custom-key-123', 'name' => 'WithKey']]);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('WithKey', $result[0]['name']);
        // ID should be set from the key field
        $this->assertEquals('custom-key-123', $result[0]['ID']);
    }

    // ------------------------------------------------------------------
    // createEntities — date handling
    // ------------------------------------------------------------------

    public function testCreateEntitiesWithValidDateField(): void
    {
        $ds = $this->buildDataStore([
            'name' => ['string'],
            'created' => ['date'],
        ]);

        $result = $ds->createEntities([[
            'name' => 'Dated',
            'created' => '2024-01-15',
        ]]);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('Dated', $result[0]['name']);
        // Date should be a DateTime object or a date string
        $this->assertArrayHasKey('created', $result[0]);
    }

    public function testCreateEntitiesWithValidDatetimeField(): void
    {
        $ds = $this->buildDataStore([
            'name' => ['string'],
            'ts' => ['datetime'],
        ]);

        $result = $ds->createEntities([[
            'name' => 'Timestamped',
            'ts' => '2024-01-15 10:30:00',
        ]]);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('Timestamped', $result[0]['name']);
    }

    public function testCreateEntitiesWithInvalidDateReturnsError(): void
    {
        $ds = $this->buildDataStore([
            'name' => ['string'],
            'created' => ['date'],
        ]);

        $result = $ds->createEntities([[
            'name' => 'BadDate',
            'created' => 'not-a-date-at-all',
        ]]);

        $this->assertIsArray($result);
        // Should contain an error entry
        $this->assertNotEmpty($result);
        $this->assertArrayHasKey('error', $result[0]);
    }

    // ------------------------------------------------------------------
    // getEntitySchema tests
    // ------------------------------------------------------------------

    public function testGetEntitySchemaWithArrayReturnsSchemaObject(): void
    {
        $schema = ['name' => ['string'], 'age' => ['integer']];
        $ds = $this->buildDataStore($schema);

        $ref = new ReflectionObject($ds);
        $schemaProp = $ref->getProperty('schema');
        $schemaProp->setAccessible(true);
        $internalSchema = $schemaProp->getValue($ds);

        $this->assertArrayHasKey('props', $internalSchema);
        $this->assertArrayHasKey('data', $internalSchema);
        $this->assertCount(2, $internalSchema['props']);
    }

    public function testGetEntitySchemaWithoutArrayReturnsEmptyProps(): void
    {
        $ds = $this->buildDataStore(null);

        $ref = new ReflectionObject($ds);
        $schemaProp = $ref->getProperty('schema');
        $schemaProp->setAccessible(true);
        $internalSchema = $schemaProp->getValue($ds);

        $this->assertArrayHasKey('props', $internalSchema);
        $this->assertEmpty($internalSchema['props']);
    }

    public function testGetEntitySchemaParsesTypesCorrectly(): void
    {
        $schema = [
            'id'     => ['key'],
            'title'  => ['string'],
            'count'  => ['integer'],
            'price'  => ['float'],
            'active' => ['boolean'],
            'tags'   => ['list'],
            'geo'    => ['geo'],
            'when'   => ['datetime'],
            'unknown_type' => ['custom'],
        ];

        $ds = $this->buildDataStore($schema);

        $ref = new ReflectionObject($ds);
        $schemaProp = $ref->getProperty('schema');
        $schemaProp->setAccessible(true);
        $internalSchema = $schemaProp->getValue($ds);

        // 'key' type should NOT appear in props (skipped in switch case)
        $this->assertCount(8, $internalSchema['props']);

        // Verify the prop type assignments
        $this->assertEquals('string', $internalSchema['props'][0][1]);  // title
        $this->assertEquals('integer', $internalSchema['props'][1][1]); // count
        $this->assertEquals('float', $internalSchema['props'][2][1]);   // price
        $this->assertEquals('boolean', $internalSchema['props'][3][1]); // active
        $this->assertEquals('list', $internalSchema['props'][4][1]);    // tags
        $this->assertEquals('geo', $internalSchema['props'][5][1]);     // geo
        $this->assertEquals('datetime', $internalSchema['props'][6][1]);// when
        $this->assertEquals('string', $internalSchema['props'][7][1]);  // unknown_type (default)
    }

    // ------------------------------------------------------------------
    // Schema class tests
    // ------------------------------------------------------------------

    public function testSchemaGetKindReturnsKindName(): void
    {
        $schema = new Schema('MyKind');
        $this->assertEquals('MyKind', $schema->getKind());
    }

    public function testSchemaAddPropertyReturnsSelf(): void
    {
        $schema = new Schema('TestKind');
        $result = $schema->addString('field1', true);
        $this->assertSame($schema, $result);
    }

    public function testSchemaAddPropertyTracksDefinedFields(): void
    {
        $schema = new Schema('TestKind');
        $schema->addString('name', true);
        $schema->addInteger('age', false);

        $props = $schema->getProperties();
        $this->assertCount(2, $props);
        $this->assertEquals(Schema::PROPERTY_STRING, $props['name']['type']);
        $this->assertTrue($props['name']['index']);
        $this->assertEquals(Schema::PROPERTY_INTEGER, $props['age']['type']);
        $this->assertFalse($props['age']['index']);
    }

    public function testSchemaCreateEntityReturnsEntityInstance(): void
    {
        $schema = new Schema('TestKind');
        $entity = $schema->createEntity();
        $this->assertInstanceOf(Entity::class, $entity);
    }

    // ------------------------------------------------------------------
    // Entity class tests
    // ------------------------------------------------------------------

    public function testEntitySetAndGetKeyId(): void
    {
        $entity = new Entity();
        $entity->setKeyId(42);
        $this->assertEquals(42, $entity->getKeyId());
    }

    public function testEntitySetAndGetKeyName(): void
    {
        $entity = new Entity();
        $entity->setKeyName('my-record');
        $this->assertEquals('my-record', $entity->getKeyName());
    }

    public function testEntityMagicSetAndGet(): void
    {
        $entity = new Entity();
        $entity->name = 'Test';
        $entity->count = 99;

        $this->assertEquals('Test', $entity->name);
        $this->assertEquals(99, $entity->count);
    }

    public function testEntityGetData(): void
    {
        $entity = new Entity();
        $entity->foo = 'bar';
        $entity->num = 42;

        $data = $entity->getData();
        $this->assertEquals(['foo' => 'bar', 'num' => 42], $data);
    }

    public function testEntitySetAndGetKind(): void
    {
        $entity = new Entity();
        $entity->setKind('Person');
        $this->assertEquals('Person', $entity->getKind());
    }

    public function testEntityMagicGetReturnsNullForMissingKey(): void
    {
        $entity = new Entity();
        $this->assertNull($entity->nonexistent);
    }
}
