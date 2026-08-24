import { useCallback, useEffect, useMemo, useState, type FormEvent } from 'react';
import { fetchStatus, getConfig, saveGreeting, saveLicenseKey } from './api';
import { createTranslator, localeFromWp, type LocaleId } from './i18n';
import { AboutPage, DashboardPage, ErrorState, HelpPage, LoadingState, SettingsPage } from './pages';
import type { GreetingSettings, HubId, StatusPayload } from './types';

const HUBS: HubId[] = ['dashboard', 'settings', 'help', 'about'];

function hubFromHash(): HubId {
  const hash = window.location.hash.replace('#', '').replace(/^\//, '');
  switch (hash) {
    case 'dashboard':
    case 'settings':
    case 'help':
    case 'about':
      return hash;
    case '':
      return 'dashboard';
    default:
      return 'dashboard';
  }
}

function hubLabelKey(hub: HubId): string {
  switch (hub) {
    case 'dashboard':
      return 'navDashboard';
    case 'settings':
      return 'navSettings';
    case 'help':
      return 'navHelp';
    case 'about':
      return 'navAbout';
    default: {
      const _exhaustive: never = hub;
      return _exhaustive;
    }
  }
}

export function App() {
  const config = useMemo(() => getConfig(), []);
  const [hub, setHub] = useState<HubId>(hubFromHash);
  const [locale, setLocale] = useState<LocaleId>(localeFromWp(config.locale));
  const [menuOpen, setMenuOpen] = useState(false);
  const [status, setStatus] = useState<'loading' | 'ready' | 'error'>('loading');
  const [data, setData] = useState<StatusPayload | null>(null);
  const [draft, setDraft] = useState<GreetingSettings>({
    greeting: 'Hello',
    audience: 'World',
    show_site_info: true,
  });
  const [licenseDraft, setLicenseDraft] = useState('');
  const [savingSettings, setSavingSettings] = useState(false);
  const [savingLicense, setSavingLicense] = useState(false);
  const [settingsNotice, setSettingsNotice] = useState<{ kind: 'ok' | 'err'; text: string } | null>(null);
  const [licenseNotice, setLicenseNotice] = useState<{ kind: 'ok' | 'err'; text: string } | null>(null);

  const t = useMemo(() => createTranslator(locale), [locale]);

  const load = useCallback(async () => {
    setStatus('loading');
    try {
      const payload = await fetchStatus();
      setData(payload);
      setDraft(payload.settings);
      setStatus('ready');
    } catch {
      setStatus('error');
    }
  }, []);

  useEffect(() => {
    void load();
  }, [load]);

  useEffect(() => {
    const onHash = () => setHub(hubFromHash());
    window.addEventListener('hashchange', onHash);
    return () => window.removeEventListener('hashchange', onHash);
  }, []);

  const go = (next: HubId) => {
    setHub(next);
    setMenuOpen(false);
    window.location.hash = next;
  };

  const onSaveSettings = async (event: FormEvent) => {
    event.preventDefault();
    setSavingSettings(true);
    setSettingsNotice(null);
    try {
      const result = await saveGreeting(draft);
      setData((current) =>
        current
          ? { ...current, message: result.message, settings: result.settings }
          : current
      );
      setDraft(result.settings);
      setSettingsNotice({ kind: 'ok', text: t('saved') });
    } catch {
      setSettingsNotice({ kind: 'err', text: t('saveError') });
    } finally {
      setSavingSettings(false);
    }
  };

  const onSaveLicense = async (event: FormEvent) => {
    event.preventDefault();
    if (licenseDraft.trim() === '') {
      return;
    }
    setSavingLicense(true);
    setLicenseNotice(null);
    try {
      const license = await saveLicenseKey(licenseDraft);
      setData((current) => (current ? { ...current, license } : current));
      setLicenseDraft('');
      const text =
        license.status === 'saved'
          ? t('aboutLicenseSaved')
          : license.status === 'invalid'
            ? t('aboutLicenseInvalid')
            : t('aboutLicenseMissing');
      setLicenseNotice({ kind: license.status === 'invalid' ? 'err' : 'ok', text });
    } catch {
      setLicenseNotice({ kind: 'err', text: t('aboutLicenseSaveError') });
    } finally {
      setSavingLicense(false);
    }
  };

  const onClearLicense = async () => {
    setSavingLicense(true);
    setLicenseNotice(null);
    try {
      const license = await saveLicenseKey('', true);
      setData((current) => (current ? { ...current, license } : current));
      setLicenseDraft('');
      setLicenseNotice({ kind: 'ok', text: t('aboutLicenseMissing') });
    } catch {
      setLicenseNotice({ kind: 'err', text: t('aboutLicenseSaveError') });
    } finally {
      setSavingLicense(false);
    }
  };

  const renderHub = () => {
    if (status === 'loading') {
      return <LoadingState t={t} />;
    }
    if (status === 'error' || data === null) {
      return <ErrorState t={t} onRetry={() => void load()} />;
    }

    switch (hub) {
      case 'dashboard':
        return <DashboardPage data={data} t={t} onOpenSettings={() => go('settings')} />;
      case 'settings':
        return (
          <SettingsPage
            settings={draft}
            t={t}
            preview={config.preview}
            saving={savingSettings}
            notice={settingsNotice}
            onChange={setDraft}
            onSubmit={(event) => void onSaveSettings(event)}
          />
        );
      case 'help':
        return <HelpPage t={t} />;
      case 'about':
        return (
          <AboutPage
            data={data}
            t={t}
            licenseDraft={licenseDraft}
            saving={savingLicense}
            notice={licenseNotice}
            onLicenseDraft={setLicenseDraft}
            onSubmit={(event) => void onSaveLicense(event)}
            onClear={() => void onClearLicense()}
          />
        );
      default: {
        const _exhaustive: never = hub;
        return _exhaustive;
      }
    }
  };

  return (
    <div className={`ot-hello-app${config.preview ? ' ot-hello-preview-root' : ''}`} lang={locale === 'pl' ? 'pl' : 'en'}>
      <div className="ot-hello-topbar">
        <strong>
          {t('appTitle')}
          {config.preview ? <span className="ot-hello-badge">{t('previewBadge')}</span> : null}
        </strong>
        <button
          type="button"
          className="ot-hello-btn ot-hello-btn--secondary"
          onClick={() => setMenuOpen((open) => !open)}
          aria-expanded={menuOpen}
          aria-controls="ot-hello-nav"
        >
          {menuOpen ? t('closeMenu') : t('menu')}
        </button>
      </div>
      <div className="ot-hello-shell">
        <nav id="ot-hello-nav" className={`ot-hello-nav${menuOpen ? ' is-open' : ''}`} aria-label={t('appTitle')}>
          <div className="ot-hello-brand">
            <div className="ot-hello-mark" aria-hidden="true">
              H
            </div>
            <div>
              <h1>
                {t('appTitle')}
                {config.preview ? <span className="ot-hello-badge">{t('previewBadge')}</span> : null}
              </h1>
              <p>{t('appTagline')}</p>
            </div>
          </div>
          <div className="ot-hello-nav-list">
            {HUBS.map((item) => (
              <button
                key={item}
                type="button"
                className={`ot-hello-nav-btn${hub === item ? ' is-active' : ''}`}
                onClick={() => go(item)}
                aria-current={hub === item ? 'page' : undefined}
              >
                {t(hubLabelKey(item))}
              </button>
            ))}
          </div>
          <div className="ot-hello-nav-footer">
            <label htmlFor="ot-hello-lang">{t('language')}</label>
            <select
              id="ot-hello-lang"
              className="ot-hello-select"
              value={locale}
              onChange={(event) => setLocale(event.target.value === 'pl' ? 'pl' : 'en')}
            >
              <option value="en">{t('langEn')}</option>
              <option value="pl">{t('langPl')}</option>
            </select>
          </div>
        </nav>
        <main className="ot-hello-main">{renderHub()}</main>
      </div>
    </div>
  );
}
