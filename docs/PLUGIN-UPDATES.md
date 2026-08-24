# Official updates (plugins.onethird.pl)

OT Hello follows the **same catalog as OT re:Writer**: product slug on the update server, now also matching the WordPress install folder.

| Item | OT re:Writer | OT Hello |
|------|----------------|----------|
| GitHub | (rewriter repo) | https://github.com/hikikomorime/ot-hello |
| Local tree | `OneThird\ai-rewriter` | `OneThird\ot-hello` |
| Update URI | `/updates/plugins/ot-rewriter/` | `/updates/plugins/ot-hello/` |
| Manifest | `update.json` (+ `version.txt` fallback) | `update.json` (+ `latest.json` + `version.txt`) |
| ZIP inside | `ai-helpdesk-rewriter/` | `ot-hello/` |
| ZIP filename | `ai-helpdesk-rewriter-{ver}.zip` | `ot-hello-v{ver}.zip` |
| FTP remote dir | `ot-rewriter` | `ot-hello` |
| FTP account | shared `OT_PLUGINS_FTP_*` | **same account** |

## Credentials

`npm run deploy-update` loads, in order:

1. `../ai-rewriter/scripts/.env`
2. `../ot-rewriter/scripts/.env`
3. `../ot-seo/scripts/.env`
4. local `scripts/.env` (wins)

Sibling `OT_PLUGINS_FTP_REMOTE_DIR` is ignored so rewriter’s catalog is never reused. Local file may set it; default is `ot-hello`.

```bash
# optional local override
cp ../ai-rewriter/scripts/.env scripts/.env
# OT_PLUGINS_FTP_REMOTE_DIR=ot-hello
```

Never commit `scripts/.env`.

## Maintainer flow

1. Bump version in `ot-hello.php`, `package.json`, `readme.txt`, `CHANGELOG.md`
2. `npm run archive-release` — ZIP in `backups/` + `dist/update.json`, `latest.json`, `version.txt`
3. `npm run deploy-update` — `mkdir` + upload to `public_html/updates/plugins/ot-hello/`
4. Push the release to GitHub: https://github.com/hikikomorime/ot-hello (`scripts/push-github.ps1`)

A valid `OT-HELLO-` (or legacy `OT-HW-`) key is required **on the site** before WordPress receives a `package` URL. Features stay unlocked without a key.

## Manifest shape

```json
{
  "slug": "ot-hello",
  "version": "0.1.2",
  "download_url": "https://plugins.onethird.pl/updates/plugins/ot-hello/ot-hello-v0.1.2.zip",
  "requires": "6.4",
  "tested": "6.6",
  "requires_php": "8.1",
  "sha256": "…"
}
```
