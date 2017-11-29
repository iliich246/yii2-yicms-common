<?php


class TestClassCest
{
    public function _before(FunctionalTester $I)
    {

    }

    public function _after(FunctionalTester $I)
    {

    }

    // tests
    public function loadLanguagesList(FunctionalTester $I)
    {
        $I->amOnPage(['common/dev/languages-list']);
        $I->see('List of languages', 'h1');
    }

    public function loadLanguageCreate(FunctionalTester $I)
    {
        $I->amOnPage(['common/dev/create-language']);
        $I->see('Create new language page', 'h1');
        $I->submitForm('#create-update-language-form', [
            'LanguagesDb[name]' => 'test',
            'LanguagesDb[code]' => 'test',
            'LanguagesDb[used]' => true,
        ], 'submitButton');
        $I->seeElement('.has-error');
        $I->canSeeRecord(\Iliich246\YicmsCommon\Languages\LanguagesDb::className(), [
            'name' => 'English',
        ]);
    }


}
