import Notiflix from 'notiflix';

export default function UseNotiflix(app) {
    // =================== Configuración NOTIFY ===================
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
    });

    // =================== Configuración CONFIRM ===================
    Notiflix.Confirm.init({
        fontFamily: 'Patrick Hand',
        titleColor: '#000',
        messageColor: '#333',
        okButtonBackground: '#22c55e',
        cancelButtonBackground: '#ef4444',
        borderRadius: '8px',
        zindex: 9999,
    });

    // =================== Configuración REPORT ===================
    Notiflix.Report.init({
        fontFamily: 'Patrick Hand',
        borderRadius: '8px',
        plainText: false,
        zindex: 9999,
    });

    // =================== Configuración BLOCK ===================
    Notiflix.Block.init({
        fontFamily: 'Patrick Hand',
        svgSize: '40px',
        messageFontSize: '15px',
        backgroundColor: 'rgba(0,0,0,0.4)',
        zindex: 9999,
    });

    // =================== Proveedores globales ===================
    app.provide('$notify', (mensaje, tipo = 'success') => {
        tipo = tipo === 'danger' ? 'failure' : tipo;
        if (typeof Notiflix.Notify[tipo] === 'function') {
            Notiflix.Notify[tipo](mensaje);
        } else {
            Notiflix.Notify.info(mensaje);
        }
    });

    app.provide('$confirm', Notiflix.Confirm);
    app.provide('$report', Notiflix.Report);
    app.provide('$block', Notiflix.Block);

    app.config.globalProperties.$notify = app._context.provides.$notify;
    app.config.globalProperties.$confirm = Notiflix.Confirm;
    app.config.globalProperties.$report = Notiflix.Report;
    app.config.globalProperties.$block = Notiflix.Block;
}
