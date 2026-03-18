const path = require('path');
const { chromium } = require('playwright');

async function main() {
  const browser = await chromium.launch({ headless: true });
  const page = await browser.newPage({ viewport: { width: 1440, height: 1100 } });
  const fixturePath = path.resolve(process.cwd(), 'output/playwright/blank-canvas-check.html');
  const url = `file:///${fixturePath.replace(/\\/g, '/')}`;

  await page.goto(url, { waitUntil: 'networkidle' });
  await page.waitForTimeout(500);

  const result = await page.evaluate(() => {
    const header = document.querySelector('.lms-header');
    const firstContentBlock = document.getElementById('firstContentBlock');

    if (!header || !firstContentBlock) {
      return { ok: false, error: 'Required elements not found.' };
    }

    const headerRect = header.getBoundingClientRect();
    const contentRect = firstContentBlock.getBoundingClientRect();
    const offsetVar = getComputedStyle(document.documentElement)
      .getPropertyValue('--blank-canvas-header-offset')
      .trim();

    return {
      ok: contentRect.top >= headerRect.bottom - 1,
      headerBottom: headerRect.bottom,
      contentTop: contentRect.top,
      offsetVar,
      overlap: Math.max(0, headerRect.bottom - contentRect.top),
    };
  });

  await page.screenshot({
    path: 'output/playwright/blank-canvas-check.png',
    fullPage: true,
  });

  console.log(JSON.stringify(result, null, 2));

  await browser.close();

  if (!result.ok) {
    process.exitCode = 1;
  }
}

main().catch((error) => {
  console.error(error);
  process.exit(1);
});
