#!/usr/bin/env node
/**
 * Build a WordPress-installable ZIP: backups/ot-hello-v{version}.zip
 * Root folder inside the archive is ot-hello/.
 * Update catalog slug on plugins.onethird.pl is ot-hello (same pattern as ot-rewriter).
 */

import { createHash } from 'node:crypto';
import { existsSync, mkdirSync, readFileSync, rmSync, statSync, writeFileSync } from 'node:fs';
import { cp } from 'node:fs/promises';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { spawnSync } from 'node:child_process';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const installSlug = 'ot-hello';
const catalogSlug = 'ot-hello';
const header = readFileSync(path.join(root, `${installSlug}.php`), 'utf8');
const versionMatch = header.match(/^\s*\*\s*Version:\s*([0-9.]+)/m);
if (!versionMatch) {
  console.error('Could not read Version from ot-hello.php');
  process.exit(1);
}

const version = versionMatch[1];
const distRoot = path.join(root, 'dist', installSlug);
const backupsDir = path.join(root, 'backups');
const zipName = `${installSlug}-v${version}.zip`;
const zipPath = path.join(backupsDir, zipName);

const include = [
  'ot-hello.php',
  'uninstall.php',
  'readme.txt',
  'LICENSE',
  'CHANGELOG.md',
  'src',
  'includes',
  'build',
  'assets',
  'languages',
];

if (!existsSync(path.join(root, 'build', 'admin.js'))) {
  console.error('Missing build/admin.js — run npm run build first.');
  process.exit(1);
}

rmSync(path.join(root, 'dist'), { recursive: true, force: true });
mkdirSync(distRoot, { recursive: true });
mkdirSync(backupsDir, { recursive: true });

for (const rel of include) {
  const from = path.join(root, rel);
  if (!existsSync(from)) {
    console.error(`Missing required path: ${rel}`);
    process.exit(1);
  }
  await cp(from, path.join(distRoot, rel), { recursive: statSync(from).isDirectory() });
}

if (existsSync(zipPath)) {
  rmSync(zipPath);
}

const zip = spawnSync('zip', ['-r', '-q', zipPath, installSlug], {
  cwd: path.join(root, 'dist'),
  stdio: 'inherit',
});

if (zip.status !== 0) {
  console.error('zip failed — is the zip CLI installed?');
  process.exit(zip.status ?? 1);
}

const sha256 = createHash('sha256').update(readFileSync(zipPath)).digest('hex');
const downloadUrl = `https://plugins.onethird.pl/updates/plugins/${catalogSlug}/${zipName}`;

const manifest = {
  slug: catalogSlug,
  version,
  download_url: downloadUrl,
  requires: '6.4',
  tested: '6.6',
  requires_php: '8.1',
  last_updated: new Date().toISOString().replace('T', ' ').slice(0, 19),
  sha256,
  description: 'A modern OneThird hello plugin with a Fluent admin hub.',
  changelog: '0.1.2 — OT convention: install slug ot-hello, PHP OTHello, REST ot-hello/v1. 0.1.1 — Product name OT Hello. 0.1.0 — First public slice.',
};

writeFileSync(path.join(root, 'dist', 'latest.json'), JSON.stringify(manifest, null, 2) + '\n');
writeFileSync(path.join(root, 'dist', 'update.json'), JSON.stringify(manifest, null, 2) + '\n');
writeFileSync(path.join(root, 'dist', 'version.txt'), version + '\n');

console.log(`Wrote ${path.relative(root, zipPath)}`);
console.log(`Wrote dist/update.json for ${downloadUrl}`);
