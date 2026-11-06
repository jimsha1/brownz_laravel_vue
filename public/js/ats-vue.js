
var refreshTooltips = function ($parent) {
    $parent.find('[data-toggle="tooltip"]').tooltip("dispose");
    $parent.find('[data-toggle="tooltip"]').tooltip();
};

var refreshSelect2 = function ($parent) {
    if (!window.vocoHasSelect2 || !window.vocoHasSelect2()) {
        return;
    }

    $parent.find(".voco-select2").each(function () {
        window.vocoDestroySelect2($(this));
    });
    applySelect2($parent.find(".voco-select2"));
};

function get_voco_moment(value, format) {
    if (!value) {
        return '';
    }

    if (format === 'humanize') {
        // @TODO - use server time and timezone for "moment()"
        return moment.duration(moment(value).diff(moment())).humanize(true);
    }

    return moment(value).format(format);
}
function get_voco_moment11(value, format) {
  if (!value) {
      return '';
  }

  try {
      // Handle "humanize" format separately
      if (format === 'humanize') {
          // Use server time and timezone for "moment()" if required
          const duration = moment.duration(moment(value).diff(moment()));
          return duration.humanize(true);
      }

      // Parse and format the date
      const momentDate = moment(value, format, true); // Strict parsing
      if (momentDate.isValid()) {
          return momentDate.format(format);
      } else {
          console.warn(`Invalid date value provided: ${value}`);
          return value; // Return raw value if invalid
      }
  } catch (error) {
      console.error(`Error in get_voco_moment: ${error.message}`);
      return value; // Fallback to the raw value if an error occurs
  }
}


Vue.filter('vc_date', function (value) {
    return get_voco_moment(value, 'DD/MM/YYYY');
});

Vue.filter('vc_datetime', function (value) {
    return get_voco_moment(value, 'DD/MM/YYYY hh:mma');
});

Vue.filter('vc_date_expanded', function (value) {
    return get_voco_moment(value, 'D MMM, YYYY');
});

Vue.filter('vc_datetime_expanded', function (value) {
    return get_voco_moment(value, 'D MMM, YYYY \\a\\t h:mma');
});

Vue.filter('vc_humanize', function (value) {
    return get_voco_moment(value, 'humanize');
});

Vue.filter('vc_money', function (value) {
    return parseFloat(value).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
});

Vue.filter('vc_ucwords', function (value) {
    return (value + '').replace(/^(.)|\s+(.)/g, function ($1) {
        return $1.toUpperCase();
    });
});

/* global mixin */
Vue.mixin({
  methods: {
    vlist_length: function (list) {
      if (Array.isArray(list)) {
        return list.length;
      }
      if (list instanceof Object) {
        return Object.keys(list).length;
      }
      return 0;
    }
  }
});

var vcinput_mixin = {
    inheritAttrs: false,
    props: {
        label: String,
        value: [String, Number],
        colclass: {
            type: String,
            default: "col-md-6"
        }
    }
};

Vue.component('vcinput-basic', {
    mixins: [vcinput_mixin],
    template: `
<div class="form-group" v-bind:class="colclass">
  <label v-if="label" v-bind:for="$attrs.id">{{ label }}</label>
  <input
    type="text"
    class="form-control"
    v-bind:name="$attrs.name||$attrs.id"
    v-bind:placeholder="$attrs.placeholder||label"
    v-bind="$attrs"
    v-bind:value="value"
    v-on:input="$emit('input', $event.target.value)"
  >
</div>
`
});

Vue.component('vcinput-basic-email', {
    mixins: [vcinput_mixin],
    template: `
<div class="form-group" v-bind:class="colclass">
  <label v-if="label" v-bind:for="$attrs.id">{{ label }}</label>
  <input
    type="email"
    class="form-control"
    v-bind:name="$attrs.name||$attrs.id"
    v-bind:placeholder="$attrs.placeholder||label"
    v-bind="$attrs"
    v-bind:value="value"
    v-on:input="$emit('input', $event.target.value)"
  >
</div>
`
});

Vue.component('vcinput-basic-number', {
  mixins: [vcinput_mixin],
  template: `
<div class="form-group" v-bind:class="colclass">
<label v-if="label" v-bind:for="$attrs.id">{{ label }}</label>
<input
  type="number"
  class="form-control"
  v-bind:name="$attrs.name||$attrs.id"
  v-bind:placeholder="$attrs.placeholder||label"
  v-bind="$attrs"
  v-bind:value="value"
  v-on:input="$emit('input', $event.target.value)"
>
</div>
`
});

Vue.component('vcinput-textarea', {
    mixins: [vcinput_mixin],
    template: `
<div class="form-group" v-bind:class="colclass">
  <label v-if="label" v-bind:for="$attrs.id">{{ label }}</label>
  <textarea
    class="form-control"
    v-bind:name="$attrs.name||$attrs.id"
    v-bind:placeholder="$attrs.placeholder||label"
    v-bind="$attrs"
    v-bind:value="value"
    v-on:input="$emit('input', $event.target.value)"
  ></textarea>
</div>
`
});

Vue.component('vcinput-select2', {
    mixins: [vcinput_mixin],
    mounted() {
        var vm = this;
        var $el_dom = $("#" + vm.$attrs.id);

        if (!window.vocoHasSelect2 || !window.vocoHasSelect2()) {
            return;
        }

        var options = $.map(vm.$attrs.options || [], function (obj) {
          var option = $.extend({}, obj);
          option.selected = String(vm.value) === String(obj.id);
          return option;
        });

        const snakeToCamel = str => str.replace(/([-_]\w)/g, g => g[1].toUpperCase());

        // @TODO - avoid multiple code execution by moving this code block
        // from `templateSelection` to `mounted`
        var allowClear = $el_dom.data('allow-clear') === true
          || String($el_dom.attr('data-allow-clear') || '').toLowerCase() === 'true'
          || String(vm.$attrs['data-allow-clear'] || '').toLowerCase() === 'true';

        $el_dom.select2({
          data: options,
          allowClear: allowClear,
          placeholder: vm.$attrs.placeholder || vm.label || '',
          templateSelection: function (data, container) {
          for(var prop in data){
            if(!(["id", "text", "element", "selected", "disabled"].includes(prop))){
              data.element.dataset[snakeToCamel(prop)] = data[prop];
            }
          }
          return data.text;
        }}).on("change", function(e) {
          vm.$emit("input", this.value)
        });

        if (vm.value !== undefined && vm.value !== null && vm.value !== '') {
          $el_dom.val(String(vm.value)).trigger('change.select2');
        }

        vm.$once("hook:beforeDestroy", function() {
          $el_dom.select2("destroy")
        });
    },
    template: `
<div class="form-group" v-bind:class="colclass">
  <label v-if="label" v-bind:for="$attrs.id">{{ label }}</label>
  <select class="form-control vue-select2"
    v-bind:name="$attrs.name||$attrs.id"
    v-bind:data-placeholder="$attrs.placeholder||label"
    v-bind="$attrs"
    v-bind:value="value"
  >
    <option value=""></option>
  </select>
</div>
`
});
Vue.component('vcinput-select21', {
    mixins: [vcinput_mixin],
    mounted() {
        var vm = this;
        var $el_dom = $("#" + vm.$attrs.id);

        if (!window.vocoHasSelect2 || !window.vocoHasSelect2()) {
            return;
        }

        var options = $.map(vm.$attrs.options || [], function (obj) {
            var option = $.extend({}, obj);
            option.selected = String(vm.value) === String(obj.id);
            return option;
        });

        console.log("VM data:", vm);
        console.log("Select2 options:", options);
        console.log("Select2 data:", $el_dom.data());


        $el_dom.select2({
            data: options,
            templateSelection: function (data, container) {
                for (var prop in data) {
                    if (!(["id", "text", "element", "selected", "disabled"].includes(prop))) {
                        data.element.dataset[snakeToCamel(prop)] = data[prop];
                    }
                }
                return data.text;
            }
        }).on("change", function (e) {
            vm.$emit("input", this.value);
        });

        if (vm.value !== undefined && vm.value !== null && vm.value !== '') {
            $el_dom.val(String(vm.value)).trigger('change.select2');
        }

        vm.$once("hook:beforeDestroy", function () {
            $el_dom.select2("destroy");
        });
    },
    template: `
<div class="form-group" v-bind:class="colclass">
  <label v-if="label" v-bind:for="$attrs.id">{{ label }}</label>
  <select class="form-control vue-select2"
    v-bind:name="$attrs.name||$attrs.id"
    v-bind:data-placeholder="$attrs.placeholder||label"
    v-bind="$attrs"
    v-bind:value="value"
  >
    <option value=""></option>
  </select>
</div>
`
});


// @TODO - fix two-way binding for checkbox
Vue.component('vcinput-checkbox', {
    mixins: [vcinput_mixin],
    model: {
        prop: 'checked',
        event: 'change'
    },
    props: {
        value: {
            default: true
        },
        trueValue: {
            default: null
        },
        falseValue: {
            default: false
        },
        checked: {
            type: [String, Number, Object, Array, Boolean],
            default: null
        }
    },
    mounted() {
        console.log("mounted...");
        console.log(this.checked);
        this.$emit('change', 1);
        this.$emit('input', this.checked);
    },
    template: `
<div class="form-check" v-bind:class="colclass">
  <input
      type="checkbox"
      class="form-check-input"
      v-bind:name="$attrs.name||$attrs.id"
      v-bind="$attrs"
      v-bind:value="value"
      v-on:change="$emit('change', $event.target.checked ? (trueValue ? trueValue : value) : falseValue)"
    >
  <label v-if="label" class="form-check-label" v-bind:for="$attrs.id">{{ label }}</label>
</div>
`
});

Vue.component('vcinput-datepicker', {
  mixins: [vcinput_mixin],
  props: {
      value: {
          type: [String, Date],
          default: ''
      },
      label: {
          type: String,
          default: ''
      },
      dateFormat: {
          type: String,
          default: 'DD/MM/YYYY' // Default format for the datepicker
      }
  },
  template: `
<div class="form-group" v-bind:class="colclass">
<label v-if="label" v-bind:for="$attrs.id">{{ label }}</label>
<div class="input-group">
  <input
    type="text"
    class="form-control"
    v-bind:name="$attrs.name || $attrs.id"
    v-bind:placeholder="$attrs.placeholder || label"
    v-bind="$attrs"
    v-bind:value="dateValue"
    ref="dpinput"
    autocomplete="off"
  >
  <div class="input-group-append">
    <span class="input-group-text">
      <i class="fa fa-calendar"></i>
    </span>
  </div>
</div>
</div>
`,
computed: {
  dateValue() {
      // Return the value formatted according to the specified or default format
      // return this.value ? get_voco_moment(this.value, this.dateFormat).format(this.dateFormat) : '';
      return this.value ? get_voco_moment(this.value, this.dateFormat) : '';
  }
},
mounted() {
  const vm = this;
  const $dp_dom = $("#" + this.$attrs.id);

  // Initialize the datepicker
  $dp_dom.datepicker({
      autoclose: true,
      todayHighlight: true,
      format: this.dateFormat.toLowerCase() === 'dd/mm/yyyy' ? 'dd/mm/yyyy' : 'yyyy-mm-dd' // Set appropriate format
  });

  // Update Vue model when the date is changed via datepicker
  $dp_dom.datepicker().on("changeDate", function(e) {
      const formattedDate = moment(e.date).format('YYYY-MM-DD'); // Use desired format for the model
      vm.$emit("input", formattedDate);
  });

  // Clean up datepicker when the component is destroyed
  this.$once("hook:beforeDestroy", function() {
      $dp_dom.datepicker("destroy");
  });
}
});

// Vue.component('vcinput-datepicker', {
//   mixins: [vcinput_mixin],
//   props: {
//       value: {
//           type: [String, Date],
//           default: ''
//       },
//       label: {
//           type: String,
//           default: ''
//       },
//       dateFormat: {
//           type: String,
//           default: 'YYYY-MM-DD' // Default format for the datepicker (as per your example)
//       }
//   },
//   template: `
// <div class="form-group" v-bind:class="colclass">
//   <label v-if="label" v-bind:for="$attrs.id">{{ label }}</label>
//   <div class="input-group">
//     <input
//       type="text"
//       class="form-control"
//       v-bind:name="$attrs.name || $attrs.id"
//       v-bind:placeholder="$attrs.placeholder || label"
//       v-bind="$attrs"
//       v-bind:value="dateValue"
//       ref="dpinput"
//       autocomplete="off"
//     >
//     <div class="input-group-append">
//       <span class="input-group-text">
//         <i class="fa fa-calendar"></i>
//       </span>
//     </div>
//   </div>
// </div>
// `,
//   computed: {
//     dateValue() {
//       // Format the date using get_voco_moment or return an empty string
//       return this.value ? get_voco_moment(this.value, this.dateFormat) : '';
//     }
//   },
//   mounted() {
//     const vm = this;
//     const $dp_dom = $("#" + this.$attrs.id);

//     // Initialize the datepicker
//     $dp_dom.datepicker({
//       autoclose: true,
//       todayHighlight: true,
//       format: 'yyyy-mm-dd', // Set the date format for the datepicker (YYYY-MM-DD)
//       defaultViewDate: {
//         year: new Date().getFullYear(), // Show the current year
//         month: 0, // Optional: Start from January
//         day: 1   // Optional: Start from the first day of the month
//       }
//     });

//     // Update Vue model when the date is changed via datepicker
//     $dp_dom.datepicker().on("changeDate", function(e) {
//       const formattedDate = moment(e.date).format(vm.dateFormat); // Use the component's dateFormat (YYYY-MM-DD)
//       vm.$emit("input", formattedDate);
//     });

//     // Clean up datepicker when the component is destroyed
//     this.$once("hook:beforeDestroy", function() {
//       $dp_dom.datepicker("destroy");
//     });
//   }
// });




Vue.component('vcinput-flatpickr', {
    mixins: [vcinput_mixin],
    props: {
        value: [String, Date]
    },
    template: `
<div class="form-group" v-bind:class="colclass">
  <label v-if="label" v-bind:for="$attrs.id">{{ label }}</label>
  <div class="input-group" v-bind="$attrs">
    <input
      ref="dpinput"
      type="text"
      class="form-control"
      v-bind:name="$attrs.name||$attrs.id"
      v-bind:placeholder="$attrs.placeholder||label"
      v-bind:value="value"
      v-on:input="$emit('input', $event.target.value)"
      autocomplete="off"
      data-input
    >
    <div class="input-group-append" data-toggle>
      <span class="input-group-text">
        <i class="fa fa-calendar"></i>
      </span>
    </div>
  </div>
</div>
`,
    mounted() {
        var vm = this;

        var flatpickr_dob = flatpickr(vm.$refs.dpinput.parentNode, {
          altInput: vm.$attrs['data-alt-input'] || true,
          altFormat: vm.$attrs['data-alt-format'] || "F j, Y",
          dateFormat: vm.$attrs['data-date-format'] || "Y-m-d",
          wrap: true
        });
    }
});
