import axios from 'axios';

export const API_BASE = import.meta.env.VITE_BACKEND_URL || 'http://app.test';

export const api = axios.create({
  baseURL: API_BASE,
  withCredentials: true,
  xsrfCookieName: 'XSRF-TOKEN',
  xsrfHeaderName: 'X-XSRF-TOKEN',
  headers: {
    Accept: 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  },
});

export async function ensureCsrf() {
  // létrehozza XSRF-TOKEN + laravel_session sütiket app.test domainre
  return api.get('/sanctum/csrf-cookie');
}

function getXsrfToken() {
  const name = 'XSRF-TOKEN=';
  const all = document.cookie ? document.cookie.split('; ') : [];
  for (const c of all) if (c.startsWith(name)) return decodeURIComponent(c.slice(name.length));
  return '';
}

export async function sanctumLogin(email, password, remember = false) {
  await ensureCsrf();
  const token = getXsrfToken();
  return api.post(
    '/spa/login',
    { email, password, remember },
    { headers: token ? { 'X-XSRF-TOKEN': token } : {} },
  );
}

export async function sanctumLogout() {
  await ensureCsrf();
  const token = getXsrfToken();
  return api.post(
    '/spa/logout',
    {},
    { headers: token ? { 'X-XSRF-TOKEN': token } : {} },
  );
}

// ---- 419 interceptor (egyszeri retry), de NEM login/logout-ra! ----
api.interceptors.response.use(
  (r) => r,
  async (error) => {
    const status = error?.response?.status;
    const config = error?.config;

    // nincs config? nem tudunk retry-olni
    if (!config) return Promise.reject(error);

    // már retry-oltunk? lépjünk ki
    if (config.__isRetry) return Promise.reject(error);

    // ne retry-oljunk SPA auth endpointra (különben loop lesz)
    const url = (config.url || '').toString();
    const isAuthEndpoint = url.includes('/spa/login') || url.includes('/spa/logout');

    if (status === 419 && !isAuthEndpoint) {
      try {
        config.__isRetry = true;
        await ensureCsrf();
        const token = getXsrfToken();
        config.headers = { ...(config.headers || {}), ...(token ? { 'X-XSRF-TOKEN': token } : {}) };
        return await api.request(config);
      } catch (e) {
        return Promise.reject(e);
      }
    }

    return Promise.reject(error);
  },
);
