// resources/js/Plugins/validacion.js
import { FormValidate } from '../Utils/validacion'

export default {
    install(app) {
        app.provide('FormValidate', FormValidate)
    }
}
