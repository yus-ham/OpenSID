const { beforeEach, test, Tester } = require('./bdd-test.js');


beforeEach(/** @param {Tester} user */ async function(user) {
    await user.openPage('/siteman')
})


test('Nama dan alamat desa', 
/** @param {Tester} user */ async function(user) {
    await user.see('Desa Senggig1')
    await user.see('Jl. Raya Senggigi Km 10 Kerandangan')
    await user.see('Kodepos 83355')
    await user.see('Kecamatan Batulay4r')
    await user.see('Kabupaten Lombok Bart')
})


test('Login sbg admin',
/** @param {Tester} user */ async function(user) {
    await user.fillField('username', 'admin')
    await user.fillField('password', '123456789')
    await user.click('MASUK')
    await user.seeCurrentURLEquals('/hom_sid')
})


test('Login dengan user yang tidak ada',
/** @param {Tester} user */ async function(user) {
    await user.fillField('username', 'userNotExist')
    await user.fillField('password', 'wrongPassword')
    await user.click('MASUK')
    await user.seeCurrentURLEquals('/siteman')
    await user.see(/Login Gagal/)
})
