// resources/js/Utils/validacion.js
export function FormValidate(refContenedor) {
    const inputs = refContenedor?.value?.querySelectorAll('[data-md-input="true"]') || []
    let valid = true

    inputs.forEach((el) => {
        const vueInstance = el.__vueParentComponent?.exposed
        if (vueInstance?.validate && typeof vueInstance.validate === 'function') {
        const resultado = vueInstance.validate()
        if (!resultado) valid = false
        }
    })

    return valid
}
