<script src="{{ asset('frontend-website/assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('frontend-website/assets/js/popper.min.js') }}"></script>
<script src="{{ asset('frontend-website/assets/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('vendor/intlTelInput/js/intlTelInput-jquery.min.js') }}"></script>
<script src="{{ asset('vendor/intlTelInput/js/intlTelInput.min.js') }}"></script>
<script src="{{ asset('frontend-website/assets/js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('frontend-website/assets/js/toastr.min.js') }}"></script>
<script src="{{ asset('frontend-website/assets/js/owl.carousel.min.js') }}"></script>
<script src="{{ asset('frontend-website/assets/js/owl.carousel.js') }}"></script>

<script>
    $(document).ready(function() {
        /*
        $("form").on("submit", function () {
            $(this).find(":submit").prop("disabled", true);
        });
        */
        $("#signupModalId").on("click", function(e) {
            $('#signinModal').modal('hide');
        });

        $("#forgotmodal-link").on("click", function(e) {
            $('#signinModal').modal('hide');

            $('#forgotmodal').modal('show');
        });

        $('#signin-link').click(function(e) {
            e.preventDefault();

            $('#signupModal').modal('hide');

            $('#signinModal').modal('show');
        });

        //PHONE 
        var input = document.querySelector("#phone");
        errorMsg = document.querySelector("#error-msg");
        validMsg = document.querySelector("#valid-msg");

        if (input) {
            var iti = window.intlTelInput(input, {
                hiddenInput: "contact_number",
                separateDialCode: true,
                utilsScript: "{{ asset('vendor/intlTelInput/js/utils.js') }}" // just for formatting/placeholders etc
            });

            input.addEventListener("countrychange", function() {
                validate();
            });

            // // here, the index maps to the error code returned from getValidationError - see readme
            var errorMap = ["Invalid number", "Invalid country code", "Too short", "Too long",
                "Invalid number"
            ];
            //
            // // initialise plugin
            const phone = $('#phone');
            const err = $('#error-msg');
            const succ = $('#valid-msg');
            var reset = function() {
                err.addClass('d-none');
                succ.addClass('d-none');
                validate();
            };

            // on blur: validate
            $(document).on('blur, keyup', '#phone', function() {
                reset();
                var val = $(this).val();
                if (val.match(/[^0-9\.\+.\s.]/g)) {
                    $(this).val(val.replace(/[^0-9\.\+.\s.]/g, ''));
                }
                if (val === '') {
                    $('[type="submit"]').removeClass('disabled').prop('disabled', false);
                }
            });

            // on keyup / change flag: reset
            input.addEventListener('change', reset);
            input.addEventListener('keyup', reset);

            var errorCode = '';

            function validate() {
                if (input.value.trim()) {
                    if (iti.isValidNumber()) {
                        succ.removeClass('d-none');
                        err.html('');
                        err.addClass('d-none');
                        $('[type="submit"]').removeClass('disabled').prop('disabled', false);
                    } else {
                        errorCode = iti.getValidationError();
                        err.html(errorMap[errorCode]);
                        err.removeClass('d-none');
                        phone.closest('.form-group').addClass('has-danger');
                        $('[type="submit"]').addClass('disabled').prop('disabled', true);
                    }
                }
            }
        }
    });



    //LOADER
    window.onload = function() {
        document.getElementById("loader").style.display = "none";
    };

    // Scroll Top
    let mybutton = document.getElementById("myBtn");

    window.onscroll = function() {
        scrollFunction()
    };

    function scrollFunction() {
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            mybutton.style.display = "block";
        } else {
            mybutton.style.display = "none";
        }
    }

    function topFunction() {
        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0;
    }

    // TESTIMONIAL 
    $('.multiple-card-slider .carousel').each(function() {
        var currentCarouselId = '#' + $(this).attr('id');
        const multipleItemCarousel = document.querySelector(currentCarouselId);

        if (window.matchMedia("(min-width:576px)").matches) {
            const carousel = new bootstrap.Carousel(multipleItemCarousel, {
                interval: false,
                wrap: false
            })
            var carouselWidth = $(currentCarouselId + ' .carousel-inner')[0].scrollWidth;
            var cardWidth = $(currentCarouselId + ' .carousel-item').width();
            var scrollPosition = 0;

            function toggleCarouselControls() {
                var numItems = $(currentCarouselId + ' .carousel-item').length;

                if (window.matchMedia("(min-width:992px)").matches) {
                    if (numItems <= 1) {
                        $('#onecard-flex').css({
                            'display': 'flex',
                            'justify-content': 'center'
                        });
                    }
                }

                if (numItems <= 3) {
                    $(currentCarouselId + ' .carousel-control-prev, ' + currentCarouselId +
                        ' .carousel-control-next').hide();
                } else {
                    $(currentCarouselId + ' .carousel-control-prev, ' + currentCarouselId +
                        ' .carousel-control-next').show();
                }

                $(window).on('resize', function() {
                    var currentCarouselId = '#' + $('.multiple-card-slider .carousel').attr('id');
                    var numItems = $(currentCarouselId + ' .carousel-item').length;

                    if (window.matchMedia("(min-width:992px)").matches) {
                        if (numItems <= 3) {
                            $(currentCarouselId + ' .carousel-control-prev, ' + currentCarouselId +
                                ' .carousel-control-next').hide();
                        } else {
                            $(currentCarouselId + ' .carousel-control-prev, ' + currentCarouselId +
                                ' .carousel-control-next').show();
                        }
                    } else {
                        $(currentCarouselId + ' .carousel-control-prev, ' + currentCarouselId +
                            ' .carousel-control-next').show();
                    }
                }).trigger('resize');

            }

            toggleCarouselControls();

            $(currentCarouselId + ' .carousel-control-next').on('click', function() {
                if (scrollPosition < (carouselWidth - (cardWidth * 3))) {
                    scrollPosition = scrollPosition + cardWidth;
                    $(currentCarouselId + ' .carousel-inner').animate({
                        scrollLeft: scrollPosition
                    }, 600);
                    toggleCarouselControls();
                }
            });

            $(currentCarouselId + ' .carousel-control-prev').on('click', function() {
                if (scrollPosition > 0) {
                    scrollPosition = scrollPosition - cardWidth;
                    $(currentCarouselId + ' .carousel-inner').animate({
                        scrollLeft: scrollPosition
                    }, 600);
                    toggleCarouselControls();
                }
            });
        } else {
            $(multipleItemCarousel).addClass('slide');
        }
    });


    // START OWL CAROUSEL SECTION

    $("#client-slider").owlCarousel({
        responsive: {
            0: {
                items: 1
            },
            600: {
                items: 1
            },                
            980: {
                items: 2
            },
            1199: {
                items: 2
            },
        },
        dots: false,
        navigation: false,
        pagination: true,
        autoplay: true,
        autoplayTimeout: 5000,
        autoplayHoverPause: true,
        rtl: $("html").attr("dir") === "rtl" ? true : false
    });

    // END OWL CAROUSEL SECTION

    // START PASSWORD

    const togglePasswords = document.querySelectorAll('.togglePassword');
    const passwords = document.querySelectorAll('.password');

    togglePasswords.forEach((toggle, index) => {
        toggle.addEventListener('click', function() {
            const type = passwords[index].getAttribute('type') === 'password' ? 'text' : 'password';
            passwords[index].setAttribute('type', type);

            this.classList.toggle('fa-eye-slash');
            this.classList.toggle('fa-eye');
        });
    });

    // END PASSWORD

    $("#signupForm").validate({
        rules: {
            name: {
                required: true,
            },
            username: {
                required: true,
            },
            email: {
                required: true,
                email: true,
            },
            contact_number: "required",
            password: "required",
            checkbox: {
                required: true
            },
        },
        messages: {
            name: "This field is required",
            username: "This field is required",
            email: {
                required: "This field is required",
                email: "Please enter a valid Email Address.",
                remote: "Email already exists"

            },
            contact_number: "This field is required",
            password: "This field is required",
            checkbox: "Please agree to the Terms of Service and Privacy Policy",

        },
        highlight: function(element) {
            var elementName = $(element).attr("name");
            if (elementName !== "contact_number" && elementName !== "password") {
                $(element).addClass('is-invalid').removeClass('is-valid');
            }
            if ($(element).attr("name") === "password") {
                $(element).siblings('.togglePassword').css("margin-top", "-12px");
            }    
        },
        unhighlight: function(element) {
            var elementName = $(element).attr("name");
            if (elementName !== "contact_number" && elementName !== "password") {
                $(element).addClass('is-valid').removeClass('is-invalid');
            }
            if ($(element).attr("name") === "password") {
                $(element).siblings('.togglePassword').css("margin-top", "");
            }
        },
        errorPlacement: function(error, element) {
            if (element.attr("name") == "checkbox") {
                error.insertAfter(element.next()).wrap("<div class='error-message'></div>");
            } else {
                error.insertAfter(element);
            }
        },
    });

    $("#signinForm").validate({
        errorPlacement: function(error, element) {
            // if (element.attr("name") == "checkbox") {
            if (jQuery.inArray(element.attr("name"), ['checkbox','remember']) !== -1) {
                error.insertAfter(element.next());
            } else {
                error.insertAfter(element);
            }
            error.wrap("<span class='error-message'></span>");
        },
        rules: {
            email: {
                required: true,
                email: true,
            },
            password: "required",
            remember: {
                required: true
            },
            checkbox: {
                required: true
            }
        },
        messages: {
            email: {
                required: "This field is required",
                email: "Please enter a valid Email Address.",
            },
            password: "This field is required",
            checkbox: "Please agree to the Terms of Service and Privacy Policy",
        },
        highlight: function(element, errorClass, validClass) {
        if ($(element).attr("name") === "password") {
            $(element).siblings('.togglePassword').css("margin-top", "-12px");
            }
        },
        unhighlight: function(element, errorClass, validClass) {
            if ($(element).attr("name") === "password") {
                $(element).siblings('.togglePassword').css("margin-top", "");
            }
        }
    });

    @if (Session::has('message'))
        var type = "{{ Session::get('alert-type', 'info') }}"
        switch (type) {
            case 'info':

                toastr.options.timeOut = 10000;
                toastr.info("{{ Session::get('message') }}");
                break;
            case 'success':

                toastr.options.timeOut = 10000;
                toastr.success("{{ Session::get('message') }}");
                break;
            case 'warning':

                toastr.options.timeOut = 10000;
                toastr.warning("{{ Session::get('message') }}");
                break;
            case 'error':

                toastr.options.timeOut = 10000;
                toastr.error("{{ Session::get('message') }}");
                break;
        }
    @endif

    @if (Session::has('user_type'))
        var userType = "{{ Session::get('user_type') }}";
        var message = '';

        switch (userType) {
            case 'admin':
                message = 'These credentials do not match our records.';
                toastr.error(message);
                break;
            case 'delivery_man':
                message = 'These credentials do not match our records.';
                toastr.error(message);
                break;
            case 'authcheck':
                message = 'These credentials do not match our records.';
                toastr.error(message);
                break;
            case 'client':
                message = 'Welcome, Client!';
                toastr.success(message);
                break;
            default:
                break;
        }
    @endif
    @if ($errors->any())
        @foreach ($errors->all() as $error)
            toastr.options.timeOut = 5000;
            toastr.error("{{ $error }}");
        @endforeach
    @endif
</script>
