<div class="row">
  <div class="col-md-6">
    @foreach(session('menus') as $menu)
      @if(count($menu['childs']) > 0)
        @foreach($menu['childs'] as $child)
          @if(request()->segment(2) == $child['slug'])
            <h4><b>{{$child['description']}}</b></h4>
          @endif
        @endforeach
        @if(request()->segment(1) == $menu['slug'])
          <p class="text-muted">{{$menu['description']}}</p>
        @endif
      @else
        @if(request()->segment(1) == $menu['slug'])
          <h4><b>{{$menu['description']}}</b></h4>
        @endif
      @endif
    @endforeach
  </div>
  <div class="col-md-6 d-flex justify-content-end align-items-center">
    @if(request()->segment(3))
      @foreach(session('menus') as $menu)
        @foreach($menu['childs'] as $child)
          @if(request()->segment(1) == 'autodebit' || request()->segment(1) == 'informasi-notifikasi')
            @if(request()->segment(2) == $child['slug'])
              @foreach($child['grand_childs'] as $grandChild)
                @if(request()->segment(3) == $grandChild['slug'])
                  @if(request()->segment(4))
                    <a href="{{ url('/' . $menu['slug'] . '/' . $child['slug']) . '/' . $grandChild['slug']}}" class="btn btn-primary-outline" style="font-size: 18px; font-weight: bold;padding: 12px; padding-left: 25px; padding-right: 25px; background: #f4f6f9 !important; border-color: #f4f6f9 !important;"><i class="fas fa-angle-left">&nbsp;Kembali</i></a>
                  @else
                    <a href="{{ url('/' . $menu['slug'] . '/' . $child['slug'] . '/' . $grandChild['slug'] . '/new') }}" class="btn btn-primary btn-template-tambah" id="buttonTambah">
                      Tambah
                      {{$child['description']}}
                    </a>
                  @endif
                @endif
              @endforeach
            @endif
          @else
            @if(request()->segment(2) == $child['slug'])
              @if(request()->segment(3))
                <a href="{{ url('/' . $menu['slug'] . '/' . $child['slug'])}}" class="btn btn-primary-outline" style="font-size: 18px; font-weight: bold;padding: 12px; padding-left: 25px; padding-right: 25px; background: #f4f6f9 !important; border-color: #f4f6f9 !important;"><i class="fas fa-angle-left">&nbsp;Kembali</i></a>
              @else
                <a href="{{ url('/' . $menu['slug'] . '/' . $child['slug'] . '/new') }}" class="btn btn-primary btn-template-tambah" id="buttonTambah">
                  Tambah
                  {{$child['description']}}
                </a>
              @endif
            @endif
          @endif
        @endforeach
      @endforeach
    @endif
  </div>
</div>
