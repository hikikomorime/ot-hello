# OneThird local layout

OT Hello lives next to the other OneThird plugins — same convention as `ot-seo`.

```
C:\Users\micha\OneDrive\Dokumenty\AI-Projects\OneThird\
  ot-hello\                 ← this repo (ot-hello.php)
  ot-seo\
  ai-rewriter\              ← shared FTP account in scripts/.env
  ot-rewriter\              ← alternate rewriter folder name
```

## Clone

```powershell
cd C:\Users\micha\OneDrive\Dokumenty\AI-Projects\OneThird
git clone https://github.com/hikikomorime/ot-hello.git ot-hello
cd ot-hello
powershell -File scripts\bootstrap-onethird.ps1
```

`bootstrap-onethird.ps1` will:

1. Ensure the repo is at that path and `origin`/`github` point at hikikomorime/ot-hello
2. Reuse `..\ai-rewriter\scripts\.env` or `..\ot-rewriter\scripts\.env` for FTP
3. Force `OT_PLUGINS_FTP_REMOTE_DIR=ot-hello`
4. Run `npm install` and `composer install` when those tools are on PATH

This Cloud Agent cannot write to the Windows OneDrive path. After GitHub has `main`, run the script on the PC.

## WordPress install folder

The on-disk project folder and the WordPress plugin folder are both `ot-hello` (like `ot-seo` / `ot-seo.php`).
