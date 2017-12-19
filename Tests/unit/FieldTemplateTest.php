<?php

use Iliich246\YicmsCommon\Fields\FieldTemplate;
use Iliich246\YicmsCommon\Fields\Field;

/**
 * Class FieldTemplateTest
 */
class FieldTemplateTest extends \Codeception\Test\Unit
{
    use \Codeception\Specify;

    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    public function testCreateFieldTemplate()
    {
        $fieldTemplate = new FieldTemplate();

        $fieldTemplate->program_name = 'test_name1';
        $fieldTemplate->field_template_reference = 'test_reference111';
        $fieldTemplate->type = FieldTemplate::TYPE_INPUT;
        $fieldTemplate->language_type = FieldTemplate::LANGUAGE_TYPE_TRANSLATABLE;
        $fieldTemplate->editable = true;
        $fieldTemplate->visible = true;
        $fieldTemplate->is_main = false;

        $fieldTemplate->setScenario(FieldTemplate::SCENARIO_CREATE);

        $this->assertEquals(FieldTemplate::SCENARIO_CREATE, $fieldTemplate->scenario);
        $this->assertTrue($fieldTemplate->save());

        $this->tester->canSeeRecord(FieldTemplate::className(), [
            'program_name' => 'test_name1'
        ]);

        $this->assertFalse($fieldTemplate->save());

        $this->tester->canSeeRecord(FieldTemplate::className(), [
            'program_name' => 'test_name1'
        ]);

        $fieldTemplate = new FieldTemplate();

        $fieldTemplate->program_name = 'test_name2';
        $fieldTemplate->field_template_reference = 'test_reference111';
        $fieldTemplate->type = FieldTemplate::TYPE_INPUT;
        $fieldTemplate->language_type = FieldTemplate::LANGUAGE_TYPE_TRANSLATABLE;
        $fieldTemplate->editable = true;
        $fieldTemplate->visible = true;
        $fieldTemplate->is_main = false;

        $fieldTemplate->setScenario(FieldTemplate::SCENARIO_CREATE);

        $this->assertTrue($fieldTemplate->save());

        $this->tester->canSeeRecord(FieldTemplate::className(), [
            'program_name' => 'test_name2'
        ]);
    }

    /**
     * @depends testCreateFieldTemplate
     */
    public function testUpdateFieldTemplate()
    {
        /** @var FieldTemplate $fieldTemplate */
        $fieldTemplate = FieldTemplate::find()->where([
            'program_name' => 'test_name1'
        ])->one();

        $this->assertNotNull($fieldTemplate);
        $this->assertInstanceOf(FieldTemplate::className(), $fieldTemplate);

        $fieldTemplate->visible = false;
        $fieldTemplate->editable = false;

        $fieldTemplate->setScenario(FieldTemplate::SCENARIO_UPDATE);
        $this->assertTrue($fieldTemplate->save());

        $this->tester->canSeeRecord(FieldTemplate::className(), [
            'program_name' => 'test_name1',
            'visible' => false,
            'editable' => false,
        ]);

        /** @var FieldTemplate $fieldTemplate */
        $fieldTemplate = FieldTemplate::find()->where([
            'program_name' => 'test_name1'
        ])->one();

        $fieldTemplate->setScenario(FieldTemplate::SCENARIO_UPDATE);
        $fieldTemplate->program_name = 'test_name2';
        $this->assertFalse($fieldTemplate->save());

        $this->tester->canSeeRecord(FieldTemplate::className(), [
            'program_name' => 'test_name1',
            'visible' => false,
            'editable' => false,
        ]);

        $this->tester->canSeeRecord(FieldTemplate::className(), [
            'program_name' => 'test_name2',
        ]);
    }

    /**
     * @depends testCreateFieldTemplate
     */
    public function testDeleteFieldTemplate()
    {
        /** @var FieldTemplate $fieldTemplate */
        $fieldTemplate = FieldTemplate::find()->where([
            'program_name' => 'test_name2'
        ])->one();

        $this->assertNotNull($fieldTemplate);
        $this->assertInstanceOf(FieldTemplate::className(), $fieldTemplate);

        $this->assertEquals(1, $fieldTemplate->delete());

        $this->tester->cantSeeRecord(FieldTemplate::className(), [
            'program_name' => 'test_name2',
        ]);
    }

    public function testCreateWithConstraints()
    {
        $fieldTemplate = new FieldTemplate();

        $fieldTemplate->program_name = 'test_name_constraints1';
        $fieldTemplate->field_template_reference = 'test_reference_constraints';
        $fieldTemplate->type = FieldTemplate::TYPE_INPUT;
        $fieldTemplate->language_type = FieldTemplate::LANGUAGE_TYPE_TRANSLATABLE;
        $fieldTemplate->editable = true;
        $fieldTemplate->visible = true;
        $fieldTemplate->is_main = false;

        $fieldTemplate->setScenario(FieldTemplate::SCENARIO_CREATE);
        $this->assertEquals(FieldTemplate::SCENARIO_CREATE, $fieldTemplate->scenario);
        $this->assertTrue($fieldTemplate->save());

        $field = new Field();
        $field->common_fields_template_id = $fieldTemplate->id;
        $field->field_reference = 'xxx1';
        $field->value = 'test1';
        $field->editable = true;
        $field->visible = true;

        $this->assertTrue($field->save());

        $field = new Field();
        $field->common_fields_template_id = $fieldTemplate->id;
        $field->field_reference = 'xxx2';
        $field->value = 'test1';
        $field->editable = true;
        $field->visible = true;

        $this->assertTrue($field->save());

        $field = new Field();
        $field->common_fields_template_id = $fieldTemplate->id;
        $field->field_reference = 'xxx3';
        $field->value = 'test1';
        $field->editable = true;
        $field->visible = true;

        $this->assertTrue($field->save());
        $this->tester->canSeeRecord(FieldTemplate::className(), [
            'program_name' => 'test_name_constraints1',
        ]);

        $this->tester->canSeeRecord(Field::className(), [
            'common_fields_template_id' => $fieldTemplate->id,
            'field_reference' => 'xxx1',
        ]);

        $this->tester->canSeeRecord(Field::className(), [
            'common_fields_template_id' => $fieldTemplate->id,
            'field_reference' => 'xxx2',
        ]);

        $this->tester->canSeeRecord(Field::className(), [
            'common_fields_template_id' => $fieldTemplate->id,
            'field_reference' => 'xxx3',
        ]);

        $fieldTemplate = new FieldTemplate();

        $fieldTemplate->program_name = 'test_name_constraints2';
        $fieldTemplate->field_template_reference = 'test_reference_constraints2';
        $fieldTemplate->type = FieldTemplate::TYPE_INPUT;
        $fieldTemplate->language_type = FieldTemplate::LANGUAGE_TYPE_TRANSLATABLE;
        $fieldTemplate->editable = true;
        $fieldTemplate->visible = true;
        $fieldTemplate->is_main = false;

        $fieldTemplate->setScenario(FieldTemplate::SCENARIO_CREATE);
        $this->assertEquals(FieldTemplate::SCENARIO_CREATE, $fieldTemplate->scenario);
        $this->assertTrue($fieldTemplate->save());

        $field = new Field();
        $field->common_fields_template_id = $fieldTemplate->id;
        $field->field_reference = 'xxx1';
        $field->value = 'test1';
        $field->editable = true;
        $field->visible = true;

        $this->assertTrue($field->save());

        $field = new Field();
        $field->common_fields_template_id = $fieldTemplate->id;
        $field->field_reference = 'xxx2';
        $field->value = 'test1';
        $field->editable = true;
        $field->visible = true;

        $this->assertTrue($field->save());

        $this->tester->canSeeRecord(FieldTemplate::className(), [
            'program_name' => 'test_name_constraints1',
        ]);

        $this->tester->canSeeRecord(Field::className(), [
            'common_fields_template_id' => $fieldTemplate->id,
            'field_reference' => 'xxx1',
        ]);

        $this->tester->canSeeRecord(Field::className(), [
            'common_fields_template_id' => $fieldTemplate->id,
            'field_reference' => 'xxx2',
        ]);
    }

    /**
     * @depends testCreateWithConstraints
     */
    public function testDeleteWithConstraints()
    {
        /** @var FieldTemplate $fieldTemplate */
        $fieldTemplate = FieldTemplate::find()->where([
            'program_name' => 'test_name_constraints1'
        ])->one();

        $this->assertNotNull($fieldTemplate);
        $this->assertInstanceOf(FieldTemplate::className(), $fieldTemplate);

        $fieldTemplate->delete();

        $this->tester->cantSeeRecord(FieldTemplate::className(), [
            'program_name' => 'test_name_constraints1',
        ]);

        $this->tester->cantSeeRecord(Field::className(), [
            'common_fields_template_id' => $fieldTemplate->id,
            'field_reference' => 'xxx1',
        ]);

        $this->tester->cantSeeRecord(Field::className(), [
            'common_fields_template_id' => $fieldTemplate->id,
            'field_reference' => 'xxx2',
        ]);

        $this->tester->cantSeeRecord(Field::className(), [
            'common_fields_template_id' => $fieldTemplate->id,
            'field_reference' => 'xxx3',
        ]);
    }
}
