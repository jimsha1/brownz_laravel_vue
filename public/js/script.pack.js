jQuery.fn.elementExists = function () {
    return 0 < $(this).length;
};
var processFormAjax = function (a, b) {
    (this.findElement = function (a, b) {
        var c = a.find("#" + b);
        if (c.elementExists()) return c;
        if (((c = a.find('[name="' + b + '"]')), c.elementExists()))
            return c;
        var d = b.indexOf(".");
        if (0 >= d) return null;
        var e = b.substr(0, d),
            f = b.substr(d + 1);
        return "" === f
            ? null
            : ((c = a.find('[name="' + e + "[" + f + ']"]')),
                c.elementExists())
                ? c
                : ((c = a.find('[name="' + e + '[]"]')), c[f] ? $(c[f]) : null);
    }),
        (this.parseValidationErrors = function (b) {
            if ("string" == typeof b) return void alert(b);
            for (var c in b) {
                var d = this.findElement(a, c);
                if (null !== d) {
                    d.addClass("is-invalid");
                    var e = d.closest(".form-group");
                    e.find(".invalid-feedback").remove();
                    var f = e.find(".input-group").has(d),
                        g = e
                            .find('div[class*="col-"]')
                            .filter(function () {
                                return this.className.match(/(?:^|\s)col-/);
                            })
                            .has(d),
                        h =
                            "string" == typeof b[c]
                                ? b[c]
                                : b[c].join("<br>"),
                        i = '<div class="invalid-feedback">' + h + "</div>";
                    0 < f.length
                        ? f.append(i)
                        : 0 < g.length
                            ? g.append(i)
                            : e.append(i);
                }
            }
        }),
        b.response_verror && this.parseValidationErrors(b.response_verror);
},
    processAjaxResponse = function (a) {
        (this.parseHtml = function (a) {
            for (var b in a) $(b).html(a[b]);
        }),
            (this.parseValues = function (a) {
                for (var b in a) $(b).val(a[b]);
            }),
            (this.parseDataAttribs = function (a) {
                for (var b in a) {
                    var c = Object.keys(a[b])[0];
                    $(b).data(c, a[b][c]);
                }
            }),
            (this.parseVueData = function (a) {
                for (var b in a)
                    if (window[b] && window[b] instanceof Vue)
                        for (var c in a[b]) window[b][c] = a[b][c];
            }),
            (this.parseNotifications = function (a) {
                for (var b in a) js_notify(a[b], b);
            }),
            (this.parseUrlHistory = function (a) {
                window.location.href !== a.url &&
                    history.pushState(a.state, a.title, a.url);
            }),
            a.response_html && this.parseHtml(a.response_html),
            a.response_values && this.parseValues(a.response_values),
            a.response_data_attribs &&
            this.parseDataAttribs(a.response_data_attribs),
            Vue && a.vue_data && this.parseVueData(a.vue_data),
            a.js_notifications && this.parseNotifications(a.js_notifications),
            a.gotourl && (window.location.href = a.gotourl),
            a.url_history_push && this.parseUrlHistory(a.url_history_push);
    },
    js_notify = function (a, b) {
        toastr[b](a);
    },
    ajaxSubmitForm = function (a, b) {
        (this.disableButton = function () {
            a.find(":submit").each(function () {
                var a = $(this),
                    b = a.prop("disabled");
                (!0 === b || !1 === b) &&
                    (a.data("vc-prev-disabled", b), a.prop("disabled", !0));
            });
        }),
            (this.resetButton = function () {
                a.find(":submit").each(function () {
                    var a = $(this),
                        b = a.data("vc-prev-disabled");
                    (!0 === b || !1 === b) &&
                        (a.data("vc-prev-disabled", b), a.prop("disabled", b));
                });
            });
        var c = this;
        a.find(".is-invalid").removeClass("is-invalid"),
            a.find(".invalid-feedback").remove();
        var d = a.data("hook-before-send");
        return (
            !(window[d] && $.isFunction(window[d]) && !1 === window[d](a)) &&
            void (c.disableButton(),
                $.ajax({
                    url: a.attr("action"),
                    method: a.attr("method"),
                    data: a.serialize(),
                    dataType: "json",
                })
                    .done(function (c) {
                        processFormAjax(a, c);
                        var d = a.data("hook-success");
                        window[d] && $.isFunction(window[d]) && window[d](a, c),
                            b && b(a, c),
                            c.callback &&
                            window[c.callback] &&
                            $.isFunction(window[c.callback]) &&
                            window[c.callback](a, c);
                    })
                    // .fail(function (b) {
                    //     var c = !1,
                    //         d = a.data("hook-error");
                    //     if (window[d] && $.isFunction(window[d])) {
                    //         if (b.responseText)
                    //             try {
                    //                 var e = JSON.parse(b.responseText);
                    //                 (c = !0), window[d](a, e, b);
                    //             } catch (a) {}
                    //         200 === b.status &&
                    //             !1 === c &&
                    //             ((c = !0), window[d](a, {}, b));
                    //     }
                    //     200 !== b.status &&
                    //         422 !== b.status &&
                    //         !1 === c &&
                    //         js_notify(
                    //             "Error while getting response from server!",
                    //             "error"
                    //         );
                    // })
                    .fail(function (b) {
                        var c = false,
                            d = a.data("hook-error");

                        if (window[d] && $.isFunction(window[d])) {
                            if (b.responseText) {
                                try {
                                    var e = JSON.parse(b.responseText);
                                    c = true;
                                    window[d](a, e, b);
                                } catch (err) { }
                            }

                            if (b.status === 200 && !c) {
                                c = true;
                                window[d](a, {}, b);
                            }
                        }

                        // Show Laravel exception message
                        if (b.status === 422 && b.responseJSON && b.responseJSON.message) {
                            js_notify(b.responseJSON.message, "error");
                            return;
                        }

                        if (b.status !== 200 && b.status !== 422 && !c) {
                            js_notify("Error while getting response from server!", "error");
                        }
                    })
                    .always(function (b) {
                        422 === b.status &&
                            b.responseJSON.errors &&
                            processFormAjax(a, {
                                response_verror: b.responseJSON.errors,
                            }),
                            c.resetButton();
                    }))
        );
    },
    initToolTips = function () {
        $('[data-toggle="tooltip"]').tooltip(),
            $(document).ajaxComplete(function () {
                $('[data-toggle="tooltip"]').tooltip("dispose"),
                    $('[data-toggle="tooltip"]').tooltip();
            });
    },
    activateCurrentMenu = function () {
        var a = $(".sidebar .nav-item .nav-link"),
            b = a.filter(function () {
                return (
                    "#" !== $(this).attr("href") &&
                    this.href === window.location.href
                );
            });
        0 === b.length &&
            (b = a.filter(function () {
                return (
                    "#" !== $(this).attr("href") &&
                    0 === window.location.href.indexOf(this.href)
                );
            })),
            b.addClass("active").closest(".nav-item").addClass("open"),
            b.parentsUntil(".sidebar-nav").each(function () {
                $(this).hasClass("nav-dropdown") && $(this).addClass("open");
            });
    },
    vocoSetMomentOffset = function () {
        moment.now = function () {
            return vc_moment_date_offset + Date.now();
        };
    },
    initAjaxTabs = function () {
        $('.vc-ajax-nav-tabs a[data-toggle="tab"]').on(
            "shown.bs.tab",
            function (a) {
                var b = $(a.target),
                    c = $(b.attr("href"));
                if ("" === $.trim(c.html())) {
                    c.html(
                        '<i class="fa fa-refresh fa-spin fa-2x fa-fw text-muted"></i><span class="sr-only">Loading...</span>'
                    );
                    $.getJSON(b.data("url"), { rq: "ajxfx" }, function (a) {
                        a.ajax_tab_html
                            ? c.html(a.ajax_tab_html)
                            : c.html(
                                '<div class="alert alert-danger">There was a problem fetching tab content. Please try again later.</div>'
                            );
                    }).fail(function () {
                        c.html(
                            '<div class="alert alert-danger">There was a problem fetching tab content. Please try again later.</div>'
                        );
                    });
                }
            }
        );
    },
    initVocoAjaxModal = function () {
        $("body").on("click", '[data-voco-toggle="modal"]', function (a) {
            a.preventDefault();
            var b = $(this),
                c = $(b.data("target")),
                d = c.find(".modal-body");
            !0 === b.data("modal-wide")
                ? c.find(".modal-dialog").addClass("modal-lg")
                : c.find(".modal-dialog").removeClass("modal-lg");
            var e = {};
            for (var f in b[0].dataset)
                [
                    "modalTitle",
                    "modalWide",
                    "originalTitle",
                    "target",
                    "toggle",
                    "url",
                    "vocoToggle",
                ].includes(f) || (e[f] = b[0].dataset[f]);
            c.modal(e), d.empty().append("Loading...");
            var g = b.data("modal-title");
            g && c.find(".modal-title").empty().append(g);
            var h = b.data("modal-button-save");
            !1 === h
                ? c.find(".btn-voco-save").hide()
                : c.find(".btn-voco-save").show();
            var i = b.data("url"),
                j = $.trim(b.attr("href"));
            if (("#" !== j && "" !== j && (i = j), i)) {
                var k = b.data("url-params"),
                    l = {};
                window[k] && $.isFunction(window[k])
                    ? (l = window[k](b))
                    : $.isPlainObject(k) && (l = k),
                    i.replace(/\/+$/, "") + "/?",
                    (i += "string" == typeof l ? l : $.param(l)),
                    !0 === b.data("iframe-content")
                        ? (c.find(".modal-content").addClass("iframe-content"),
                            d
                                .empty()
                                .append(
                                    '<iframe title="" width="100%" height="100%" src="' +
                                    i +
                                    '"></iframe>'
                                ))
                        : (c
                            .find(".modal-content")
                            .removeClass("iframe-content"),
                            d.load(i, function (a, b, e) {
                                return (
                                    "error" === b &&
                                    d
                                        .empty()
                                        .append(
                                            "<h2>Oh boy</h2><p>Sorry, but there was an error:" +
                                            e.status +
                                            " " +
                                            e.statusText +
                                            "</p>"
                                        ),
                                    applySelect2($(".voco-select2-ajax")),
                                    0 === d.find("form").length &&
                                    0 === d.find(".has-vue-form").length &&
                                    c.find(".btn-voco-save").hide(),
                                    this
                                );
                            }));
            }
        });
    },
    bootConfirm = function (a, b, c, d) {
        bootbox.confirm(
            $.extend(
                {},
                {
                    title: a || "Confirm",
                    message: b,
                    buttons: {
                        cancel: { label: '<i class="fa fa-times"></i> Cancel' },
                        confirm: {
                            label: '<i class="fa fa-check"></i> Confirm',
                        },
                    },
                    callback: function (a) {
                        c(a);
                    },
                },
                d || {}
            )
        );
    },
    triggerConfirm = function (a, b) {
        var c = $.trim(a.data("bc-confirm")) || "Are you sure?",
            d = $.trim(a.data("bc-title")) || "Confirm";
        bootConfirm(d, c, b, {
            buttons: {
                cancel: {
                    label:
                        '<i class="fa fa-times"></i> ' +
                        (a.data("bc-cancel-label") || "Cancel"),
                },
                confirm: {
                    label:
                        '<i class="fa fa-check"></i> ' +
                        (a.data("bc-confirm-label") || "Confirm"),
                },
            },
        });
    },
    initExplainerBox = function (a) {
        var b = this;
        (b.open_box = function () { }),
            (b.close_box = function () {
                $(".vcexbox").removeClass("in").animate({ width: 0 }),
                    $(".voucher-explainer-opener").removeClass("opener-active");
            }),
            (b.load_content = function (a) {
                return (
                    !!a.data("url") &&
                    void ($(".explainer-body").html("Loading..."),
                        $.getJSON(a.data("url"), function (b) {
                            b.ajax_tab_html
                                ? ($(".explainer-body").html(b.ajax_tab_html),
                                    applySelect2())
                                : $(".explainer-body").html(
                                    "Failed to load contents! Try again later."
                                ),
                                $(".vcexbox-title").text(
                                    a.data("title") || "Quick Edit"
                                );
                        }))
                );
            }),
            (b.refresh_content = function () {
                b.load_content($(".opener-active"));
            }),
            $("body").on("click", function (a) {
                var c = $(a.target);
                c.closest(".vcexbox").length ||
                    c.hasClass("voucher-explainer-opener") ||
                    b.close_box();
            }),
            $(document).keyup(function (a) {
                27 === a.keyCode && b.close_box();
            }),
            $("body").on(
                "click",
                ".explainer-body .vc-form-btn-cancel, .vcexbox-header .close",
                function (a) {
                    a.preventDefault(), b.close_box();
                }
            );
        $("body").on("click", ".voucher-explainer-opener", function (a) {
            a.preventDefault();
            var c = $(this);
            b.load_content(c),
                $(".explainer-body").css({ opacity: 0.25 }),
                $(".vcexbox").hasClass("in")
                    ? c.hasClass("opener-active")
                        ? b.close_box()
                        : $(".explainer-body").fadeTo("slow", 1)
                    : $(".vcexbox").animate({ width: "70vw" }, function () {
                        $(".vcexbox").addClass("in"),
                            $(".explainer-body").fadeTo("slow", 1);
                    }),
                $(".voucher-explainer-opener").removeClass("opener-active"),
                c.addClass("opener-active");
        }),
            b[a] && b[a]();
    },
    applySelect2 = function (a = null) {
        if (!window.vocoHasSelect2 || !window.vocoHasSelect2()) return;
        null === a && (a = $(".voco-select2")),
            a.each(function (a, b) {
                window.vocoInitSelect2($(b));
            });
    };
jQuery(function (a) {
    a.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": a('meta[name="csrf-token"]').attr("content"),
        },
    }),
        a(document).ajaxComplete(function (b, c) {
            if (c.responseJSON) return void processAjaxResponse(c.responseJSON);
            try {
                var d = a.parseJSON(c.responseText);
                d && processAjaxResponse(d);
            } catch (a) { }
        }),
        a(document).ajaxError(function (a, b) {
            401 === b.status &&
                global_urls.login &&
                (window.location.href = global_urls.login);
        }),
        vocoSetMomentOffset(),
        activateCurrentMenu(),
        initToolTips(),
        initAjaxTabs(),
        initVocoAjaxModal(),
        initExplainerBox(),
        a("body").on("click", "#voco-ajax-modal .btn-voco-save", function () {
            var b = a("#voco-ajax-modal .voco-ajax-form");
            0 === b.length && (b = a("#voco-ajax-modal form")),
                0 < b.length && ajaxSubmitForm(b);
        }),
        a("body").on("submit", ".voco-ajax-form", function (b) {
            b.preventDefault();
            var c = a(this);
            a(".card-top-search").has(c) && c.find('[name="page"]').val(1),
                c.hasClass("tab-search-form") &&
                c.find('[name="tab_search"]').val(1),
                ajaxSubmitForm(c);
        });
    var b = a('.card-top-search[data-auto-submit="true"]').find(
        ".voco-ajax-form"
    );
    0 < b.length && ajaxSubmitForm(b);
    var c = function (a) {
        var b = a.split("?");
        if (!b[1]) return !1;
        var c = b[1].split("&");
        for (var d in c) {
            var e = c[d].split("=");
            if (e[0] && e[1] && "page" === e[0]) return e[1];
        }
        return !1;
    };
    a("body").on("click", ".card-search-results .pagination a", function (b) {
        b.preventDefault();
        var d = a(this).attr("href"),
            e = a('.voco-ajax-form [name="page"]');
        if (0 < e.length) {
            var f = c(d);
            e.val(f), ajaxSubmitForm(e.closest(".voco-ajax-form"));
        }
    }),
        a("body").on("click", ".tab-pagination .pagination a", function (b) {
            var c = a(".tab-search-form");
            0 < c.length &&
                (b.preventDefault(),
                c.attr("action", a(this).attr("href")),
                ajaxSubmitForm(c));
        }),
        a("body").on("click", ".voco-confirm-link", function (b) {
            b.preventDefault();
            var c = a(this);
            triggerConfirm(c, function (a) {
                a && (window.location.href = c.data("url"));
            });
        }),
        a("body").on("click", ".voco-confirm-submit", function (b) {
            b.preventDefault();
            var c = a(this);
            triggerConfirm(c, function (a) {
                if (a) {
                    var b = c.closest("form");
                    b.hasClass("voco-ajax-form")
                        ? ajaxSubmitForm(b)
                        : b[0].submit();
                }
            });
        }),
        applySelect2(),
        a('a[data-toggle="tab"]').on("shown.bs.tab", function () {
            a(".tab-search-form").attr("action", a(this).data("url"));
        }),
        a('[data-toggle="popover"]').popover();
});
