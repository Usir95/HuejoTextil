import tippy from 'tippy.js';
import 'tippy.js/dist/tippy.css';

export default function UseTippy(app) {
    app.directive('tooltip', {
        mounted(el, binding) {
            let options = {};

            if (typeof binding.value === 'object' && binding.value !== null) {
                options = {
                    content: binding.value.content || '',
                    placement: binding.value.placement || 'top',
                    animation: binding.value.animation || 'shift-away',
                    arrow: binding.value.arrow !== false,
                    delay: binding.value.delay || [100, 100],
                };
            } else {
                options = {
                    content: binding.value,
                    placement: 'top',
                    animation: 'shift-away',
                    arrow: true,
                    delay: [100, 100],
                };
            }

            el._tippy = tippy(el, options);
        },

        updated(el, binding) {
            const content = typeof binding.value === 'object'
                ? binding.value.content
                : binding.value;

            if (el._tippy) {
                el._tippy.setContent(content || '');
            }
        },

        unmounted(el) {
            if (el._tippy) {
                el._tippy.destroy();
            }
        },
    });
}
