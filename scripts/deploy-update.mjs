#!/usr/bin/env node
/**
 * Upload the release ZIP + manifests to plugins.onethird.pl
 * Remote dir: public_html/updates/plugins/ot-hello/
 *
 * Credentials (never committed):
 *   1. scripts/.env
 *   2. sibling OneThird trees: ../ai-rewriter, ../ot-rewriter, ../ot-seo
 *
 * Sibling REMOTE_DIR is ignored so rewriter's ot-rewriter catalog is not reused.
 */

import { existsSync, readFileSync } from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { spawnSync } from 'node:child_process';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const localEnvPath = path.join(root, 'scripts', '.env');
const siblingEnvPaths = [
  path.join(root, '..', 'ai-rewriter', 'scripts', '.env'),
  path.join(root, '..', 'ot-rewriter', 'scripts', '.env'),
  path.join(root, '..', 'ot-seo', 'scripts', '.env'),
];

function parseEnv(text) {
  const out = {};
  for (const line of text.split('\n')) {
    const trimmed = line.trim();
    if (!trimmed || trimmed.startsWith('#')) {
      continue;
    }
    const eq = trimmed.indexOf('=');
    if (eq === -1) {
      continue;
    }
    out[trimmed.slice(0, eq).trim()] = trimmed.slice(eq + 1).trim();
  }
  return out;
}

function loadFtpEnv() {
  const env = {};
  const sources = [];

  for (const candidate of siblingEnvPaths) {
    if (!existsSync(candidate)) {
      continue;
    }
    const parsed = parseEnv(readFileSync(candidate, 'utf8'));
    delete parsed.OT_PLUGINS_FTP_REMOTE_DIR;
    Object.assign(env, parsed);
    sources.push(path.relative(root, candidate));
  }

  if (existsSync(localEnvPath)) {
    Object.assign(env, parseEnv(readFileSync(localEnvPath, 'utf8')));
    sources.push('scripts/.env');
  }

  const remote = env.OT_PLUGINS_FTP_REMOTE_DIR || 'ot-hello';
  const siblingCatalogs = new Set(['ot-rewriter', 'ot-seo', 'ai-rewriter', 'ai-helpdesk-rewriter']);
  env.OT_PLUGINS_FTP_REMOTE_DIR = siblingCatalogs.has(remote) ? 'ot-hello' : remote;

  return { env, sources };
}

const { env, sources } = loadFtpEnv();
const host = env.OT_PLUGINS_FTP_HOST || 'plugins.onethird.pl';
const user = env.OT_PLUGINS_FTP_USER;
const pass = env.OT_PLUGINS_FTP_PASS;
const remoteDir = env.OT_PLUGINS_FTP_REMOTE_DIR || 'ot-hello';

if (!user || !pass) {
  console.error('Missing OT_PLUGINS_FTP_USER / OT_PLUGINS_FTP_PASS.');
  console.error('Place them in scripts/.env or in a sibling OneThird plugin:');
  console.error('  ../ai-rewriter/scripts/.env');
  console.error('  ../ot-rewriter/scripts/.env');
  console.error('  ../ot-seo/scripts/.env');
  console.error('Canonical Windows tree: C:\\Users\\micha\\OneDrive\\Dokumenty\\AI-Projects\\OneThird\\ot-hello');
  process.exit(1);
}

if (sources.length > 0) {
  console.log('FTP credentials from: ' + sources.join(', '));
}

const header = readFileSync(path.join(root, 'ot-hello.php'), 'utf8');
const versionMatch = header.match(/^\s*\*\s*Version:\s*([0-9.]+)/m);
const version = versionMatch?.[1];
const zipPath = path.join(root, 'backups', `ot-hello-v${version}.zip`);
const latestPath = path.join(root, 'dist', 'latest.json');
const updatePath = path.join(root, 'dist', 'update.json');
const versionTxtPath = path.join(root, 'dist', 'version.txt');

if (!existsSync(zipPath) || !existsSync(updatePath)) {
  console.error('Run npm run archive-release before deploy-update.');
  process.exit(1);
}

const lftp = spawnSync('lftp', ['-v'], { encoding: 'utf8' });
if (lftp.error) {
  console.error('lftp is required for deploy-update. Install it or upload manually to:');
  console.error(`  ftp://${host}/public_html/updates/plugins/${remoteDir}/`);
  console.error('  files: latest.json, update.json, version.txt, and the versioned ZIP from backups/');
  process.exit(1);
}

const remoteSafe = /^[A-Za-z0-9._-]+$/.test(remoteDir) ? remoteDir : '';
if (!remoteSafe) {
  console.error('OT_PLUGINS_FTP_REMOTE_DIR must be a simple slug (e.g. ot-hello).');
  process.exit(1);
}

const script = `
set ftp:ssl-allow yes
set ftp:ssl-force true
open -u ${JSON.stringify(user)},${JSON.stringify(pass)} ${JSON.stringify(host)}
mkdir -p public_html/updates/plugins/${remoteSafe}
cd public_html/updates/plugins/${remoteSafe}
put ${JSON.stringify(zipPath)}
put ${JSON.stringify(updatePath)}
put ${JSON.stringify(latestPath)}
put ${JSON.stringify(versionTxtPath)}
bye
`;

const upload = spawnSync('lftp', ['-c', script], { stdio: 'inherit' });
process.exit(upload.status ?? 1);
