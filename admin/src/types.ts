export {};

type LicenseStatus = 'missing' | 'invalid' | 'saved';

export type HubId = 'dashboard' | 'settings' | 'help' | 'about';

export type GreetingSettings = {
  greeting: string;
  audience: string;
  show_site_info: boolean;
};

export type LicenseState = {
  key: string;
  status: LicenseStatus;
};

export type StatusPayload = {
  ok: boolean;
  version: string;
  message: string;
  settings: GreetingSettings;
  license: LicenseState;
  site: {
    name: string;
    wpVersion: string;
    phpVersion: string;
  };
};

export type GreetingPayload = {
  message: string;
  settings: GreetingSettings;
};

export type PluginConfig = {
  restUrl: string;
  nonce: string;
  version: string;
  locale: string;
  preview: boolean;
  siteName: string;
  wpVersion: string;
  phpVersion: string;
  pluginUrl: string;
  docsUrl: string;
  updateUri: string;
  githubUrl?: string;
  bootstrap?: {
    message: string;
    settings: GreetingSettings;
    license: LicenseState;
  };
  i18n?: Record<string, string>;
};

declare global {
  interface Window {
    otHello?: PluginConfig;
    otHelloWorld?: PluginConfig;
  }
}
