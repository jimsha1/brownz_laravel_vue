<div id="vue-list-tab-services">
    <template v-if="services.length">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('SL#') }}</th>
                        <th>{{ __('Service Name') }}</th>
                        <th>{{ __('Status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in services">
                        <td>@{{ index + 1 }}</td>
                        <td>@{{ item.name }}</td>
                        <td><span :style="{'color': item.status == 1 ? 'green' : 'red'}"> @{{ getStatusDisplayValue(item.status) }}</span></td>
                    </tr>
                </tbody>
            </table>
        </div>


    </template>
    <template v-else>
        <div class="alert alert-danger">
            {{ __('No results found.') }}
        </div>
    </template>
</div>

<script>
    var vue_list_tab_services = null;
    jQuery(function($) {
        vue_list_tab_services = new Vue({
            el: "#vue-list-tab-services",
            data: <?php echo json_encode($db_results['vue_data']['vue_list_tab_services']); ?>,
            methods: {
                getStatusDisplayValue(status) {
                    if (status == 1) {
                        return '{{ __("Active") }}';
                    } else {
                        return '{{ __("Inactive") }}';
                    }
                }
            }
        });
    });
</script>
