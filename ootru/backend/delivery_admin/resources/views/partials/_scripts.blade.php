<!-- Backend Bundle JavaScript -->
<script src="{{ asset('js/backend-bundle.min.js') }}"></script>

<script src="{{ asset('js/raphael-min.js') }}"></script>

<script src="{{ asset('js/morris.js') }}"></script>
<script src="{{ asset('vendor/tinymce/js/tinymce/tinymce.min.js') }}"></script>
<script src="{{ asset('vendor/confirmJS/jquery-confirm.min.js') }}"></script>
<script src="{{ asset('js/jquery.validate.min.js') }}"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"></script>

<script>
    // Text Editor code
    if (typeof(tinyMCE) != "undefined") {
        // tinymceEditor()
        function tinymceEditor(target, button, height = 200) {
            var rtl = $("html[lang=ar]").attr('dir');
            tinymce.init({
                selector: target || '.textarea',
                directionality: rtl,
                height: height,
                plugins: ['advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview', 'anchor',
                    'searchreplace', 'visualblocks', 'code', 'fullscreen', 'insertdatetime', 'media',
                    'table', 'help', 'wordcount'
                ],
                toolbar: 'undo redo | blocks | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
                content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }',
                automatic_uploads: false,
                /*file_picker_types: 'image',
                file_picker_callback: function(cb, value, meta) {
                   var input = document.createElement('input');
                   input.setAttribute('type', 'file');
                   input.setAttribute('accept', 'image/*');

                   input.onchange = function() {
                      var file = this.files[0];

                      var reader = new FileReader();
                      reader.onload = function() {
                         var id = 'blobid' + (new Date()).getTime();
                         var blobCache = tinymce.activeEditor.editorUpload.blobCache;
                         var base64 = reader.result.split(',')[1];
                         var blobInfo = blobCache.create(id, file, base64);
                         blobCache.add(blobInfo);

                         cb(blobInfo.blobUri(), { title: file.name });
                      };
                      reader.readAsDataURL(file);
                   };
                   input.click();
                }*/
            });
        }
    }

    function showCheckLimitData(id) {
        var checkbox = $('#' + id).is(":checked")
        if (checkbox == true) {
            $('.' + id).removeClass('d-none')
        } else {
            $('.' + id).addClass('d-none')

        }
    }
</script>
<script>
    function formValidation(formId, rules, messages) {
        $(formId).validate({
            rules: rules,
            messages: messages,
            errorClass: "help-block error",
            highlight: function(element) {
                $(element).closest(".form-group.row").addClass("has-error");
            },
            unhighlight: function(element) {
                $(element).closest(".form-group.row").removeClass("has-error");
            },
            errorPlacement: function(error, element) {
                if (element.hasClass('select2js')) {
                    error.insertAfter(element.next('.select2-container'));
                } else {
                    error.insertAfter(element);
                }
            }
        });
        $('.select2js').on('change', function() {
            $(this).valid();
        });
    }
</script>
@if (isset($assets) && in_array('location', $assets))
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAP_API_KEY') }}&libraries=places,marker" defer>
    </script>
@endif

@yield('bottom_script')

<!-- Masonary Gallery Javascript -->
<script src="{{ asset('js/masonry.pkgd.min.js') }}"></script>
<script src="{{ asset('js/imagesloaded.pkgd.min.js') }}"></script>

<!-- Vectoe Map JavaScript -->
<script src="{{ asset('js/vector-map-custom.js') }}"></script>

<!-- Chart Custom JavaScript -->
<script src="{{ asset('js/customizer.js') }}"></script>

<!-- Chart Custom JavaScript -->
<script src="{{ asset('js/chart-custom.js') }}"></script>

<!-- slider JavaScript -->
<script src="{{ asset('js/slider.js') }}"></script>

<!-- Emoji picker -->
<script type="module" src="{{ asset('vendor/emoji-picker-element/index.js') }}"></script>
<script type="module" src="{{ asset('vendor/magnific-popup/js/jquery.magnific-popup.min.js') }}"></script>

@if (isset($assets) && in_array('datatable', $assets))
    <!-- <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script> -->
    <!-- <script src="{{ asset('vendor/datatables/js/dataTables.bootstrap4.min.js') }}"></script> -->
    <!-- <script src="{{ asset('vendor/datatables/js/dataTables.buttons.min.js') }}"></script> -->
    <!-- <script src="{{ asset('vendor/datatables/js/buttons.bootstrap4.min.js') }}"></script> -->
    <script src="{{ asset('vendor/datatables/buttons.server-side.js') }}"></script>
    <!-- <script src="{{ asset('vendor/datatables/js/dataTables.select.min.js') }}"></script> -->
@endif

<!-- app JavaScript -->
@if ((isset($assets) && in_array('phone', $assets)) || in_array('contact_nbr', $assets))
    <script src="{{ asset('vendor/intlTelInput/js/intlTelInput-jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/intlTelInput/js/intlTelInput.min.js') }}"></script>
@endif

<script src="{{ asset('js/app.js') }}" defer></script>
@include('helper.app_message')


<script src="{{ asset('js/sweetalert2.js') }}"></script>

<!-- dyanamic color -->

<script>
    $(document).ready(function() {
        var root = document.documentElement;
        var siteColor = getComputedStyle(root).getPropertyValue('--site-color').trim();

        var hoverColor = lightenColor(siteColor, 20);
        root.style.setProperty('--site-hover-color', hoverColor);

        function lightenColor(color, percent) {
            var num = parseInt(color.slice(1), 16),
                amt = Math.round(2.55 * percent),
                R = (num >> 16) + amt,
                G = (num >> 8 & 0x00FF) + amt,
                B = (num & 0x0000FF) + amt;

            return `#${(0x1000000 + (R<255?R<1?0:R:255)*0x10000 + (G<255?G<1?0:G:255)*0x100 + (B<255?B<1?0:B:255)).toString(16).slice(1)}`;
        }
    });
</script>
