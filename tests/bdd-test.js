const { test, expect, Page } = require('@playwright/test');


class Tester {
  /** @param {Page} page */
  constructor(page) { this.page = page }
  /** @param {string} url */
  openPage(url) { return this.page.goto(url) }
  /** @param {string|RegExp} text */
  see(text) { return expect(this.page.locator('body')).toContainText(text) }
  /** @param {string} locator */
  seeElement(locator) { return expect(this.page.locator(locator)).toBeVisible() }
  /** @param {string|RegExp} url */
  seeCurrentURLEquals(url) { return expect(this.page).toHaveURL(url) }
  /** @param {string} text */
  click(text) { return this.page.locator(`text="${text}"`).click() }
  /** @param {string} name @param {string} value */
  fillField(name, value) { return this.page.fill(`[name="${name}"]`, value) }

}
module.exports.Tester = Tester;


/** @param {function(Tester)} testFunction */
module.exports.beforeEach = function(testFunction) {
    test.beforeEach(({page}) => {
        testFunction(new Tester(page), page)
    })
}


/**
 * @param {string} title
 * @param {function(Tester)} testFunction
 */
module.exports.test = function(title, testFunction) {
  test(title, ({ page }) => {
    const tester = new Tester(page)
    // const user = new Proxy(tester, {
    //   get(target, prop) {
    //     if (target[prop]) {
    //       return target[prop]
    //     }
    //     if (page[prop]) {
    //       return page[prop]
    //     }
    //   }
    // })
    return testFunction(tester, page)
  })
}
