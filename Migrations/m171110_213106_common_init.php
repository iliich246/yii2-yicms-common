<?php

use yii\db\Migration;

/**
 * Class m171110_213106_common_init
 *
 * ALTER DATABASE <database_name> CHARACTER SET utf8 COLLATE utf8_unicode_ci;
 */
class m171110_213106_common_init extends Migration
{
    /**
 * @inheritdoc
 */
    public function safeUp()
    {
        //////////////////////////////////////////////////////////////////
        // Common functionality
        //////////////////////////////////////////////////////////////////
        /**
         * common_languages table
         */
        $this->createTable('{{%common_languages}}', [
            'id'   => $this->primaryKey(),
            'code' => $this->string(5)->notNull(),
            'name' => $this->string()->notNull(),
            'used' => $this->boolean()->notNull(),
        ]);

        $this->insert('{{%common_languages}}', [
            'id'   => 1,
            'code' => 'en-EU',
            'name' => 'English',
            'used' => true,
        ]);

        $this->insert('{{%common_languages}}', [
            'id'   => 2,
            'code' => 'ru-RU',
            'name' => 'Русский',
            'used' => false,
        ]);

        /**
         * common_config table
         */
        $this->createTable('{{%common_config}}', [
            'id'              => $this->primaryKey(),
            'defaultLanguage' => $this->string(),
            'languageMethod'  => $this->string(),
        ]);

        $this->insert('{{%common_config}}', [
            'id'              => 1,
            'defaultLanguage' => 'en-EU',
            'languageMethod'  => 1,
        ]);

        /**
         * common_validators table
         */
        $this->createTable('{{%common_validators}}', [
            'id'                  => $this->primaryKey(),
            'validator_reference' => $this->string(),
            'validator'           => $this->string(),
            'is_active'           => $this->boolean(),
            'params'              => $this->text(),
        ]);

        //////////////////////////////////////////////////////////////////
        // Fields functionality
        //////////////////////////////////////////////////////////////////
        /**
         * common_fields_templates table
         */
        $this->createTable('{{%common_fields_templates}}', [
            'id'                       => $this->primaryKey(),
            'field_template_reference' => $this->string(),
            'validator_reference'      => $this->string(),
            'program_name'             => $this->string(50),
            'type'                     => $this->smallInteger(),
            'language_type'            => $this->smallInteger(),
            'field_order'              => $this->integer(),
            'editable'                 => $this->boolean(),
            'visible'                  => $this->boolean(),
        ]);

        $this->createIndex(
            'field_template_reference-index',
            '{{%common_fields_templates}}',
            'field_template_reference'
        );

        /**
         * common_fields_represents table
         */
        $this->createTable('{{%common_fields_represents}}', [
            'id'                        => $this->primaryKey(),
            'common_fields_template_id' => $this->integer(),
            'field_reference'           => $this->string(),
            'value'                     => $this->text()->defaultValue(null),
            'editable'                  => $this->boolean(),
            'visible'                   => $this->boolean(),
        ]);

        $this->createIndex(
            'field_reference-index',
            '{{%common_fields_represents}}',
            'field_reference'
        );

        $this->addForeignKey('common_fields_represents-to-common_fields_templates',
            '{{%common_fields_represents}}',
            'common_fields_template_id',
            '{{%common_fields_templates}}',
            'id'
        );

        /**
         * common_field_translates table
         */
        $this->createTable('{{%common_field_translates}}', [
            'id'                         => $this->primaryKey(),
            'common_fields_represent_id' => $this->integer(),
            'common_language_id'         => $this->integer(),
            'value'                      => $this->text(),
        ]);

        $this->addForeignKey('common_field_translates-to-common_fields_represents',
            '{{%common_field_translates}}',
            'common_fields_represent_id',
            '{{%common_fields_represents}}',
            'id'
        );

        $this->addForeignKey('common_field_translates-to-common_languages',
            '{{%common_field_translates}}',
            'common_language_id',
            '{{%common_languages}}',
            'id'
        );

        /**
         * common_field_names table
         */
        $this->createTable('{{%common_field_names}}', [
            'id'                        => $this->primaryKey(),
            'common_fields_template_id' => $this->integer(),
            'common_language_id'        => $this->integer(),
            'name'                      => $this->string(),
            'description'               => $this->string(),
        ]);

        $this->addForeignKey('common_field_names-to-common_fields_templates',
            '{{%common_field_names}}',
            'common_fields_template_id',
            '{{%common_fields_templates}}',
            'id'
        );

        $this->addForeignKey('common_field_names-to-common_languages',
            '{{%common_field_names}}',
            'common_language_id',
            '{{%common_languages}}',
            'id'
        );

        //////////////////////////////////////////////////////////////////
        // Files functionality
        //////////////////////////////////////////////////////////////////
        /**
         * common_files_templates table
         */
        $this->createTable('{{%common_files_templates}}', [
            'id'                           => $this->primaryKey(),
            'file_template_reference'      => $this->string(),
            'field_template_reference'     => $this->string(),
            'condition_template_reference' => $this->string(),
            'validator_reference'          => $this->string(),
            'program_name'                 => $this->string(50),
            'type'                         => $this->smallInteger(),
            'file_order'                   => $this->integer(),
            'language_type'                => $this->smallInteger(),
            'editable'                     => $this->boolean(),
            'visible'                      => $this->boolean(),
            'max_files'                    => $this->integer(),
        ]);

        $this->createIndex(
            'file_template_reference-index',
            '{{%common_files_templates}}',
            'file_template_reference'
        );

        /**
         * common_files table
         */
        $this->createTable('{{%common_files}}', [
            'id'                       => $this->primaryKey(),
            'common_files_template_id' => $this->integer(),
            'file_reference'           => $this->string(),
            'field_reference'          => $this->string(),
            'condition_reference'      => $this->string(),
            'system_name'              => $this->string(),
            'original_name'            => $this->string(),
            'file_order'               => $this->integer(),
            'size'                     => $this->integer(),
            'type'                     => $this->string(),
            'editable'                 => $this->boolean(),
            'visible'                  => $this->boolean(),
            'created_at'               => $this->integer(),
            'updated_at'               => $this->integer(),
        ]);

        $this->createIndex(
            'file_reference-index',
            '{{%common_files}}',
            'file_reference'
        );

        $this->addForeignKey('common_files-to-common_files_templates',
            '{{%common_files}}',
            'common_files_template_id',
            '{{%common_files_templates}}',
            'id'
        );

        /**
         * common_file_translates table
         */
        $this->createTable('{{%common_file_translates}}', [
            'id'                 => $this->primaryKey(),
            'common_file_id'     => $this->integer(),
            'common_language_id' => $this->integer(),
            'system_name'        => $this->string(),
            'original_name'      => $this->string(),
            'filename'           => $this->string(),
            'size'               => $this->integer(),
            'type'               => $this->string(),
            'editable'           => $this->boolean(),
            'visible'            => $this->boolean(),
        ]);

        $this->addForeignKey('common_file_translates-to-common_files',
            '{{%common_file_translates}}',
            'common_file_id',
            '{{%common_files}}',
            'id'
        );

        $this->addForeignKey('common_file_translates-to-common_languages',
            '{{%common_file_translates}}',
            'common_language_id',
            '{{%common_languages}}',
            'id'
        );

        /**
         * common_file_names table
         */
        $this->createTable('{{%common_file_names}}', [
            'id'                       => $this->primaryKey(),
            'common_files_template_id' => $this->integer(),
            'common_language_id'       => $this->integer(),
            'name'                     => $this->string(),
            'description'              => $this->text(),
        ]);

        $this->addForeignKey('common_file_names-to-common_files_templates',
            '{{%common_file_names}}',
            'common_files_template_id',
            '{{%common_files_templates}}',
            'id'
        );

        $this->addForeignKey('common_file_names-to-common_languages',
            '{{%common_file_names}}',
            'common_language_id',
            '{{%common_languages}}',
            'id'
        );

        //////////////////////////////////////////////////////////////////
        // Images functionality
        //////////////////////////////////////////////////////////////////
        /**
         * common_images_templates table
         */
        $this->createTable('{{%common_images_templates}}', [
            'id'                           => $this->primaryKey(),
            'image_template_reference'     => $this->string(),
            'field_template_reference'     => $this->string(),
            'condition_template_reference' => $this->string(),
            'validator_reference'          => $this->string(),
            'program_name'                 => $this->string(50),
            'type'                         => $this->smallInteger(),
            'image_order'                  => $this->integer(),
            'language_type'                => $this->smallInteger(),
            'visible'                      => $this->boolean(),
            'editable'                     => $this->boolean(),
            'max_images'                   => $this->smallInteger(),
            'fill_color'                   => $this->string(),
            'crop_type'                    => $this->smallInteger(),
            'crop_height'                  => $this->integer(),
            'crop_width'                   => $this->integer(),
        ]);

        $this->createIndex(
            'image_template_reference-index',
            '{{%common_images_templates}}',
            'image_template_reference'
        );

        /**
         * common_images table
         */
        $this->createTable('{{%common_images}}', [
            'id'                         => $this->primaryKey(),
            'common_images_templates_id' => $this->integer(),
            'image_reference'            => $this->string(),
            'field_reference'            => $this->string(),
            'condition_reference'        => $this->string(),
            'system_name'                => $this->string(),
            'original_name'              => $this->string(),
            'image_order'                => $this->integer(),
            'size'                       => $this->integer(),
            'type'                       => $this->string(),
            'editable'                   => $this->boolean(),
            'visible'                    => $this->boolean(),
            'created_at'                 => $this->integer(),
            'updated_at'                 => $this->integer(),
        ]);

        $this->createIndex(
            'image_reference-index',
            '{{%common_images}}',
            'image_reference'
        );

        $this->addForeignKey('common_images-to-common_images_templates',
            '{{%common_images}}',
            'common_images_templates_id',
            '{{%common_images_templates}}',
            'id'
        );

        /**
         * common_image_translates table
         */
        $this->createTable('{{%common_image_translates}}', [
            'id'                 => $this->primaryKey(),
            'common_image_id'    => $this->integer(),
            'common_language_id' => $this->integer(),
            'system_name'        => $this->string(),
            'original_name'      => $this->string(),
            'size'               => $this->integer(),
            'type'               => $this->string(),
            'editable'           => $this->boolean(),
            'visible'            => $this->boolean(),
        ]);

        $this->addForeignKey('common_image_translates-to-common_images',
            '{{%common_image_translates}}',
            'common_image_id',
            '{{%common_images}}',
            'id'
        );

        $this->addForeignKey('common_image_translates-to-common_languages',
            '{{%common_image_translates}}',
            'common_language_id',
            '{{%common_languages}}',
            'id'
        );

        /**
         * common_image_names table
         */
        $this->createTable('{{%common_image_names}}', [
            'id'                        => $this->primaryKey(),
            'common_images_template_id' => $this->integer(),
            'common_language_id'        => $this->integer(),
            'name'                      => $this->string(),
            'description'               => $this->text(),
        ]);

        $this->addForeignKey('common_image_names-to-common_images_templates',
            '{{%common_image_names}}',
            'common_images_template_id',
            '{{%common_images_templates}}',
            'id'
        );

        $this->addForeignKey('common_image_names-to-common_languages',
            '{{%common_image_names}}',
            'common_language_id',
            '{{%common_languages}}',
            'id'
        );

        /**
         * common_images_thumbnails table
         */
        $this->createTable('{{%common_images_thumbnails}}', [
            'id'                         => $this->primaryKey(),
            'common_images_templates_id' => $this->integer(),
            'program_name'               => $this->string(50),
            'divider'                    => $this->smallInteger(),
            'quality'                    => $this->smallInteger(),
        ]);

        $this->addForeignKey('common_images_thumbnails-to-common_images_templates',
            '{{%common_images_thumbnails}}',
            'common_images_templates_id',
            '{{%common_images_templates}}',
            'id'
        );

        //////////////////////////////////////////////////////////////////
        // Conditions functionality
        //////////////////////////////////////////////////////////////////
        /**
         * common_conditions_templates table
         */
        $this->createTable('{{%common_conditions_templates}}', [
            'id'                           => $this->primaryKey(),
            'condition_template_reference' => $this->string(),
            'program_name'                 => $this->string(50),
            'type'                         => $this->smallInteger(),
            'condition_order'              => $this->integer(),
            'editable'                     => $this->boolean(),
        ]);

        $this->createIndex(
            'condition_template_reference-index',
            '{{%common_conditions_templates}}',
            'condition_template_reference'
        );

        /**
         * common_conditions table
         */
        $this->createTable('{{%common_conditions}}', [
            'id'                           => $this->primaryKey(),
            'common_condition_template_id' => $this->integer(),
            'condition_reference'          => $this->string(),
            'common_value_id'              => $this->integer(),
            'editable'                     => $this->boolean(),
        ]);

        $this->createIndex(
            'condition_reference-index',
            '{{%common_conditions}}',
            'condition_reference'
        );

        $this->addForeignKey('common_conditions-to-common_conditions_templates',
            '{{%common_conditions}}',
            'common_condition_template_id',
            '{{%common_conditions_templates}}',
            'id'
        );

        /**
         * common_conditions_values table
         */
        $this->createTable('{{%common_conditions_values}}', [
            'id'                           => $this->primaryKey(),
            'common_condition_template_id' => $this->integer(),
            'value_name'                   => $this->string(),
            'condition_value_order'        => $this->integer(),
            'is_default'                   => $this->boolean(),
        ]);

        $this->addForeignKey('common_conditions_values-to-common_conditions_templates',
            '{{%common_conditions_values}}',
            'common_condition_template_id',
            '{{%common_conditions_templates}}',
            'id'
        );

        $this->addForeignKey('common_conditions-to-common_conditions_values',
            '{{%common_conditions}}',
            'common_value_id',
            '{{%common_conditions_values}}',
            'id'
        );

        /**
         * common_conditions_value_names table
         */
        $this->createTable('{{%common_conditions_value_names}}', [
            'id'                        => $this->primaryKey(),
            'common_condition_value_id' => $this->integer(),
            'common_language_id'        => $this->integer(),
            'name'                      => $this->string(),
            'description'               => $this->string(),
        ]);

        $this->addForeignKey('common_conditions_value_names-to-common_conditions_values',
            '{{%common_conditions_value_names}}',
            'common_condition_value_id',
            '{{%common_conditions_values}}',
            'id'
        );

        $this->addForeignKey('common_conditions_value_names-to-common_languages',
            '{{%common_conditions_value_names}}',
            'common_language_id',
            '{{%common_languages}}',
            'id'
        );

        /**
         * common_conditions_names table
         */
        $this->createTable('{{%common_conditions_names}}', [
            'id'                           => $this->primaryKey(),
            'common_condition_template_id' => $this->integer(),
            'common_language_id'           => $this->integer(),
            'name'                         => $this->string(),
            'description'                  => $this->string(),
        ]);

        $this->addForeignKey('common_conditions_names-to-common_conditions_templates',
            '{{%common_conditions_names}}',
            'common_condition_template_id',
            '{{%common_conditions_templates}}',
            'id'
        );

        $this->addForeignKey('common_conditions_names-to-common_languages',
            '{{%common_conditions_names}}',
            'common_language_id',
            '{{%common_languages}}',
            'id'
        );

        //////////////////////////////////////////////////////////////////
        // Free essences functionality
        //////////////////////////////////////////////////////////////////
        /**
         * common_conditions_value_names table
         */
        $this->createTable('{{%common_free_essences}}', [
            'id'                           => $this->primaryKey(),
            'program_name'                 => $this->string(50),
            'editable'                     => $this->boolean(),
            'visible'                      => $this->boolean(),
            'free_essences_order'          => $this->integer(),
            'field_template_reference'     => $this->string(),
            'field_reference'              => $this->string(),
            'file_template_reference'      => $this->string(),
            'file_reference'               => $this->string(),
            'image_template_reference'     => $this->string(),
            'image_reference'              => $this->string(),
            'condition_template_reference' => $this->string(),
            'condition_reference'          => $this->string(),
        ]);

        /**
         * common_free_essence_name_translates table
         */
        $this->createTable('{{%common_free_essence_name_translates}}', [
            'id'                     => $this->primaryKey(),
            'common_free_essence_id' => $this->integer(),
            'common_language_id'     => $this->integer(),
            'name'                   => $this->string(),
            'description'            => $this->string(),
        ]);

        $this->addForeignKey('common_free_essence_name_translates-to-common_free_essences',
            '{{%common_free_essence_name_translates}}',
            'common_free_essence_id',
            '{{%common_free_essences}}',
            'id'
        );

        $this->addForeignKey('common_free_essence_name_translates-to-common_languages',
            '{{%common_free_essence_name_translates}}',
            'common_language_id',
            '{{%common_languages}}',
            'id'
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        //free essences functionality
        $this->dropForeignKey('common_free_essence_name_translates-to-common_languages',
            '{{%common_free_essence_name_translates}}');
        $this->dropForeignKey('common_free_essence_name_translates-to-common_free_essences',
            '{{%common_free_essence_name_translates}}');
        $this->dropTable('{{%common_free_essence_name_translates}}');
        $this->dropTable('{{%common_free_essences}}');

        //conditions functionality
        $this->dropForeignKey('common_conditions_names-to-common_languages', '{{%common_conditions_names}}');
        $this->dropForeignKey('common_conditions_names-to-common_conditions_templates', '{{%common_conditions_names}}');
        $this->dropTable('{{%common_conditions_names}}');

        $this->dropForeignKey('common_conditions_value_names-to-common_languages', '{{%common_conditions_value_names}}');
        $this->dropForeignKey('common_conditions_value_names-to-common_conditions_values', '{{%common_conditions_value_names}}');
        $this->dropTable('{{%common_conditions_value_names}}');

        $this->dropForeignKey('common_conditions-to-common_conditions_values', '{{%common_conditions}}');
        $this->dropForeignKey('common_conditions_values-to-common_conditions_templates', '{{%common_conditions_values}}');
        $this->dropTable('{{%common_conditions_values}}');

        $this->dropForeignKey('common_conditions-to-common_conditions_templates', '{{%common_conditions}}');
        $this->dropIndex('condition_reference-index', '{{%common_conditions}}');
        $this->dropTable('{{%common_conditions}}');

        $this->dropIndex('condition_template_reference-index', '{{%common_conditions_templates}}');
        $this->dropTable('{{%common_conditions_templates}}');

        //images functionality
        $this->dropForeignKey('common_images_thumbnails-to-common_images_templates', '{{%common_images_thumbnails}}');
        $this->dropTable('{{%common_images_thumbnails}}');

        $this->dropForeignKey('common_image_names-to-common_languages', '{{%common_image_names}}');
        $this->dropForeignKey('common_image_names-to-common_images_templates', '{{%common_image_names}}');
        $this->dropTable('{{%common_image_names}}');

        $this->dropForeignKey('common_image_translates-to-common_languages', '{{%common_image_translates}}');
        $this->dropForeignKey('common_image_translates-to-common_images', '{{%common_image_translates}}');
        $this->dropTable('{{%common_image_translates}}');

        $this->dropForeignKey('common_images-to-common_images_templates', '{{%common_images}}');
        $this->dropIndex('image_reference-index', '{{%common_images}}');
        $this->dropTable('{{%common_images}}');

        $this->dropIndex('image_template_reference-index', '{{%common_images_templates}}');
        $this->dropTable('{{%common_images_templates}}');

        //files functionality
        $this->dropForeignKey('common_file_names-to-common_languages', '{{%common_file_names}}');
        $this->dropForeignKey('common_file_names-to-common_files_templates', '{{%common_file_names}}');
        $this->dropTable('{{%common_file_names}}');

        $this->dropForeignKey('common_file_translates-to-common_languages', '{{%common_file_translates}}');
        $this->dropForeignKey('common_file_translates-to-common_files', '{{%common_file_translates}}');
        $this->dropTable('{{%common_file_translates}}');

        $this->dropForeignKey('common_files-to-common_files_templates', '{{%common_files}}');
        $this->dropIndex('file_reference-index', '{{%common_files}}');
        $this->dropTable('{{%common_files}}');

        $this->dropIndex('file_template_reference-index', '{{%common_files_templates}}');
        $this->dropTable('{{%common_files_templates}}');

        //fields functionality
        $this->dropForeignKey('common_field_names-to-common_languages', '{{%common_field_names}}');
        $this->dropForeignKey('common_field_names-to-common_fields_templates', '{{%common_field_names}}');
        $this->dropTable('{{%common_field_names}}');

        $this->dropForeignKey('common_field_translates-to-common_languages', '{{%common_field_translates}}');
        $this->dropForeignKey('common_field_translates-to-common_fields_represents', '{{%common_field_translates}}');
        $this->dropTable('{{%common_field_translates}}');

        $this->dropForeignKey('common_fields_represents-to-common_fields_templates', '{{%common_fields_represents}}');
        $this->dropIndex('field_reference-index', '{{%common_fields_represents}}');
        $this->dropTable('{{%common_fields_represents}}');

        $this->dropIndex('field_template_reference-index', '{{%common_fields_templates}}');
        $this->dropTable('{{%common_fields_templates}}');

        //common functionality
        $this->dropTable('{{%common_validators}}');
        $this->dropTable('{{%common_languages}}');
        $this->dropTable('{{%common_config}}');
    }
}
