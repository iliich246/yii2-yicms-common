<?php


class CommonLanguagesCest
{
    public function _before(FunctionalTester $I)
    {

    }

    public function _after(FunctionalTester $I)
    {

    }

    /**
     * Test languages list page
     * @param FunctionalTester $I
     */
    public function loadLanguagesListTest(FunctionalTester $I)
    {
        $I->amOnPage(['common/dev/languages-list']);
        $I->see('List of languages', 'h1');
        $I->see('English', 'p');
        $I->see('Русский', 'p');
    }

    /**
     * Test create language
     * @param FunctionalTester $I
     */
    public function languageCreateTest(FunctionalTester $I)
    {
        $I->amOnPage(['common/dev/create-language']);
        $I->see('Create new language page', 'h1');
        $I->submitForm('#create-update-language-form', [
            'LanguagesDb[name]' => 'TestLanguage',
            'LanguagesDb[code]' => 'te-LE',
            'LanguagesDb[used]' => true,
        ], 'submitButton');

        $I->dontSeeElement('.has-error');
        $I->canSeeRecord(\Iliich246\YicmsCommon\Languages\LanguagesDb::className(), [
            'name' => 'TestLanguage',
        ]);

        $I->amOnPage(['common/dev/create-language']);
        $I->submitForm('#create-update-language-form', [
            'LanguagesDb[name]' => 'TestLanguage',
            'LanguagesDb[code]' => 'te-LE',
            'LanguagesDb[used]' => true,
        ], 'submitButton');

        $I->seeElement('.has-error');
        $I->canSeeRecord(\Iliich246\YicmsCommon\Languages\LanguagesDb::className(), [
            'name' => 'TestLanguage',
            'code' => 'te-LE',
        ]);

        $I->amOnPage(['common/dev/create-language']);
        $I->submitForm('#create-update-language-form', [
            'LanguagesDb[name]' => 'TestLanguage1',
            'LanguagesDb[code]' => 'te-LELELELELE',
            'LanguagesDb[used]' => true,
        ], 'submitButton');

        $I->seeElement('.has-error');
        $I->dontSeeRecord(\Iliich246\YicmsCommon\Languages\LanguagesDb::className(), [
            'name' => 'TestLanguage1',
        ]);

        $I->amOnPage(['common/dev/create-language']);
        $I->submitForm('#create-update-language-form', [
            'LanguagesDb[name]' => 'TestLanguage',
            'LanguagesDb[code]' => 'te-DE',
            'LanguagesDb[used]' => true,
        ], 'submitButton');

        $I->seeElement('.has-error');
        $I->canSeeRecord(\Iliich246\YicmsCommon\Languages\LanguagesDb::className(), [
            'name' => 'TestLanguage',
            'code' => 'te-LE',
        ]);

        $I->amOnPage(['common/dev/create-language']);
        $I->submitForm('#create-update-language-form', [
            'LanguagesDb[name]' => 'TestLanguage2',
            'LanguagesDb[code]' => 'te-LE',
            'LanguagesDb[used]' => true,
        ], 'submitButton');

        $I->seeElement('.has-error');
        $I->canSeeRecord(\Iliich246\YicmsCommon\Languages\LanguagesDb::className(), [
            'name' => 'TestLanguage',
            'code' => 'te-LE',
        ]);
    }

    /**
     * Test update language
     * @param FunctionalTester $I
     */
    public function updateLanguageTest(FunctionalTester $I)
    {
        $I->amOnPage(['common/dev/update-language', 'id' => 1]);
        $I->see('Edit language page', 'h1');

        $I->submitForm('#create-update-language-form', [
            'LanguagesDb[name]' => 'English1',
            'LanguagesDb[code]' => 'en-EU',
            'LanguagesDb[used]' => true,
        ], 'submitButton');

        $I->dontSeeElement('.has-error');
        $I->canSeeRecord(\Iliich246\YicmsCommon\Languages\LanguagesDb::className(), [
            'name' => 'English1',
        ]);
        $I->dontSeeRecord(\Iliich246\YicmsCommon\Languages\LanguagesDb::className(), [
            'name' => 'English',
        ]);

        $I->submitForm('#create-update-language-form', [
            'LanguagesDb[name]' => 'Русский',
            'LanguagesDb[code]' => 'en-EU',
            'LanguagesDb[used]' => true,
        ], 'submitButton');

        $I->seeElement('.has-error');
        $I->canSeeRecord(\Iliich246\YicmsCommon\Languages\LanguagesDb::className(), [
            'name' => 'English1',
            'code' => 'en-EU',
        ]);

        $I->submitForm('#create-update-language-form', [
            'LanguagesDb[name]' => 'English',
            'LanguagesDb[code]' => 'en-EU',
            'LanguagesDb[used]' => true,
        ], 'submitButton');

        $I->dontSeeElement('.has-error');
        $I->canSeeRecord(\Iliich246\YicmsCommon\Languages\LanguagesDb::className(), [
            'name' => 'English',
        ]);

        $I->submitForm('#create-update-language-form', [
            'LanguagesDb[name]' => 'English',
            'LanguagesDb[code]' => 'ru-RU',
            'LanguagesDb[used]' => true,
        ], 'submitButton');

        $I->seeElement('.has-error');
        $I->canSeeRecord(\Iliich246\YicmsCommon\Languages\LanguagesDb::className(), [
            'name' => 'English',
            'code' => 'en-EU',
        ]);

        $I->submitForm('#create-update-language-form', [
            'LanguagesDb[name]' => 'English',
            'LanguagesDb[code]' => 'en-EU',
            'LanguagesDb[used]' => 0,
        ], 'submitButton');

        $I->seeElement('.has-error');
        $I->canSeeRecord(\Iliich246\YicmsCommon\Languages\LanguagesDb::className(), [
            'name' => 'English',
            'code' => 'en-EU',
        ]);

        $I->amOnPage(['common/dev/update-language', 'id' => 2]);

        $I->submitForm('#create-update-language-form', [
            'LanguagesDb[name]' => 'Русский',
            'LanguagesDb[code]' => 'ru-RU',
            'LanguagesDb[used]' => 1,
        ], 'submitButton');

        $I->dontSeeElement('.has-error');
        $I->canSeeRecord(\Iliich246\YicmsCommon\Languages\LanguagesDb::className(), [
            'name' => 'Русский',
            'used' => true,
        ]);

        $I->amOnPage(['common/dev/update-language', 'id' => 2]);
        $I->submitForm('#create-update-language-form', [
            'LanguagesDb[name]' => 'Русский',
            'LanguagesDb[code]' => 'ru-RU',
            'LanguagesDb[used]' => 0,
        ], 'submitButton');

        $I->dontSeeElement('.has-error');
        $I->canSeeRecord(\Iliich246\YicmsCommon\Languages\LanguagesDb::className(), [
            'name' => 'Русский',
            'used' => false,
        ]);
    }

    /**
     * Test delete language
     * @param FunctionalTester $I
     */
    public function deleteLanguage(FunctionalTester $I)
    {
        $I->amOnPage(['common/dev/create-language']);
        $I->submitForm('#create-update-language-form', [
            'LanguagesDb[name]' => 'TestDelete',
            'LanguagesDb[code]' => 'te-DE',
            'LanguagesDb[used]' => true,
        ], 'submitButton');

        $I->see('TestDelete', 'p');
        $I->canSeeRecord(\Iliich246\YicmsCommon\Languages\LanguagesDb::className(), [
            'name' => 'TestDelete',
        ]);

        /** @var \Iliich246\YicmsCommon\Languages\LanguagesDb $language */
        $language = \Iliich246\YicmsCommon\Languages\LanguagesDb::find()->where([
            'name' => 'TestDelete'
        ])->one();

        $I->amOnPage(['common/dev/update-language', 'id' => $language->id]);
        $I->seeElement('.btn-boot-box');

        //Can`t emulate pjax request there
    }
}
