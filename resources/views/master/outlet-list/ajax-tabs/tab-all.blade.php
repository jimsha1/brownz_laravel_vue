<div id="vue-list-tab-all"class="m-n1">
    <template v-if="paginator.db_rows.length" >
        <div class="table-responsive">
            <table class="table brownz-table-list-v1">
                <thead>
                    <tr>
                        <th>{{ __('SL#') }}</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Location</th>
                        <th>City</th>
                        <th>Status</th>
                        <th class="hidden-print no-sort">Manage</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in paginator.db_rows">
                        <td>@{{ index + paginator.sequence_start }}</td>
                        <td>
                            <a v-bind:href="item.edit_url">
                                @{{ item.name }}
                            </a>
                        </td>
                        <td>@{{ item.phone }}</td>
                        <td>@{{ item.email }}</td>
                        <td>@{{ item.location }}</td>
                        <td>@{{ item.city }}</td>
                        <td>
                            <span v-if="item.is_active" class="badge badge-success">{{ __('Active') }}</span>
                            <span v-else class="badge badge-warning">{{ __('Inactive') }}</span>
                        </td>
                        <td class="hidden-print">
                            <a v-bind:href="item.edit_url">
                                <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="tab-pagination" v-html="paginator.links">
        </div>
    </template>
    <template v-else>
        <div class="alert alert-danger">
            {{ __('No results found.') }}
        </div>
    </template>
</div>

<script>
    var vue_list_tab_all = null;
    jQuery(function($) {
        vue_list_tab_all = new Vue({
            el: "#vue-list-tab-all",
            data: <?php echo json_encode($db_results['vue_data']['vue_list_tab_all']); ?>
        });
    });
</script>
