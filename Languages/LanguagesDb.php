<?php

namespace Iliich246\YicmsCommon\Languages;

use Iliich246\YicmsCommon\Base\CommonConfigDb;
use yii\db\ActiveRecord;
use yii\db\ActiveQuery;
use Iliich246\YicmsCommon\Base\CommonException;

/**
 * Class LanguagesDb
 *
 * @property integer $id
 * @property string $code
 * @property string $name
 * @property bool $used
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class LanguagesDb extends ActiveRecord
{
    const SCENARIO_CREATE = 0x00;
    const SCENARIO_UPDATE = 0x01;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%common_languages}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Language name (on that language)',
            'code' => 'Code of language in ISO format',
            'used' => 'Is this language activated',
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            self::SCENARIO_CREATE => [
                'name', 'code', 'used'
            ],
            self::SCENARIO_UPDATE => [
                'name', 'code', 'used'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return
            [
                [['name', 'code'], 'required', 'message' => 'Obligatory input field'],
                ['name', 'string', 'max' => 50, 'tooLong' => 'Name of language must be less than 50 symbols'],
                ['code', 'string', 'max' => '5', 'tooLong' => 'Code of language must be less than 5 symbols'],
                [
                    ['name'], 'unique', 'skipOnError' => true, 'skipOnEmpty' => true,
                    'targetClass' => LanguagesDb::className(),
                    'targetAttribute' => ['name'],
                    'message' => 'This value has already exist in languages table',
                    'filter' => function($query) {
                        /** @var $query ActiveQuery */
                        if ($this->scenario == self::SCENARIO_UPDATE)
                            $query->andWhere(['not in', 'name', $this->getOldAttribute('name')]);
                        return $query;
                    }
                ],
                [
                    ['code'], 'unique', 'skipOnError' => true, 'skipOnEmpty' => true,
                    'targetClass' => LanguagesDb::className(),
                    'targetAttribute' => ['code'],
                    'message' => 'This value has already exist in languages table',
                    'filter' => function($query) {
                        /** @var $query ActiveQuery */
                        if ($this->scenario == self::SCENARIO_UPDATE)
                            $query->andWhere(['not in', 'code', $this->getOldAttribute('code')]);
                        return $query;
                    }
                ],
                [['used'], 'boolean'],
                ['used', 'validateNotDefault', 'on' => self::SCENARIO_UPDATE]
        ];
    }

    /**
     * Validates used param, if he sets fo false, language must not be default.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateNotDefault($attribute, $params)
    {
        if (!$this->hasErrors()) {

            $config = CommonConfigDb::getInstance();

            if ($config->defaultLanguage == $this->code && $this->used == 0)
                $this->addError($attribute, 'It is forbidden to deactivate default language. You can change default
                language and deactivate this language.');
        }
    }

    /**
     * Return true, if this language is active in system
     * @return bool
     * @throws CommonException
     */
    public function isActive()
    {
        $languageFacade = Language::getInstance();

        if ($languageFacade->getCurrentLanguage()->id === $this->id) return true;
        return false;
    }

    /**
     * Returns instance of language by his code
     * @param $code
     * @return self|null
     */
    public static function instanceByCode($code)
    {
        return self::find()->where([
            'code' => $code
        ])->one();
    }
}
