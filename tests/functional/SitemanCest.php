<?php

// test controllers/Siteman
class SitemanCest
{
    public function _before($I)
    {
        $I->amOnPage('siteman');
    }

    public function buka_laman_login($I)
    {
        $I->seeCurrentUrlEquals('siteman');

        $I->seeElement('.login-form');
        $I->seeElement('input[name=username]');
        $I->seeElement('input[name=password]');
        $I->seeElement('input[type=checkbox].show-password-check');
        $I->seeElement('[type=submit]');
    }

    public function login_dgn_user_pass_kosong($I)
    {
        $I->submitForm('.login-form', []);

        $I->seeCurrentUrlEquals('siteman');
        $I->see('Login Gagal');
    }

    public function login_dgn_user_pass_salah($I)
    {
        $I->submitForm('.login-form', [
            'username' => 'username_ini',
            'password' => 'tidak_ada',
        ]);

        $I->seeCurrentUrlEquals('siteman');
        $I->see('Login Gagal');
    }

    public function login_success($I)
    {
        $I->submitForm('.login-form', [
            'username' => 'admin',
            'password' => 'www',
        ]);

        $I->dontSeeCurrentUrlEquals('siteman');
        $I->dontSeeElement('.login-form');
        $I->see('Anda Login Sebagai');
        $I->see('Keluar');
    }
}
