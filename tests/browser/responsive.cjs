/*
 * Verifikasi kontrak responsive (GUIDELINES §10) + screenshot halaman demo.
 *
 *   vendor/bin/testbench serve --port=8077 &
 *   node tests/browser/responsive.cjs [baseUrl] [outDir]
 *
 * Memakai Playwright yang terpasang di mesin (require('playwright')).
 * Keluar dengan kode 1 bila ada pelanggaran.
 */
const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

const BASE = process.argv[2] || 'http://127.0.0.1:8077';
const OUT = process.argv[3] || path.join(__dirname, '../../docs/screenshots/0.5');
const WIDTHS = [360, 768, 1280];

const PAGES = [
  { name: 'index', url: '/nexus-demo/index', shot: WIDTHS },
  { name: 'index-cari-tidak-cocok', url: '/nexus-demo/index?search=zzz', shot: [360] },
  { name: 'index-kosong', url: '/nexus-demo/index?state=empty', shot: [360] },
  { name: 'index-flash-sortir', url: '/nexus-demo/index?state=flash&sort=harga&dir=desc', shot: [] },
  { name: 'index-halaman-3', url: '/nexus-demo/index?page=3', shot: [] },
  { name: 'form-tambah', url: '/nexus-demo/form', shot: WIDTHS },
  { name: 'form-edit', url: '/nexus-demo/form?mode=edit', shot: [1280] },
  { name: 'form-error', url: '/nexus-demo/form?state=error', shot: [360, 1280] },
  { name: 'detail', url: '/nexus-demo/detail', shot: WIDTHS },
  { name: 'detail-kosong', url: '/nexus-demo/detail?state=empty', shot: [360] },
  { name: 'galeri', url: '/nexus-demo', shot: [360] },
];

// Elemen interaktif yang wajib ≥ 40px di bawah 900px.
const TOUCH = '.btn, .pg-btn:not(.pg-gap), .form-input, .form-select, .search-box, .topbar-btn, .topbar-toggle, .form-check, .modal-close';

const failures = [];
const fail = (msg) => { failures.push(msg); console.log('  ✗ ' + msg); };
const ok = (msg) => console.log('  ✓ ' + msg);

async function checkPage(page, label, width) {
  const m = await page.evaluate(() => ({
    scroll: document.documentElement.scrollWidth,
    inner: window.innerWidth,
  }));
  if (m.scroll > m.inner) fail(`${label}: scroll horizontal (${m.scroll} > ${m.inner})`);
  else ok(`${label}: scrollWidth ${m.scroll} <= ${m.inner}`);

  if (width < 900) {
    const small = await page.evaluate((sel) => {
      return Array.from(document.querySelectorAll(sel))
        .filter((el) => {
          const r = el.getBoundingClientRect();
          const cs = getComputedStyle(el);
          return r.width > 0 && r.height > 0 && cs.visibility !== 'hidden' && !el.closest('.modal-overlay:not(.show), .dropdown-menu:not(.show), [hidden]');
        })
        .map((el) => ({ el: el.className.toString().split(' ').slice(0, 3).join('.'), h: Math.round(el.getBoundingClientRect().height) }))
        .filter((x) => x.h < 40);
    }, TOUCH);
    if (small.length) fail(`${label}: target sentuh < 40px → ${JSON.stringify(small.slice(0, 5))}`);
    else ok(`${label}: target sentuh ≥ 40px`);
  }
}

(async () => {
  fs.mkdirSync(OUT, { recursive: true });
  const browser = await chromium.launch();

  for (const width of WIDTHS) {
    const ctx = await browser.newContext({ viewport: { width, height: width < 900 ? 780 : 860 }, deviceScaleFactor: 1 });
    const page = await ctx.newPage();
    // Font eksternal tidak diperlukan untuk verifikasi tata letak.
    await page.route(/fonts\.(googleapis|gstatic)\.com/, (r) => r.abort());

    for (const p of PAGES) {
      await page.goto(BASE + p.url, { waitUntil: 'load' });
      const label = `${p.name}@${width}`;
      console.log(label);
      await checkPage(page, label, width);
      if (p.shot.includes(width)) {
        await page.screenshot({ path: path.join(OUT, `${p.name}-${width}.png`), fullPage: true });
      }
    }

    // Dropdown aksi baris: tidak terpotong & di dalam viewport.
    await page.goto(BASE + '/nexus-demo/index', { waitUntil: 'load' });
    const rowTrigger = page.locator('tbody .row-actions-trigger').last();
    await rowTrigger.scrollIntoViewIfNeeded();
    await rowTrigger.click();
    const menu = page.locator('tbody .dropdown-menu.show');
    await page.waitForTimeout(250); // tunggu animasi slideDown
    const dd = await menu.evaluate((el) => {
      const r = el.getBoundingClientRect();
      const hit = document.elementFromPoint(r.left + r.width / 2, r.bottom - 8);
      return { top: r.top, left: r.left, right: r.right, bottom: r.bottom, vw: innerWidth, vh: innerHeight, visible: el.contains(hit), pos: getComputedStyle(el).position };
    });
    const inside = dd.left >= 0 && dd.top >= 0 && dd.right <= dd.vw && dd.bottom <= dd.vh;
    if (inside && dd.visible && dd.pos === 'fixed') ok(`dropdown@${width}: di dalam viewport & tidak terpotong`);
    else fail(`dropdown@${width}: ${JSON.stringify(dd)}`);
    if (width === 360 || width === 1280) await page.screenshot({ path: path.join(OUT, `dropdown-aksi-${width}.png`) });
    await page.keyboard.press('Escape');

    // Modal: penuh layar di HP.
    await page.locator('.page-card-actions .row-actions-trigger').click();
    await page.locator('[data-modal-open="demoImpor"]').click();
    await page.waitForTimeout(350); // tunggu animasi scaleIn selesai
    const mod = await page.locator('#demoImpor .modal').evaluate((el) => {
      const r = el.getBoundingClientRect();
      return { w: Math.round(r.width), h: Math.round(r.height), vw: innerWidth, vh: innerHeight };
    });
    if (width < 600) {
      if (mod.w === mod.vw && mod.h === mod.vh) ok(`modal@${width}: penuh layar`);
      else fail(`modal@${width}: tidak penuh layar ${JSON.stringify(mod)}`);
      await page.screenshot({ path: path.join(OUT, `modal-${width}.png`) });
    } else {
      ok(`modal@${width}: ${mod.w}×${mod.h}`);
    }
    await page.keyboard.press('Escape');
    await page.goto(BASE + '/nexus-demo/index', { waitUntil: 'load' });

    // Sidebar: off-canvas < 900px, terbuka lewat toggle.
    const sb = async () => page.locator('.sidebar').evaluate((el) => Math.round(el.getBoundingClientRect().right));
    if (width < 900) {
      if ((await sb()) <= 0) ok(`sidebar@${width}: tersembunyi (off-canvas)`);
      else fail(`sidebar@${width}: terlihat saat dimuat`);
      await page.locator('[data-sidebar-toggle]').click();
      await page.waitForTimeout(400);
      if ((await sb()) > 0) ok(`sidebar@${width}: terbuka lewat toggle`);
      else fail(`sidebar@${width}: toggle tidak membuka`);
      if (width === 360) await page.screenshot({ path: path.join(OUT, `sidebar-terbuka-${width}.png`) });
    } else {
      if ((await sb()) > 0) ok(`sidebar@${width}: tampil`);
      else fail(`sidebar@${width}: tidak tampil di desktop`);
    }

    await ctx.close();
  }

  // Alur nyata: submit kosong → redirect back withErrors + withInput.
  const ctx = await browser.newContext({ viewport: { width: 360, height: 780 } });
  const page = await ctx.newPage();
  await page.route(/fonts\.(googleapis|gstatic)\.com/, (r) => r.abort());
  await page.goto(BASE + '/nexus-demo/form', { waitUntil: 'load' });
  await page.fill('#kode', 'GD-B-02');
  await page.fill('#nama', '');
  await page.click('.form-actions button[type="submit"]');
  await page.waitForLoadState('load');
  const flow = await page.evaluate(() => ({
    kode: document.querySelector('#kode').value,
    namaInvalid: document.querySelector('#nama').classList.contains('is-invalid'),
    summaryFocused: document.activeElement && document.activeElement.hasAttribute('data-error-summary'),
    links: document.querySelectorAll('[data-error-summary] a').length,
  }));
  if (flow.kode === 'GD-B-02' && flow.namaInvalid && flow.summaryFocused && flow.links >= 2) ok(`alur submit: old() terisi, is-invalid, ringkasan error difokus (${flow.links} tautan)`);
  else fail(`alur submit: ${JSON.stringify(flow)}`);
  await page.screenshot({ path: path.join(OUT, 'form-submit-error-360.png'), fullPage: true });
  await ctx.close();

  await browser.close();
  console.log(failures.length ? `\n${failures.length} pelanggaran` : '\nSemua pemeriksaan lulus');
  process.exit(failures.length ? 1 : 0);
})();
