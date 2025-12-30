/*!
 * Bootstrap v3.4.1 (https://getbootstrap.com/)
 */

if (typeof jQuery === 'undefined') {
    throw new Error('Bootstrap\'s JavaScript requires jQuery')
}

+function ($) {
    'use strict';
    var version = $.fn.jquery.split(' ')[0].split('.')
    if ((version[0] < 2 && version[1] < 9) || (version[0] === 1 && version[1] === 9 && version[2] < 1) || (version[0] > 3)) {
        throw new Error('Bootstrap\'s JavaScript requires jQuery version 1.9.1 or higher, but lower than version 4')
    }
}(jQuery);

+function ($) {
    'use strict';
    // TAB CLASS DEFINITION
    var Tab = function (element)
    {
        // jscs:disable requireDollarBeforejQueryAssignment
        this.element = $(element)
        // jscs:enable requireDollarBeforejQueryAssignment
    }

    Tab.VERSION = '3.4.1'

    Tab.TRANSITION_DURATION = 150

    Tab.prototype.show = function () {
        var $this    = this.element
        var $ul      = $this.closest('ul:not(.dropdown-menu)')
        var selector = $this.data('target')

        if (!selector) {
            selector = $this.attr('href')
            selector = selector && selector.replace(/.*(?=#[^\s]*$)/, '') // strip for ie7
        }

        if ($this.parent('li').hasClass('active')) return

        var $previous = $ul.find('.active:last a')
        var hideEvent = $.Event('hide.bs.tab', {
            relatedTarget: $this[0]
        })
        var showEvent = $.Event('show.bs.tab', {
            relatedTarget: $previous[0]
        })

        $previous.trigger(hideEvent)
        $this.trigger(showEvent)

        if (showEvent.isDefaultPrevented() || hideEvent.isDefaultPrevented()) return

        var $target = $(document).find(selector)

        this.activate($this.closest('li'), $ul)
        this.activate($target, $target.parent(), function () {
            $previous.trigger({
                type: 'hidden.bs.tab',
                relatedTarget: $this[0]
            })
            $this.trigger({
                type: 'shown.bs.tab',
                relatedTarget: $previous[0]
            })
        })
    }

    Tab.prototype.activate = function (element, container, callback) {
        var $active    = container.find('> .active')
        var transition = callback
            && $.support.transition
            && ($active.length && $active.hasClass('fade') || !!container.find('> .fade').length)

        function next() {
            $active
                .removeClass('active')
                .find('> .dropdown-menu > .active')
                .removeClass('active')
                .end()
                .find('[data-toggle="tab"]')
                .attr('aria-expanded', false)

            element
                .addClass('active')
                .find('[data-toggle="tab"]')
                .attr('aria-expanded', true)

            if (transition) {
                element[0].offsetWidth // reflow for transition
                element.addClass('in')
            } else {
                element.removeClass('fade')
            }

            if (element.parent('.dropdown-menu').length) {
                element
                    .closest('li.dropdown')
                    .addClass('active')
                    .end()
                    .find('[data-toggle="tab"]')
                    .attr('aria-expanded', true)
            }

            callback && callback()
        }

        $active.length && transition ?
            $active
                .one('bsTransitionEnd', next)
                .emulateTransitionEnd(Tab.TRANSITION_DURATION) :
            next()

        $active.removeClass('in')
    }

    // TAB PLUGIN DEFINITION
    function Plugin(option) {
        return this.each(function () {
            var $this = $(this)
            var data  = $this.data('bs.tab')

            if (!data) $this.data('bs.tab', (data = new Tab(this)))
            if (typeof option == 'string') data[option]()
        })
    }

    var old = $.fn.tab

    $.fn.tab             = Plugin
    $.fn.tab.Constructor = Tab

    // TAB NO CONFLICT
    $.fn.tab.noConflict = function () {
        $.fn.tab = old
        return this
    }

    // TAB DATA-API
    var clickHandler = function (e) {
        e.preventDefault()
        Plugin.call($(this), 'show')
    }

    $(document)
        .on('click.bs.tab.data-api', '[data-toggle="tab"]', clickHandler)
        .on('click.bs.tab.data-api', '[data-toggle="pill"]', clickHandler)
}(jQuery);


window.addEventListener('DOMContentLoaded', function () {

    let supportButtons = document.getElementsByClassName('mo_otp_sliding_support');
    let supportForms = document.getElementsByClassName('mo_otp_sliding_support_form');
    for (let i = 0; i < supportButtons.length; i++) {
        supportButtons[i].addEventListener("click", function (e) {
            if (supportForms[0].style.right != "0px") {
                supportForms[0].style.right = "0px";
            } else {
                supportForms[0].style.right = "-360px";
            }
        });
    }
}
);

function nospaces(t){
    if(t.value.match(/\s/g)){
        alert("Please enter the country codes without spacing");
        t.value=t.value.replace(/\s/g,'');
    }
}

var clock=1;
var no_of_entry="10";

jQuery(document).ready(function() {
    let currentTab = window.location.hash.substring(1) || jQuery(".mo_otp_tab-active").attr("id")?.replace("mo_", "");
    
    if (currentTab) {
        mo_show_tab(currentTab);
    }
});

function mo_show_tab(tab_id) {
    jQuery(".mini_otp_tab").removeClass("mo_otp_tab-active").css({
        "background": "none",
        "color": "white"
    });

    jQuery(".mo_otp_tab").hide();
    jQuery("#" + tab_id).show();
    
    jQuery("#mo_" + tab_id).addClass("mo_otp_tab-active").css({
        "background": "white",
        "color": "black",
        "font-weight": "bold",
    });

    window.location.hash = tab_id;
}

jQuery(document).ready(function () {
    if(jQuery('#reg_restriction_for_email').prop('checked'))
    {
        jQuery('.white_or_black').prop('disabled',false);
        jQuery('.mo_otp_allowed_email_domains').prop('disabled',false);

    }

    jQuery('#reg_restriction_for_email').click(function () {

        if(jQuery('#reg_restriction_for_email').prop('checked'))
        {
            jQuery('.white_or_black').prop('disabled',false);
            jQuery('.mo_otp_allowed_email_domains').prop('disabled',false);
        }
        else{
            jQuery('.white_or_black').prop('disabled',true);
            jQuery('.mo_otp_allowed_email_domains').prop('disabled',true);
        }
    });
});

var counts = [0, 0, 0, 0];
function toggleFAQ(index, id) {
    counts[index]++;
    jQuery(id).toggle(counts[index] % 2 === 1);
}

function info2() { toggleFAQ(0, '#faqa1'); }
function info3() { toggleFAQ(1, '#faqa2'); }
function info4() { toggleFAQ(2, '#faqb1'); }
function info5() { toggleFAQ(3, '#faqb2'); }

//OTP transaction tab
jQuery(document).ready(function (){
    next_or_prev_page('next');
});

function list_of_entry(){
    no_of_entry=jQuery("#select_number").val();
    next_or_prev_page('on');
}
function sort(button){
    var order ="";
    if(clock)
    {
        clock = 0;
        order = 'up';
    }
    else
    {
        clock = 1;
        order = 'down';
    }
    next_or_prev_page(button,order);
}

function search()
{
    var value="";
    value=jQuery("#search_text").val().toLowerCase();
    jQuery("#myTable tbody tr").filter(function() {
        jQuery(this).toggle(jQuery(this).text().toLowerCase().indexOf(value) > -1)
    });
}

function back_reg()
{
    jQuery('#otp_cancel_form').submit();
}

jQuery('#back_btn').click(function () {
    jQuery('#mo_otp_cancel_form').submit();
});

function submit_form() {
    jQuery('#resend_otp_form').submit();
}

jQuery(document).ready(function () {
    if (document.getElementById("otp_during_registration").checked === false){
        jQuery('.otp_reg_dropdown').prop('disabled',true);
        jQuery('.mo_resend_otp_dropdown').prop('disabled',true);
    }
    if(jQuery('.otp_during_registration').prop('checked'))
    {
        jQuery('.login_otp_type').prop('disabled',false);
        jQuery('.otp_reg_dropdown').prop('disabled',false);
        jQuery('.mo_resend_otp_dropdown').prop('disabled',false);
    }

    if(jQuery('.otp_during_registration').prop('disabled'))
    {
        jQuery('.login_otp_type').prop('disabled',true);
        jQuery('.otp_reg_dropdown').prop('disabled',true);
        jQuery('.mo_resend_otp_dropdown').prop('disabled',true);
    }

    jQuery('#otp_during_registration').click(function () {
        if(jQuery('.otp_during_registration').prop('checked'))
        {
            jQuery('.login_otp_type').prop('disabled',false);
            jQuery('.otp_reg_dropdown').prop('disabled',false);
            jQuery('.mo_resend_otp_dropdown').prop('disabled',false);
        }
         else{
            jQuery('.login_otp_type').prop('disabled',true);
            jQuery('.otp_reg_dropdown').prop('disabled',true);
            jQuery('.mo_resend_otp_dropdown').prop('disabled',true);
        }
    });
});

function remove()
{
    jQuery("#remove_acc").submit();
}

function remove_new_form(){
    var countnoForms= jQuery('#form_count').val();
    if(countnoForms!=='0')
    {
        jQuery(".rs_forms:last").remove();
        countnoForms--;
        jQuery('#form_count').attr('value',countnoForms);
    }

}

function displayFileName() {
    var fileInput = document.getElementById('fileInput');
    var file = fileInput.files[0];

    if (file && file.name.endsWith('.json')) {
        document.getElementById('fileName').textContent = file.name; 
    } else {
        document.getElementById('fileName').textContent = "Invalid file. Please select a .json file.";
    }
}
