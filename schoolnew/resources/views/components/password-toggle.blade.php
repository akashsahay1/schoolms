<style>
.password-wrapper {
    position: relative;
}
.password-wrapper .password-toggle {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #999;
    font-size: 14px;
    z-index: 5;
    background: none;
    border: none;
    padding: 0;
    line-height: 1;
}
.password-wrapper .password-toggle:hover {
    color: #333;
}
</style>
<script>
jQuery(document).ready(function() {
    jQuery('input[type="password"]').each(function() {
        var input = jQuery(this);
        // Skip if already wrapped or inside .show-hide parent (login page)
        if (input.closest('.password-wrapper').length || input.siblings('.show-hide').length) {
            return;
        }
        input.wrap('<div class="password-wrapper"></div>');
        input.after('<span class="password-toggle"><i class="fa fa-eye"></i></span>');
    });

    jQuery(document).on('click', '.password-toggle', function() {
        var wrapper = jQuery(this).closest('.password-wrapper');
        var input = wrapper.find('input');
        var icon = jQuery(this).find('i');
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
});
</script>
