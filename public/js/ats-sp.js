
const quickGotoTask = function ($form, data) {
    if (data.issue_url) {
        window.location.href = data.issue_url;
    } else if (data.issue_error) {
        alert(data.issue_error);
    }
};

//const ajaxPunchIn = function () {
//    $.post("", {}, function (data) {
//
//    }, "json");
//};

var atsfn = {};

atsfn.issueTimerClock = {
    interval: {asc: null, des: null},
    show: function ($element, seconds, asc_des) {
        if (asc_des !== "asc" && asc_des !== "des") {
            return false;
        }

        var showTime = function ($element, seconds) {
            $element.text(
                    Math.floor(seconds / 3600).toString().padStart(2, "0") + ":"
                    + Math.floor((seconds / 60) % 60).toString().padStart(2, "0") + ":"
                    + (seconds % 60).toString().padStart(2, "0")
                    );
        };

        var $this = this;
        $this.hide(asc_des);

        this.interval[asc_des] = setInterval(function () {
            if (asc_des === "asc") {
                ++seconds;
            } else if (--seconds <= 0) {
                $this.hide(asc_des);
            }
            showTime($element, seconds);
        }, 1000);
    },
    hide: function (asc_des) {
        if (this.interval && this.interval[asc_des]) {
            clearInterval(this.interval[asc_des]);
            this.interval[asc_des] = null;
        }
    }
};

jQuery(function ($) {
    $('.form-quick-goto-task [name="issue_id"]').on("input", function () {
        var txt_value = this.value;
        var has_hash = (txt_value.indexOf("#") === 0);
        txt_value = (has_hash ? "#" : "") + txt_value.replace(/[^\d]/g, "");
        if (this.value != txt_value) {
            this.value = txt_value;
        }
    });

    Vue.filter('nl2br', function (value) {
        return value.split("\n").join("<br>");
    });
});
