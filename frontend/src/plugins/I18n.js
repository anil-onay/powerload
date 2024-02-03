import {getLocale} from '@/utils/helpers';
import {createI18n} from 'vue-i18n';
import tr_TR from '@/lang/tr_TR';
import en_US from '@/lang/en_US';

const i18n = createI18n({
    legacy: false,
    locale: getLocale(), 
    fallbackLocale: 'tr_TR',
    messages: { tr_TR, en_US },
    warnHtmlMessage: false
})

export default i18n;