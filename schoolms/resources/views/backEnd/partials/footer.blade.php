@php
    $setting = generalSetting();
    if(isset($setting->copyright_text)){
    $copyright_text = $setting->copyright_text;
    }else{
    $copyright_text = 'Copyright 2019 All rights reserved by Codethemes';
    }
@endphp
</div>
</div>

@if(config('app.app_sync'))
    <a target="_blank" href="https://1.envato.market/9WVoZ3" class="float_button"> <i class="ti-shopping-cart-full"></i>
        <h3>Purchase InfixEdu</h3>
    </a>
@endif
<div class="has-modal modal fade" id="showDetaildModal">
    <div class="modal-dialog modal-dialog-centered" id="modalSize">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title" id="showDetaildModalTile">@lang('system_settings.new_client_information')</h4>
                <button type="button" class="close icons" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="showDetaildModalBody">

            </div>
        </div>
    </div>
</div>
<!--  Start Modal Area -->
<div class="modal fade invoice-details" id="showDetaildModalInvoice">
    <div class="modal-dialog large-modal modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">@lang('common.add_invoice')</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="showDetaildModalBodyInvoice">
            </div>
        </div>
    </div>
</div>
<!--================Footer Area ================= -->
<footer class="footer-area">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                @if(Auth::check())
                    <p>{!! $copyright_text !!} </p>
                @else
                    <p>{!! $copyright_text !!} </p>
                @endif
            </div>
        </div>
    </div>
</footer>

<!-- ================End Footer Area ================= -->

<script>
    window.jsLang = function(key, replace) {
        let translation = true

        let json_file = $.parseJSON(window._translations[window._locale]['json'])
        translation = json_file[key]
            ? json_file[key]
            : key


        $.each(replace, (value, key) => {
            translation = translation.replace(':' + key, value)
        })

        return translation
    }
</script>

<script src="{{asset('backEnd')}}/vendors/js/jquery-ui.js"></script>
<script src="{{asset('backEnd')}}/vendors/js/jquery.data-tables.js"></script>
<script src="{{asset('backEnd')}}/vendors/js/dataTables.buttons.min.js"></script>
<script src="{{asset('backEnd')}}/vendors/js/buttons.flash.min.js"></script>
<script src="{{asset('backEnd')}}/vendors/js/jszip.min.js"></script>
<script src="{{asset('backEnd')}}/vendors/js/pdfmake.min.js"></script>
<script src="{{asset('backEnd')}}/vendors/js/vfs_fonts.js"></script>
<script src="{{asset('backEnd/js/vfs_fonts.js')}}"></script>
<script src="{{asset('backEnd')}}/vendors/js/buttons.html5.min.js"></script>
<script src="{{asset('backEnd')}}/vendors/js/buttons.print.min.js"></script>
<script src="{{asset('backEnd')}}/vendors/js/dataTables.rowReorder.min.js"></script>
<script src="{{asset('backEnd')}}/vendors/js/dataTables.responsive.min.js"></script>
<script src="{{asset('backEnd')}}/vendors/js/buttons.colVis.min.js"></script>
<script src="{{asset('backEnd')}}/vendors/js/popper.js"></script>
{{--<script src="{{asset('backEnd')}}/vendors/js/bootstrap.min.js">--}}
{{--</script>--}}
<script src="{{asset('backEnd')}}/css/rtl/bootstrap.min.js"></script>
<script src="{{asset('backEnd')}}/vendors/js/nice-select.min.js"></script>
<script src="{{asset('backEnd')}}/vendors/js/jquery.magnific-popup.min.js"></script>
<script src="{{asset('backEnd')}}/vendors/js/fastselect.standalone.min.js"></script>
<script src="{{asset('backEnd')}}/vendors/js/raphael-min.js"></script>
<script src="{{asset('backEnd')}}/vendors/js/morris.min.js"></script>
<script type="text/javascript" src="{{asset('backEnd')}}/vendors/js/toastr.min.js"></script>
<script type="text/javascript" src="{{asset('backEnd')}}/vendors/js/moment.min.js"></script>
<script src="{{ asset('backEnd/vendors/editor/ckeditor/ckeditor.js') }}"></script>
<script src="{{asset('backEnd')}}/vendors/js/bootstrap_datetimepicker.min.js"></script>
<script src="{{asset('backEnd')}}/vendors/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" src="{{asset('backEnd')}}/vendors/js/fullcalendar.min.js"></script>
<script src="{{asset('backEnd/vendors/js/fullcalendar-locale-all.js')}}"></script>
<script type="text/javascript" src="{{asset('backEnd')}}/js/jquery.validate.min.js"></script>
<script src="{{asset('backEnd')}}/vendors/js/select2/select2.min.js"></script>
<script src="{{asset('backEnd')}}/js/main.js"></script>
<script src="{{asset('backEnd')}}/js/lesson_plan.js"></script>
<script src="{{asset('backEnd')}}/js/custom.js"></script>
<script src="{{asset('')}}/js/registration_custom.js"></script>
<script src="{{asset('backEnd')}}/js/developer.js"></script>
<script src="{{asset('backEnd')}}/vendors/js/daterangepicker.min.js"></script>
<script>
    pdfMake.fonts = {
        DejaVuSans: {
            normal: 'DejaVuSans.ttf',
            bold: 'DejaVuSans-Bold.ttf',
            italics: 'DejaVuSans-Oblique.ttf',
            bolditalics: 'DejaVuSans-BoldOblique.ttf'
        }
    };



        ;(function($){
        $.fn.datepicker.dates[_locale] = new Object({
            "days" : {!! json_encode(__('calender.days')) !!},
            "daysShort": {!! json_encode(__('calender.daysShort')) !!},
            "daysMin": {!! json_encode(__('calender.daysMin')) !!},
            "months": {!! json_encode(__('calender.months')) !!},
            "monthsShort": {!! json_encode(__('calender.monthsShort')) !!},
            "today": {!! json_encode(__('calender.today')) !!},
            "clear": {!! json_encode(__('calender.clear')) !!}
        })
    }(jQuery));

</script>
<script src="{{asset('backEnd')}}/vendors/editor/summernote-bs4.js"></script>
<script src="{{url('Modules\Wallet\Resources\assets\js\wallet.js')}}"></script>

<script src="{{asset('backEnd')}}/js/lesson_plan.js"></script>
{{-- <script src="{{asset('backEnd')}}/saas/js1/custom.js"></script> --}}

<script type="text/javascript">
    //$('table').parent().addClass('table-responsive pt-4');
    // for select2 multiple dropdown in send email/Sms in Individual Tab
    $("#selectStaffss").select2();
    $("#checkbox").click(function() {
        if ($("#checkbox").is(':checked')) {
            $("#selectStaffss > option").prop("selected", "selected");
            $("#selectStaffss").trigger("change");
        } else {
            $("#selectStaffss > option").removeAttr("selected");
            $("#selectStaffss").trigger("change");
        }
    });

    // for select2 multiple dropdown in send email/Sms in Class tab
    $("#selectSectionss").select2();
    $("#checkbox_section").click(function() {
        if ($("#checkbox_section").is(':checked')) {
            $("#selectSectionss > option").prop("selected", "selected");
            $("#selectSectionss").trigger("change");
        } else {
            $("#selectSectionss > option").removeAttr("selected");
            $("#selectSectionss").trigger("change");
        }
    });
</script>
<script>
    $('.close_modal').on('click', function() {
        $('.custom_notification').removeClass('open_notification');
    });
    $('.notification_icon').on('click', function() {
        $('.custom_notification').addClass('open_notification');
    });
    $(document).click(function(event) {
        if (!$(event.target).closest(".custom_notification").length) {
            $("body").find(".custom_notification").removeClass("open_notification");
        }
    });

    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : (event.keyCode);
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    }
</script>
<script src="{{asset('backEnd')}}/js/search.js"></script>
{{-- <script src="{{asset('landing/js/toastr.js')}}"></script> --}}

{!! Toastr::message() !!}

{{--@if(request()->route()->getPrefix() == '/chat')--}}
<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ asset('chat/js/custom.js') }}"></script>
{{--@endif--}}

{{-- <script src="{{asset('Modules/Saas/Resources/assets/saas/')}}/js/main.js"></script> --}}
{{-- <script src="{{asset('Modules/Saas/Resources/assets/saas/')}}/js/saas.js"></script> --}}
{{-- <script src="{{asset('Modules/Saas/Resources/assets/saas/')}}/js/developer.js"></script>
<script src="{{asset('Modules/Saas/Resources/assets/saas/')}}/js/search.js"></script> --}}
@yield('script')
@stack('script')
@stack('scripts')
</body>

</html>