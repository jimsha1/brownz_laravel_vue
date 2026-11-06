
// -----------------------------------------------------------------------------
// Custom jQuery Plugins
// -----------------------------------------------------------------------------

// jquery element exists helper function
jQuery.fn.elementExists = function () {
    return $(this).length > 0;
};

var processFormAjax = function ($form, json) {
    this.findElement = function ($form, identifier) {
        // @TODO - use more convenient way for identifying multiple elements with same id
        var $elm = $form.find("#" + identifier);
        if ($elm.elementExists()) {
            return $elm;
        }

        // "name" based search
        $elm = $form.find('[name="' + identifier + '"]');
        if ($elm.elementExists()) {
            return $elm;
        }

        // laravel dotted array notation
        var dot_pos = identifier.indexOf(".");
        if (dot_pos <= 0) {
            return null;
        }

        var name = identifier.substr(0, dot_pos);
        var index = identifier.substr(dot_pos + 1);

        if (index === "") {
            return null;
        }

        // array with explicit index
        $elm = $form.find('[name="' + name + '[' + index + ']"]');
        if ($elm.elementExists()) {
            return $elm;
        }

        // array with auto index
        $elm = $form.find('[name="' + name + '[]"]');

        return $elm[index] ? $($elm[index]) : null;
    };

    this.parseValidationErrors = function (json_error) {
        if (typeof (json_error) === "string") {
            alert(json_error);
            return;
        }

        for (var i in json_error) {
            var $elm = this.findElement($form, i);
            if ($elm === null) {
                continue;
            }

            $elm.addClass("is-invalid");

            var $formGroup = $elm.closest(".form-group");
            $formGroup.find(".invalid-feedback").remove();

            // detect ".input-group"
            var $inputGroup = $formGroup.find('.input-group').has($elm);

            // detect columnar form
            var $elementColumn = $formGroup.find('div[class*="col-"]').filter(function () {
                return this.className.match(/(?:^|\s)col-/);
            }).has($elm);

            var error_message = (typeof json_error[i] === "string") ? json_error[i] : json_error[i].join("<br>");

            var error_dom = '<div class="invalid-feedback">' + error_message + '</div>';

            if ($inputGroup.length > 0) {
                $inputGroup.append(error_dom);
            } else if ($elementColumn.length > 0) {
                $elementColumn.append(error_dom);
            } else {
                $formGroup.append(error_dom);
            }
        }
    };

    // parse form errors
    if (json.response_verror) {
        this.parseValidationErrors(json.response_verror);
    }
};

var processAjaxResponse = function (json) {
    this.parseHtml = function (json_html) {
        for (var i in json_html) {
            $(i).html(json_html[i]);
        }
    };

    this.parseValues = function (json_values) {
        for (var i in json_values) {
            $(i).val(json_values[i]);
        }
    };

    this.parseDataAttribs = function (json_data_attribs) {
        for (var i in json_data_attribs) {
            var name = Object.keys(json_data_attribs[i])[0];
            $(i).data(name, json_data_attribs[i][name]);
        }
    };

    this.parseVueData = function (vue_data) {
        for (var vue_var in vue_data) {
            if (window[vue_var] && window[vue_var] instanceof Vue) {
                for (var i in vue_data[vue_var]) {
                    window[vue_var][i] = vue_data[vue_var][i];
                }
            }
        }
    };

    this.parseNotifications = function (json) {
        for (var i in json) {
            js_notify(json[i], i);
        }
    };

    this.parseUrlHistory = function (json) {
        if(window.location.href !== json.url) {
            history.pushState(json.state, json.title, json.url);
        }
    }

    // parse html sections
    if (json.response_html) {
        this.parseHtml(json.response_html);
    }

    // parse form values
    if (json.response_values) {
        this.parseValues(json.response_values);
    }

    // parse data attributes
    if (json.response_data_attribs) {
        this.parseDataAttribs(json.response_data_attribs);
    }

    // parse for VueJs
    if (Vue && json.vue_data) {
        this.parseVueData(json.vue_data);
    }

    if (json.js_notifications) {
        this.parseNotifications(json.js_notifications);
    }

    // check for redirect url
    if (json.gotourl) {
        window.location.href = json.gotourl;
    }

    // URL history state
    if (json.url_history_push) {
        this.parseUrlHistory(json.url_history_push);
    }
};

var js_notify = function (msg, type) {
    toastr[type](msg);
};

var ajaxSubmitForm = function ($form, callback) {
    this.disableButton = function () {
        $form.find(":submit").each(function () {
            var $btn = $(this);
            var prev_disabled = $btn.prop("disabled");
            if (prev_disabled === true || prev_disabled === false) {
                $btn.data("vc-prev-disabled", prev_disabled);
                $btn.prop("disabled", true);
            }
        });
    };

    this.resetButton = function () {
        $form.find(":submit").each(function () {
            var $btn = $(this);
            var prev_disabled = $btn.data("vc-prev-disabled");
            if (prev_disabled === true || prev_disabled === false) {
                $btn.data("vc-prev-disabled", prev_disabled);
                $btn.prop("disabled", prev_disabled);
            }
        });
    };

    var this_fn = this;

    // clear all previous errors
    $form.find(".is-invalid").removeClass("is-invalid");
    $form.find(".invalid-feedback").remove();

    var hook_before_send = $form.data("hook-before-send");
    if (window[hook_before_send] && $.isFunction(window[hook_before_send])) {
        if (window[hook_before_send]($form) === false) {
            return false;
        }
    }

    // disable submit buttons before sending ajax request
    this_fn.disableButton();

    $.ajax({
        "url": $form.attr("action"),
        "method": $form.attr("method"),
        "data": $form.serialize(),
        "dataType": "json"
    }).done(function (data) {
        processFormAjax($form, data);

        var hook_success = $form.data("hook-success");
        if (window[hook_success] && $.isFunction(window[hook_success])) {
            window[hook_success]($form, data);
        }

        if (callback) {
            callback($form, data);
        }

        if (data.callback && window[data.callback] && $.isFunction(window[data.callback])) {
            window[data.callback]($form, data);
        }
    }).fail(function (xhr) {
        var hook_executed = false;
        var hook_error = $form.data("hook-error");
        if (window[hook_error] && $.isFunction(window[hook_error])) {
            if (xhr.responseText) {
                try {
                    var json = JSON.parse(xhr.responseText);

                    hook_executed = true;
                    window[hook_error]($form, json, xhr);
                } catch (e) {
                    //
                }
            }

            // 302 redirect will return status 200
            if (xhr.status === 200 && hook_executed === false) {
                hook_executed = true;
                window[hook_error]($form, {}, xhr);
            }
        }

        // 302 redirect will return status 200
        if (xhr.status !== 200 && xhr.status !== 422 && hook_executed === false) {
            js_notify("Error while getting response from server!", "error");
        }
    }).always(function (xhr) {
        /* handling Laravel default validation errors */
        if (xhr.status === 422 && xhr.responseJSON.errors) {
            processFormAjax($form, {
                response_verror: xhr.responseJSON.errors
            });
        }

        this_fn.resetButton();
    });
};

var initToolTips = function () {
    $('[data-toggle="tooltip"]').tooltip();

    $(document).ajaxComplete(function (event, xhr, settings) {
        $('[data-toggle="tooltip"]').tooltip("dispose");
        $('[data-toggle="tooltip"]').tooltip();
    });
};

var activateCurrentMenu = function () {
    var $menuItems = $(".sidebar .nav-item .nav-link");
    var $element = $menuItems.filter(function () {
        return $(this).attr("href") !== "#" && this.href === window.location.href;
    });

    if ($element.length === 0) {
        $element = $menuItems.filter(function () {
            return $(this).attr("href") !== "#" && window.location.href.indexOf(this.href) === 0;
        });
    }

    $element.addClass("active").closest(".nav-item").addClass("open");
    $element.parentsUntil(".sidebar-nav").each(function () {
        if ($(this).hasClass("nav-dropdown")) {
            $(this).addClass("open");
        }
    });
};

var vocoSetMomentOffset = function () {
    moment.now = function () {
        return vc_moment_date_offset + Date.now();
    };
};

var initAjaxTabs = function () {
    $('.vc-ajax-nav-tabs a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        // e.target // newly activated tab
        // e.relatedTarget // previous active tab
        var $tab = $(e.target);
        var $target = $($tab.attr("href"));

        // load tab content via ajax
        if ($.trim($target.html()) === "") {
            $target.html('<i class="fa fa-refresh fa-spin fa-2x fa-fw text-muted"></i><span class="sr-only">Loading...</span>');
            var loading_error_html = '<div class="alert alert-danger">There was a problem fetching tab content. Please try again later.</div>';

            $.getJSON($tab.data('url'), {"rq": "ajxfx"}, function (data) {
                if (data.ajax_tab_html) {
                    $target.html(data.ajax_tab_html);
                } else {
                    $target.html(loading_error_html);
                }
            }).fail(function () {
                $target.html(loading_error_html);
            });
        }
    });
};

var initVocoAjaxModal = function () {
    $("body").on("click", '[data-voco-toggle="modal"]', function (e) {
        e.preventDefault();

        var $this = $(this);
        var $modal = $($this.data("target"));
        var $modalBody = $modal.find(".modal-body");

        if ($this.data("modal-wide") === true) {
            // $modal.addClass("modal-wide");
            $modal.find(".modal-dialog").addClass("modal-lg");
        } else {
            // $modal.removeClass("modal-wide");
            $modal.find(".modal-dialog").removeClass("modal-lg");
        }

        var modal_opt = {};
        for (var i in $this[0].dataset) {
            if (!["modalTitle", "modalWide", "originalTitle", "target", "toggle", "url", "vocoToggle"].includes(i)) {
                modal_opt[i] = $this[0].dataset[i];
            }
        }

        // launch modal
        // $modal.modal("show", $this);
        $modal.modal(modal_opt);

        $modalBody.empty().append("Loading...");

        var dataModalTitle = $this.data("modal-title");
        if (dataModalTitle) {
            $modal.find(".modal-title").empty().append(dataModalTitle);
        }
        var dataModalButton = $this.data("modal-button-save");
        if (dataModalButton === false) {
            $modal.find(".btn-voco-save").hide();
        } else {
            // save button may be hidden in previous modal, re-enable it
            $modal.find(".btn-voco-save").show();
        }

        var url = $this.data("url");
        var href = $.trim($this.attr("href"));
        if (href !== "#" && href !== "") {
            // href has high priority over data url
            url = href;
        }

        if (url) {
            var urlParams = $this.data("url-params");

            var paramObj = {};
            if (window[urlParams] && $.isFunction(window[urlParams])) {
                paramObj = window[urlParams]($this);
            } else if ($.isPlainObject(urlParams)) {
                paramObj = urlParams;
            }

            url.replace(/\/+$/, "") + "/?";
            if (typeof paramObj === "string") {
                url += paramObj;
            } else {
                url += $.param(paramObj);
            }

            if ($this.data("iframe-content") === true) {
                $modal.find(".modal-content").addClass("iframe-content");
                $modalBody.empty().append('<iframe title="" width="100%" height="100%" src="' + url + '"></iframe>');
            } else {
                $modal.find(".modal-content").removeClass("iframe-content");

                // fixes a bootstrap bug that prevents a modal from being reused
                $modalBody.load(url, function (response, status, xhr) {
                    if (status === "error") {
                        $modalBody.empty().append('<h2>Oh boy</h2><p>Sorry, but there was an error:' + xhr.status + ' ' + xhr.statusText + '</p>');
                    }

                    // enable select2 plugin
                    applySelect2($(".voco-select2-ajax"));

                    // hide save button, when there is no form
                    if ($modalBody.find("form").length === 0 && $modalBody.find(".has-vue-form").length === 0) {
                        $modal.find(".btn-voco-save").hide();
                    }

                    return this;
                });
            }
        }
    });
};

var bootConfirm = function (title, message, callback, options) {
    var defaults = {
        title: (title || "Confirm"),
        message: message,
        buttons: {
            cancel: {
                label: '<i class="fa fa-times"></i> Cancel'
            },
            confirm: {
                label: '<i class="fa fa-check"></i> Confirm'
            }
        },
        callback: function (result) {
            callback(result);
        }
    };
 
    bootbox.confirm($.extend({}, defaults, options || {}));
};

var triggerConfirm = function ($trigger, callback) {
    var message = $.trim($trigger.data("bc-confirm")) || "Are you sure?";
    var title = $.trim($trigger.data("bc-title")) || "Confirm";

    bootConfirm(title, message, callback, {
        buttons: {
            cancel: {
                label: '<i class="fa fa-times"></i> ' + ($trigger.data("bc-cancel-label") || "Cancel")
            },
            confirm: {
                label: '<i class="fa fa-check"></i> ' + ($trigger.data("bc-confirm-label") || "Confirm")
            }
        }
    });
};

var initExplainerBox = function (action) {
    var __this = this;
    __this.open_box = function () {
        //
    };

    __this.close_box = function () {
        $(".vcexbox").removeClass("in").animate({width: 0});
        $(".voucher-explainer-opener").removeClass("opener-active");
    };

    __this.load_content = function ($opener) {
        if (!$opener.data("url")) {
            return false;
        }

        $(".explainer-body").html("Loading...");
        $.getJSON($opener.data("url"), function (data) {
            if (data.ajax_tab_html) {
                $(".explainer-body").html(data.ajax_tab_html);
                applySelect2();
            } else {
                $(".explainer-body").html("Failed to load contents! Try again later.");
            }
            $(".vcexbox-title").text($opener.data("title") || "Quick Edit");
        });
    };

    __this.refresh_content = function () {
        __this.load_content($(".opener-active"));
    };

    $("body").on("click", function (event) {
        var $target = $(event.target);
        if (!$target.closest(".vcexbox").length && !$target.hasClass("voucher-explainer-opener")) {
            __this.close_box();
        }
    });

    $(document).keyup(function (e) {
        if (e.keyCode === 27) {
            // @TODO - check whether box opened
            __this.close_box();
        }
    });

    $("body").on("click", ".explainer-body .vc-form-btn-cancel, .vcexbox-header .close", function (e) {
        e.preventDefault();
        __this.close_box();
    });

    var boxWidth = '70vw';

    $("body").on("click", ".voucher-explainer-opener", function (e) {
        e.preventDefault();
        var $this = $(this);

        __this.load_content($this);

        $(".explainer-body").css({"opacity": 0.25});

        if ($(".vcexbox").hasClass("in")) {
            if ($this.hasClass("opener-active")) {
                __this.close_box();
            } else {
                $(".explainer-body").fadeTo("slow", 1);
            }
        } else {
            $(".vcexbox").animate({
                "width": boxWidth
            }, function () {
                $(".vcexbox").addClass("in");
                $(".explainer-body").fadeTo("slow", 1);
            });
        }

        $(".voucher-explainer-opener").removeClass("opener-active");
        $this.addClass("opener-active");
    });

    if (__this[action]) {
        __this[action]();
    }
};

var applySelect2 = function ($selector = null) {
    if (!window.vocoHasSelect2 || !window.vocoHasSelect2()) {
        return;
    }

    if ($selector === null) {
        $selector = $(".voco-select2");
    }
    $selector.each(function (i, obj) {
        window.vocoInitSelect2($(obj));
    });
};

jQuery(function ($) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ajaxComplete(function (event, xhr, settings) {
        if (xhr.responseJSON) {
            processAjaxResponse(xhr.responseJSON);
            return;
        }

        try {
            var json = $.parseJSON(xhr.responseText);
            if (json) {
                processAjaxResponse(json);
            }
        } catch (error) {
        }
    });

    $(document).ajaxError(function(event, jqxhr, settings) {
        if (jqxhr.status === 401 && global_urls.login) {
            window.location.href = global_urls.login;
        }
    });

    // timezone offset for user timezone
    vocoSetMomentOffset();

    // enable current menu
    activateCurrentMenu();

    // bootstrap tooltips
    initToolTips();

    // ajax loaded tabs
    initAjaxTabs();

    // ajax modal form
    initVocoAjaxModal();

    // quick explainer box
    initExplainerBox();

    $("body").on("click", "#voco-ajax-modal .btn-voco-save", function () {
        var $form = $("#voco-ajax-modal .voco-ajax-form");
        if ($form.length === 0) {
            $form = $("#voco-ajax-modal form");
        }
        if ($form.length > 0) {
            ajaxSubmitForm($form);
        }
    });

    $("body").on("submit", ".voco-ajax-form", function (e) {
        e.preventDefault();
        var $this = $(this);

        // reset pagination link
        if ($(".card-top-search").has($this)) {
            $this.find('[name="page"]').val(1);
        }

        if ($this.hasClass("tab-search-form")) {
            $this.find('[name="tab_search"]').val(1);
        }

        ajaxSubmitForm($this);
    });

    var $searchForm = $('.card-top-search[data-auto-submit="true"]').find(".voco-ajax-form");
    if ($searchForm.length > 0) {
        ajaxSubmitForm($searchForm);
    }

//    var $autoForm = $('form[data-auto-submit="true"]');
//    if ($autoForm.length > 0) {
//        $autoForm.each(function(){
//            // @TODO - submit multiple forms with some interval
//            ajaxSubmitForm($(this));
//        });
//    }

    var getPaginatorPageNumber = function (url) {
        var query_string = url.split('?');

        if (!query_string[1]) {
            return false;
        }
        var params = query_string[1].split('&');

        for (var i in params) {
            var single_param = params[i].split("=");
            if (single_param[0] && single_param[1] && single_param[0] === 'page') {
                return single_param[1];
            }
        }

        return false;
    };

    // ajax pagination
    $("body").on("click", ".card-search-results .pagination a", function (e) {
        e.preventDefault();

        var url = $(this).attr("href");
        var $input_page_num = $('.voco-ajax-form [name="page"]');

        if ($input_page_num.length > 0) {
            var page = getPaginatorPageNumber(url);

            $input_page_num.val(page);

            ajaxSubmitForm($input_page_num.closest(".voco-ajax-form"));
        }
        // $(".card-search-results").load($(this).attr("href"));
    });

    $("body").on("click", ".tab-pagination .pagination a", function (e) {
        var $form = $(".tab-search-form");
        if ($form.length > 0) {
            e.preventDefault();
            // set pagination link to form action
            $form.attr("action", $(this).attr("href"));
            ajaxSubmitForm($form);
        }
    });

    // Confirm before redirect
    $("body").on("click", ".voco-confirm-link", function (e) {
        e.preventDefault();
        var $this = $(this);
        triggerConfirm($this, function (result) {
            if (result) {
                window.location.href = $this.data("url");
            }
        });
    });

    // Confirm before submit
    $("body").on("click", ".voco-confirm-submit", function (e) {
        e.preventDefault();
        var $this = $(this);
        triggerConfirm($this, function (result) {
            if (result) {
                var $form = $this.closest("form");
                if($form.hasClass("voco-ajax-form")) {
                    ajaxSubmitForm($form);
                } else {
                    $form[0].submit();
                }
            }
        });
    });

    applySelect2();

    $('a[data-toggle="tab"]').on("shown.bs.tab", function (e) {
        $(".tab-search-form").attr("action", $(this).data("url"));
    });

    $('[data-toggle="popover"]').popover();
});
