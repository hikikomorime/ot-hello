import type { GreetingPayload, GreetingSettings, LicenseState, PluginConfig, StatusPayload } from './types';

const PREVIEW_STORAGE_SETTINGS = 'ot-hello-preview-settings';
const PREVIEW_STORAGE_LICENSE = 'ot-hello-preview-license';

const defaultSettings: GreetingSettings = {
  greeting: 'Hello',
  audience: 'World',
  show_site_info: true,
};

function readPreviewSettings(): GreetingSettings {
  try {
    const raw = window.localStorage.getItem(PREVIEW_STORAGE_SETTINGS);
    if (!raw) {
      return defaultSettings;
    }
    const parsed = JSON.parse(raw) as Partial<GreetingSettings>;
    return {
      greeting: typeof parsed.greeting === 'string' ? parsed.greeting : defaultSettings.greeting,
      audience: typeof parsed.audience === 'string' ? parsed.audience : defaultSettings.audience,
      show_site_info:
        typeof parsed.show_site_info === 'boolean' ? parsed.show_site_info : defaultSettings.show_site_info,
    };
  } catch {
    return defaultSettings;
  }
}

function readPreviewLicense(): LicenseState {
  try {
    const raw = window.localStorage.getItem(PREVIEW_STORAGE_LICENSE);
    if (!raw) {
      return { key: '', status: 'missing' };
    }
    const parsed = JSON.parse(raw) as LicenseState;
    return parsed;
  } catch {
    return { key: '', status: 'missing' };
  }
}

function composeMessage(settings: GreetingSettings): string {
  const greeting = settings.greeting.trim() || 'Hello';
  const audience = settings.audience.trim() || 'World';
  return `${greeting}, ${audience}!`;
}

function delay(ms: number): Promise<void> {
  return new Promise((resolve) => {
    window.setTimeout(resolve, ms);
  });
}

export function getConfig(): PluginConfig {
  const injected = window.otHello ?? window.otHelloWorld;
  const preview = import.meta.env.VITE_PREVIEW === '1' || injected?.preview === true;

  if (injected && !preview) {
    return injected;
  }

  return {
    restUrl: injected?.restUrl ?? '/ot-hello/v1/',
    nonce: injected?.nonce ?? '',
    version: injected?.version ?? '0.1.2',
    locale: injected?.locale ?? (typeof navigator !== 'undefined' && navigator.language.startsWith('pl') ? 'pl_PL' : 'en_US'),
    preview: true,
    siteName: injected?.siteName ?? 'OneThird Demo',
    wpVersion: injected?.wpVersion ?? '6.6',
    phpVersion: injected?.phpVersion ?? '8.3',
    pluginUrl: 'https://onethird.pl',
    docsUrl: 'https://onethird.pl',
    updateUri: 'https://plugins.onethird.pl/updates/plugins/ot-hello/',
    githubUrl: 'https://github.com/hikikomorime/ot-hello',
    bootstrap: injected?.bootstrap,
    i18n: injected?.i18n,
  };
}

async function request<T>(path: string, init?: RequestInit): Promise<T> {
  const config = getConfig();
  const url = `${config.restUrl.replace(/\/?$/, '/')}${path.replace(/^\//, '')}`;
  const response = await fetch(url, {
    ...init,
    headers: {
      'Content-Type': 'application/json',
      'X-WP-Nonce': config.nonce,
      ...(init?.headers ?? {}),
    },
  });

  if (!response.ok) {
    throw new Error(`HTTP ${response.status}`);
  }

  return (await response.json()) as T;
}

export async function fetchStatus(): Promise<StatusPayload> {
  const config = getConfig();
  if (config.preview) {
    await delay(280);
    const settings = readPreviewSettings();
    const license = readPreviewLicense();
    return {
      ok: true,
      version: config.version,
      message: composeMessage(settings),
      settings,
      license,
      site: {
        name: config.siteName,
        wpVersion: config.wpVersion,
        phpVersion: config.phpVersion,
      },
    };
  }

  return request<StatusPayload>('status');
}

export async function saveGreeting(settings: GreetingSettings): Promise<GreetingPayload> {
  const config = getConfig();
  if (config.preview) {
    await delay(360);
    window.localStorage.setItem(PREVIEW_STORAGE_SETTINGS, JSON.stringify(settings));
    return {
      message: composeMessage(settings),
      settings,
    };
  }

  return request<GreetingPayload>('greeting', {
    method: 'POST',
    body: JSON.stringify(settings),
  });
}

export async function saveLicenseKey(key: string, clear = false): Promise<LicenseState> {
  const config = getConfig();
  if (config.preview) {
    await delay(360);
    if (clear) {
      const state: LicenseState = { key: '', status: 'missing' };
      window.localStorage.setItem(PREVIEW_STORAGE_LICENSE, JSON.stringify(state));
      return state;
    }
    const trimmed = key.trim().toUpperCase();
    if (!trimmed) {
      return readPreviewLicense();
    }
    const valid = /^(?:OT-HELLO|OT-HW)-[A-Z0-9]{4}(?:-[A-Z0-9]{4}){3}$/.test(trimmed);
    const state: LicenseState = {
      key: trimmed.slice(0, 6) + '••••' + trimmed.slice(-4),
      status: valid ? 'saved' : 'invalid',
    };
    window.localStorage.setItem(PREVIEW_STORAGE_LICENSE, JSON.stringify(state));
    return state;
  }

  return request<LicenseState>('license', {
    method: 'POST',
    body: JSON.stringify({ key, clear }),
  });
}
