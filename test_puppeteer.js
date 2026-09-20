import puppeteer from 'puppeteer';

(async () => {
    const browser = await puppeteer.launch();
    const page = await browser.newPage();
    await page.goto('http://pscranker.test/session/human-skeletal-system-bones?stream=general');
    await page.waitForSelector('.grid'); // Wait for content
    const btn = await page.evaluate(() => {
        const b = Array.from(document.querySelectorAll('button')).find(btn => btn.textContent.includes('206'));
        if (b) {
            return b.outerHTML;
        }
        return null;
    });
    console.log(btn);
    await browser.close();
})();
