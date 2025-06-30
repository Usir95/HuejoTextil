import vueFilePond from 'vue-filepond';
import 'filepond/dist/filepond.min.css';

// Plugins opcionales
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';
import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css';

// Registrar plugins
const FilePond = vueFilePond(
    FilePondPluginFileValidateType,
    FilePondPluginImagePreview
);

export default function UseFilePond(app) {
    app.component('FilePond', FilePond);
}
