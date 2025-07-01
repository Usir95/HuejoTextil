// resources/js/plugins/aggrid-theme.js
import { themeQuartz } from 'ag-grid-community'

export function generarTemaAGGrid(params = {}) {
    const theme = themeQuartz.withParams({
        accentColor: '#E322F2',
        backgroundColor: '#1f2836',
        browserColorScheme: 'dark',
        chromeBackgroundColor: {
            ref: 'foregroundColor',
            mix: 0.07,
            onto: 'backgroundColor',
        },
        fontFamily: {
            googleFont: 'Pixelify Sans',
        },
        foregroundColor: '#FFFFFF',
        headerFontFamily: {
            googleFont: 'Inclusive Sans',
        },
        headerFontSize: 12,
        headerFontWeight: 900,
        headerRowBorder: true,
        iconSize: 12,
        oddRowBackgroundColor: '#1C2C37F0',
        rowBorder: true,
        spacing: 5,
        wrapperBorder: false,
        ...params,
    })

    return theme
}
