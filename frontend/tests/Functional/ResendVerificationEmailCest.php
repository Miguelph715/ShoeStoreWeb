<?php

declare(strict_types=1);

namespace frontend\tests\Functional;

use common\fixtures\UserFixture;
use common\models\User;
use frontend\tests\Support\FunctionalTester;

final class ResendVerificationEmailCest
{
    protected string $formId = '#resend-verification-email-form';
    public function _fixtures()
    {
        return [
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . 'user.php',
            ],
        ];
    }

    public function _before(FunctionalTester $I)
    {
        $I->amOnRoute('/site/resend-verification-email');
    }

    protected function formParams(string $email)
    {
        return [
            'ResendVerificationEmailForm[email]' => $email,
        ];
    }

    public function checkPage(FunctionalTester $I)
    {
        $I->see('Resend verification email', 'h1');
        $I->see('Enter your email to receive a new verification link');
    }

    public function checkEmptyField(FunctionalTester $I)
    {
        $I->submitForm($this->formId, $this->formParams(''));
        $I->seeValidationError('Email cannot be blank.');
    }

    public function checkWrongEmailFormat(FunctionalTester $I)
    {
        $I->submitForm($this->formId, $this->formParams('abcd.com'));
        $I->seeValidationError('Email is not a valid email address.');
    }

    public function checkWrongEmail(FunctionalTester $I)
    {
        $I->submitForm($this->formId, $this->formParams('wrong@email.com'));
        $I->seeValidationError('There is no user with this email address.');
    }

    public function checkAlreadyVerifiedEmail(FunctionalTester $I)
    {
        $I->submitForm($this->formId, $this->formParams('test2@mail.com'));
        $I->seeValidationError('There is no user with this email address.');
    }

    public function checkSendSuccessfully(FunctionalTester $I)
    {
        $I->submitForm($this->formId, $this->formParams('test@mail.com'));
        $I->canSeeEmailIsSent();
        $I->seeRecord(
            User::class, 
            [
                'email' => 'test@mail.com',
                'username' => 'test.test',
                'status' => User::STATUS_INACTIVE,
            ],
        );
        $I->see('Check your email for further instructions.');
    }
}
