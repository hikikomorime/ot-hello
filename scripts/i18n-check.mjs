#!/usr/bin/env node
/**
 * Fail if English source strings lose the ot-hello text domain
 * or if the pl_PL catalog is missing.
 */

import { readFileSync, existsSync, readdirSync, statSync } from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const domain = 'ot-hello';
let failed = 0;

function walk(dir, acc = []) {
  for (const name of readdirSync(dir)) {
    if (name === 'vendor' || name === 'node_modules' || name === 'dist' || name === 'build') {
      continue;
    }
    const full = path.join(dir, name);
    const st = statSync(full);
    if (st.isDirectory()) {
      walk(full, acc);
    } else if (name.endsWith('.php')) {
      acc.push(full);
    }
  }
  return acc;
}

const files = walk(path.join(root, 'src'));
files.push(path.join(root, 'ot-hello.php'));

const i18nCall = /(?:__|_e|esc_html__|esc_attr__|esc_html_e|esc_attr_e)\(\s*'[^']*'\s*,\s*'([^']+)'\s*\)/g;

for (const file of files) {
  const text = readFileSync(file, 'utf8');
  let match;
  while ((match = i18nCall.exec(text)) !== null) {
    if (match[1] !== domain) {
      console.error(`${file}: unexpected text domain "${match[1]}"`);
      failed += 1;
    }
  }
}

const po = path.join(root, 'languages', `${domain}-pl_PL.po`);
const l10n = path.join(root, 'languages', `${domain}-pl_PL.l10n.php`);
if (!existsSync(po)) {
  console.error('Missing languages/ot-hello-pl_PL.po');
  failed += 1;
}
if (!existsSync(l10n)) {
  console.error('Missing languages/ot-hello-pl_PL.l10n.php');
  failed += 1;
}

if (failed > 0) {
  process.exit(1);
}

console.log('i18n domain + pl_PL catalog OK.');
