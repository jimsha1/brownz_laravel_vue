
<div class="fbox-shadow mt-4 fd-card respo-margin-top respo-margin-bottom-vd">
  <div class="card-body ">
    <ul class="nav nav-tabs vc-ajax-nav-tabs br-row-gap-2" role="tablist">
      @foreach($tab_meta_data as $loop_tab_name => $tab_info)
      <li class="nav-item brow-tab-item">
        {!! $tab_info['nav_tab_item_html'] !!}
      </li>
      @endforeach

      @if(count($tab_buttons) > 0)
      <li class="nav-item ml-auto">{!! implode('', $tab_buttons) !!}</li>
      @endif
    </ul>

    <div class="tab-content border-left-0 border-right-0 border-bottom-0">
      @foreach($tab_meta_data as $loop_tab_name => $tab_info)
      {!! $tab_info['tab_pane_html'] !!}
      @endforeach
    </div>
  </div>
</div>
