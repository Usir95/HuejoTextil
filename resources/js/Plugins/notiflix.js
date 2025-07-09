import Notiflix from 'notiflix';

export default function UseNotiflix(app) {
    const getTheme = (modo = 'auto') => {
        if (modo === 'dark') return true;
        if (modo === 'light') return false;
        return document.documentElement.classList.contains('dark');
    };

    const cssVar = (name, fallback = '') =>
        getComputedStyle(document.documentElement).getPropertyValue(name)?.trim() || fallback;

    // =================== NOTIFY (config inicial básica) ===================
    const isDarkInit = getTheme();
    Notiflix.Notify.init({
        fontFamily: 'Patrick Hand',
        width: '280px',
        position: 'right-top',
        distance: '15px',
        timeout: 2500,
        fontSize: '14px',
        clickToClose: true,
        pauseOnHover: true,
        showOnlyTheLastOne: true,
        useIcon: true,
        cssAnimationStyle: 'fade',
        zindex: 9999,
        backgroundColor: isDarkInit ? cssVar('--color-background', '#1f2937') : '#fff',
    });

    // =================== GLOBAL PROVIDERS ===================
    app.provide('$notify', (mensaje, tipo = 'success', modo = 'auto') => {
        const dark = getTheme(modo);
        Notiflix.Notify.init({
            backgroundColor: dark ? cssVar('--color-background', '#1f2937') : '#fff',
        });

        tipo = tipo === 'danger' ? 'failure' : tipo;
        if (typeof Notiflix.Notify[tipo] === 'function') {
            Notiflix.Notify[tipo](mensaje);
        } else {
            Notiflix.Notify.info(mensaje);
        }
    });

    app.provide('$confirm', (
        titulo,
        mensaje,
        textoOk = 'Aceptar',
        textoCancel = 'Cancelar',
        onOk = () => {},
        onCancel = null,
        modo = 'auto'
    ) => {
        const dark = getTheme(modo);

        Notiflix.Confirm.init({
            fontFamily: 'Patrick Hand',
            titleColor: dark ? '#fff' : '#000',
            messageColor: dark ? '#e5e5e5' : '#333',
            backgroundColor: dark ? "#222831" : '#fff',

            okButtonBackground: cssVar('--color-primary', '#3b82f6'),
            cancelButtonBackground: dark ? '#374151' : '#d1d5db',
            okButtonColor: '#fff',
            cancelButtonColor: dark ? '#eee' : '#333',

            borderRadius: '16px',
            zindex: 9999,
            buttonsFontSize: '15px',
            titleFontSize: '17px',
        });

        Notiflix.Confirm.show(titulo, mensaje, textoOk, textoCancel, onOk, onCancel);
    });

    app.provide('$report', (tipo, titulo, mensaje, modo = 'auto') => {
        const dark = getTheme(modo);
        Notiflix.Report.init({
            fontFamily: 'Patrick Hand',
            backgroundColor: dark ? cssVar('--color-background', '#1f2937') : '#fff',
            titleColor: dark ? '#fff' : '#000',
            messageColor: dark ? '#e5e5e5' : '#333',
            borderRadius: '8px',
            zindex: 9999,
        });

        Notiflix.Report[`${tipo}`](titulo, mensaje, 'Cerrar');
    });

    app.provide('$block', Notiflix.Block);

    app.config.globalProperties.$notify = app._context.provides.$notify;
    app.config.globalProperties.$confirm = app._context.provides.$confirm;
    app.config.globalProperties.$report = app._context.provides.$report;
    app.config.globalProperties.$block = Notiflix.Block;
}
