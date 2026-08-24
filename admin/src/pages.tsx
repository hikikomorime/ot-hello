import type { FormEvent } from 'react';
import { getConfig } from './api';
import type { GreetingSettings, LicenseState, StatusPayload } from './types';

type Translate = (key: string) => string;

type DashboardProps = {
  data: StatusPayload;
  t: Translate;
  onOpenSettings: () => void;
};

export function DashboardPage({ data, t, onOpenSettings }: DashboardProps) {
  const empty =
    data.settings.greeting.trim() === '' && data.settings.audience.trim() === '';

  return (
    <section>
      <h2 className="ot-hello-page-title">{t('navDashboard')}</h2>
      <p className="ot-hello-lede">{t('appTagline')}</p>
      <div className="ot-hello-grid">
        <article className="ot-hello-card ot-hello-span-8">
          <h3>{t('greetingHeading')}</h3>
          {empty ? (
            <div className="ot-hello-empty">
              <p>{t('greetingEmpty')}</p>
              <button type="button" className="ot-hello-btn" onClick={onOpenSettings}>
                {t('openSettings')}
              </button>
            </div>
          ) : (
            <p className="ot-hello-greeting-hero">{data.message}</p>
          )}
        </article>
        <article className="ot-hello-card ot-hello-span-4">
          <h3>{t('siteHeading')}</h3>
          {data.settings.show_site_info ? (
            <ul className="ot-hello-meta-list">
              <li>
                <span>{t('siteName')}</span>
                <strong>{data.site.name}</strong>
              </li>
              <li>
                <span>{t('wpVersion')}</span>
                <strong>{data.site.wpVersion}</strong>
              </li>
              <li>
                <span>{t('phpVersion')}</span>
                <strong>{data.site.phpVersion}</strong>
              </li>
              <li>
                <span>{t('pluginVersion')}</span>
                <strong>{data.version}</strong>
              </li>
            </ul>
          ) : (
            <p className="ot-hello-empty">{t('siteInfoHidden')}</p>
          )}
        </article>
        <article className="ot-hello-card ot-hello-span-12">
          <h3>{t('nextHeading')}</h3>
          <div className="ot-hello-steps">
            <article>
              <h3>{t('nextShortcodeTitle')}</h3>
              <p>
                {t('nextShortcodeBody')}{' '}
                <code className="ot-hello-code">[ot_hello]</code>
              </p>
            </article>
            <article>
              <h3>{t('nextLicenseTitle')}</h3>
              <p>{t('nextLicenseBody')}</p>
            </article>
            <article>
              <h3>{t('nextPanicTitle')}</h3>
              <p>
                {t('nextPanicBody')}{' '}
                <code className="ot-hello-code">OT_HELLO_DISABLE</code>
              </p>
            </article>
          </div>
        </article>
      </div>
    </section>
  );
}

type SettingsProps = {
  settings: GreetingSettings;
  t: Translate;
  preview: boolean;
  saving: boolean;
  notice: { kind: 'ok' | 'err'; text: string } | null;
  onChange: (next: GreetingSettings) => void;
  onSubmit: (event: FormEvent) => void;
};

export function SettingsPage({
  settings,
  t,
  preview,
  saving,
  notice,
  onChange,
  onSubmit,
}: SettingsProps) {
  return (
    <section>
      <h2 className="ot-hello-page-title">{t('navSettings')}</h2>
      <p className="ot-hello-lede">{t('settingsIntro')}</p>
      {preview ? <p className="ot-hello-banner ot-hello-banner--warn">{t('previewNote')}</p> : null}
          {notice ? (
            <p
              className={`ot-hello-banner ot-hello-banner--${notice.kind}`}
              role={notice.kind === 'err' ? 'alert' : 'status'}
            >
              {notice.text}
            </p>
          ) : null}
      <form className="ot-hello-card ot-hello-span-12" onSubmit={onSubmit}>
        <div className="ot-hello-field">
          <label htmlFor="ot-hello-greeting">{t('fieldGreeting')}</label>
          <input
            id="ot-hello-greeting"
            className="ot-hello-input"
            value={settings.greeting}
            maxLength={80}
            onChange={(event) => onChange({ ...settings, greeting: event.target.value })}
          />
          <p className="ot-hello-help">{t('fieldGreetingHelp')}</p>
        </div>
        <div className="ot-hello-field">
          <label htmlFor="ot-hello-audience">{t('fieldAudience')}</label>
          <input
            id="ot-hello-audience"
            className="ot-hello-input"
            value={settings.audience}
            maxLength={80}
            onChange={(event) => onChange({ ...settings, audience: event.target.value })}
          />
          <p className="ot-hello-help">{t('fieldAudienceHelp')}</p>
        </div>
        <div className="ot-hello-field">
          <label className="ot-hello-check">
            <input
              type="checkbox"
              checked={settings.show_site_info}
              onChange={(event) => onChange({ ...settings, show_site_info: event.target.checked })}
            />
            <span>{t('fieldSiteInfo')}</span>
          </label>
        </div>
        <div className="ot-hello-actions">
          <button type="submit" className="ot-hello-btn" disabled={saving}>
            {saving ? t('saving') : t('save')}
          </button>
        </div>
      </form>
    </section>
  );
}

type HelpProps = { t: Translate };

export function HelpPage({ t }: HelpProps) {
  return (
    <section>
      <h2 className="ot-hello-page-title">{t('navHelp')}</h2>
      <p className="ot-hello-lede">{t('helpIntro')}</p>
      <div className="ot-hello-card ot-hello-help-list">
        <article>
          <h3>{t('helpInstallTitle')}</h3>
          <p>{t('helpInstallBody')}</p>
        </article>
        <article>
          <h3>{t('helpAdminTitle')}</h3>
          <p>{t('helpAdminBody')}</p>
        </article>
        <article>
          <h3>{t('helpRestTitle')}</h3>
          <p>{t('helpRestBody')}</p>
        </article>
        <article>
          <h3>{t('helpShortcodeTitle')}</h3>
          <p>{t('helpShortcodeBody')}</p>
        </article>
        <article>
          <h3>{t('helpLicenseTitle')}</h3>
          <p>{t('helpLicenseBody')}</p>
        </article>
        <article>
          <h3>{t('helpI18nTitle')}</h3>
          <p>{t('helpI18nBody')}</p>
        </article>
      </div>
    </section>
  );
}

type AboutProps = {
  data: StatusPayload;
  t: Translate;
  licenseDraft: string;
  saving: boolean;
  notice: { kind: 'ok' | 'err'; text: string } | null;
  onLicenseDraft: (value: string) => void;
  onSubmit: (event: FormEvent) => void;
  onClear: () => void;
};

function licenseCopy(status: LicenseState['status'], t: Translate): string {
  switch (status) {
    case 'missing':
      return t('aboutLicenseMissing');
    case 'invalid':
      return t('aboutLicenseInvalid');
    case 'saved':
      return t('aboutLicenseSaved');
    default: {
      const _exhaustive: never = status;
      return _exhaustive;
    }
  }
}

export function AboutPage({
  data,
  t,
  licenseDraft,
  saving,
  notice,
  onLicenseDraft,
  onSubmit,
  onClear,
}: AboutProps) {
  return (
    <section>
      <h2 className="ot-hello-page-title">{t('navAbout')}</h2>
      <p className="ot-hello-lede">{t('aboutLead')}</p>
      <div className="ot-hello-grid">
        <article className="ot-hello-card ot-hello-span-6">
          <h3>{t('aboutFeaturesTitle')}</h3>
          <ul className="ot-hello-features">
            <li>{t('aboutFeature1')}</li>
            <li>{t('aboutFeature2')}</li>
            <li>{t('aboutFeature3')}</li>
            <li>{t('aboutFeature4')}</li>
            <li>{t('aboutFeature5')}</li>
          </ul>
        </article>
        <article className="ot-hello-card ot-hello-span-6">
          <h3>{t('aboutChangelogTitle')}</h3>
          <p>{t('aboutChangelog012')}</p>
          <p>{t('aboutChangelog011')}</p>
          <p>{t('aboutChangelog010')}</p>
          <ul className="ot-hello-meta-list" style={{ marginTop: '1rem' }}>
            <li>
              <span>{t('aboutAuthor')}</span>
              <strong>{t('aboutAuthorValue')}</strong>
            </li>
            <li>
              <span>{t('pluginVersion')}</span>
              <strong>{data.version}</strong>
            </li>
            <li>
              <span>{t('aboutUpdateUri')}</span>
              <strong>plugins.onethird.pl/updates/plugins/ot-hello/</strong>
            </li>
            <li>
              <span>{t('aboutGithub')}</span>
              <strong>
                <a href={getConfig().githubUrl ?? 'https://github.com/hikikomorime/ot-hello'}>
                  hikikomorime/ot-hello
                </a>
              </strong>
            </li>
          </ul>
        </article>
        <article className="ot-hello-card ot-hello-span-12">
          <h3>{t('aboutLicenseTitle')}</h3>
          <p className="ot-hello-help" style={{ marginBottom: '0.85rem' }}>
            {t('aboutLicenseHelp')}
          </p>
          {notice ? (
            <p
              className={`ot-hello-banner ot-hello-banner--${notice.kind}`}
              role={notice.kind === 'err' ? 'alert' : 'status'}
            >
              {notice.text}
            </p>
          ) : (
            <p className="ot-hello-banner ot-hello-banner--warn">{licenseCopy(data.license.status, t)}</p>
          )}
          <form onSubmit={onSubmit}>
            <div className="ot-hello-field">
              <label htmlFor="ot-hello-license">{t('aboutLicenseTitle')}</label>
              <input
                id="ot-hello-license"
                className="ot-hello-input"
                value={licenseDraft}
                placeholder={t('aboutLicensePlaceholder')}
                autoComplete="off"
                onChange={(event) => onLicenseDraft(event.target.value)}
              />
            </div>
            <div className="ot-hello-actions">
              <button type="submit" className="ot-hello-btn" disabled={saving || licenseDraft.trim() === ''}>
                {saving ? t('saving') : t('aboutSaveLicense')}
              </button>
              {data.license.status !== 'missing' ? (
                <button type="button" className="ot-hello-btn ot-hello-btn--secondary" disabled={saving} onClick={onClear}>
                  {t('aboutClearLicense')}
                </button>
              ) : null}
            </div>
          </form>
        </article>
      </div>
    </section>
  );
}

export function LoadingState({ t }: { t: Translate }) {
  return (
    <section>
      <h2 className="ot-hello-page-title">{t('loadingTitle')}</h2>
      <p className="ot-hello-lede">{t('loadingBody')}</p>
      <div className="ot-hello-card" role="status" aria-busy="true">
        <div className="ot-hello-skeleton ot-hello-skeleton--lg" />
        <div className="ot-hello-skeleton" />
      </div>
    </section>
  );
}

export function ErrorState({ t, onRetry }: { t: Translate; onRetry: () => void }) {
  return (
    <section>
      <h2 className="ot-hello-page-title">{t('errorTitle')}</h2>
      <p className="ot-hello-lede" role="alert">
        {t('errorBody')}
      </p>
      <button type="button" className="ot-hello-btn" onClick={onRetry}>
        {t('retry')}
      </button>
    </section>
  );
}
