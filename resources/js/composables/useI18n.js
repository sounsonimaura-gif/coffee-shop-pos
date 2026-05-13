import { computed, reactive } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

// Lazy-load translation bundles bundled by Vite
const bundles = import.meta.glob('../lang/*.json', { eager: true });

const state = reactive({
    locale: document.documentElement.getAttribute('data-locale') || 'en',
    messages: {},
});

function loadBundle(locale) {
    const key = Object.keys(bundles).find((k) => k.endsWith(`/${locale}.json`));
    if (key && bundles[key].default) {
        state.messages[locale] = bundles[key].default;
    }
}

// Pre-load both bundles
loadBundle('en');
loadBundle('kh');

function translate(key, replacements = {}) {
    const dict = state.messages[state.locale] || {};
    let value = dict[key];
    if (value === undefined) {
        const fallback = state.messages['en'] || {};
        value = fallback[key];
    }
    if (value === undefined) value = key;
    for (const [k, v] of Object.entries(replacements)) {
        value = value.replace(new RegExp(`:${k}`, 'g'), v);
    }
    return value;
}

export function useI18n() {
    const page = usePage();

    const locale = computed({
        get: () => page.props.locale || state.locale,
        set: (newLocale) => setLocale(newLocale),
    });

    return {
        locale,
        availableLocales: computed(() => page.props.available_locales || ['en', 'kh']),
        t: translate,
        setLocale,
    };
}

export function setLocale(newLocale) {
    if (!newLocale || newLocale === state.locale) return;
    state.locale = newLocale;
    document.documentElement.setAttribute('lang', newLocale);
    document.documentElement.setAttribute('data-locale', newLocale);

    // Tell the server to update the session locale and reload the Inertia
    // shared props (translations) WITHOUT a full page refresh.
    router.post(
        '/locale',
        { locale: newLocale },
        {
            preserveScroll: true,
            preserveState: true,
            only: ['locale', 'translations', 'available_locales'],
        }
    );
}

export const i18nPlugin = {
    install(app) {
        app.config.globalProperties.$t = translate;
        app.config.globalProperties.$setLocale = setLocale;
        app.provide('i18n', { t: translate, setLocale });
    },
};
