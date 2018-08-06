window.Popper = require('popper.js').default;
try {
    window.$ = window.jQuery = require('jquery');
    require('bootstrap');

    $(document).ready(function() {
        $('[data-toggle="popover"]').popover();
    })
    require('codemirror/lib/codemirror');
    require('summernote/dist/summernote-bs4');
    require('bootstrap-datepicker');
    require('select2/dist/js/select2.full.min');
} catch (e) {}