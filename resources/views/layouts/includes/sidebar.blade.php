<aside class="main-sidebar sidebar-light-primary elevation-4" style="background: #000000BF !important;">
  <!-- Brand Logo -->
  <a href="{{ asset('/') }}" class="brand-link d-flex justify-content-center" style="border-bottom: 0 !important;">
    <span class="brand-text font-weight-bold" style="color: #FFFFFF;">Laravel Fabric JS</span>
  </a>

  <!-- Sidebar -->
  <div class="sidebar" style="padding: 0px 3px !important;">
    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
        @foreach(Session::get('menus') as $menu)
          @if(intval($menu['type']) == 1)
            <li class="nav-item {{ request()->segment(1) == $menu['slug'] ? 'menu-open' : '' }}">
              <a href="{{ URL::to('/') . '/' . $menu['slug'] }}" class="nav-link">
                <i class="nav-icon {{ $menu['icon'] }}"></i>
                <p class="">
                  {{ $menu['name'] }}
                </p>
              </a>
            </li>
          @else
            @if(count($menu['childs']) > 0)
              <li class="nav-item {{ request()->segment(1) == $menu['slug'] ? 'menu-open' : '' }}">
                <a href="javascript:void(0)" class="nav-link {{ request()->segment(1) == $menu['slug'] ? 'active nav-change-template' : '' }} ">
                  <div class="row">
                    <div class="col-sm-2">
                      <i class="nav-icon {{ $menu['icon'] }}" style="color : {{ request()->segment(1) == $menu['slug'] ? '#FFFFFF' : 'grey' }}"></i>
                    </div>
                    <div class="col-sm-10">
                      <div class="row">
                        <div class="col-sm-9">
                          <p style="color: {{ request()->segment(1) == $menu['slug'] ? '#FFFFFF' : 'grey' }}">
                            {{ $menu['name'] }}
                          </p>
                        </div>
                        <div class="col-sm-3">
                          @if(count($menu['childs']) > 0)
                            <i class="right fas fa-angle-left" style="color: {{ request()->segment(1) == $menu['slug'] ? '#FFFFFF' : 'grey' }}"></i>
                          @endif
                        </div>
                      </div>
                    </div>
                  </div>
                </a>
                <ul class="nav nav-treeview">
                  @foreach($menu['childs'] as $child)
                    @if(count($child['grand_childs']) > 0)
                      <li class="nav-item {{ request()->segment(2) == $child['slug'] ? 'menu-open' : '' }}">
                        <a href="javascript:void(0)" class="nav-link {{ request()->segment(2) == $child['slug'] ? 'active nav-change-template' : '' }} ">
                          <div class="row">
                            <div class="col-sm-3">
                              <i class="nav-icon {{ $child['icon'] }}" style="color : {{ request()->segment(1) == $child['slug'] ? '#FFFFFF' : 'grey' }}; margin-left: 15px"></i>
                            </div>
                            <div class="col-sm-9">
                              <div class="row">
                                <div class="col-sm-9">
                                  <p style="color: {{ request()->segment(2) == $child['slug'] ? '#FFFFFF' : 'grey' }}">
                                    {{ $child['name'] }}
                                  </p>
                                </div>
                                <div class="col-sm-3">
                                  <i class="right fas fa-angle-left" style="color: {{ request()->segment(2) == $child['slug'] ? '#FFFFFF' : 'grey' }}"></i>
                                </div>
                              </div>
                            </div>
                          </div>
                        </a>
                        <ul class="nav nav-treeview">
                          @foreach($child['grand_childs'] as $grand_child)
                            <li class="nav-item {{ request()->segment(3) == $grand_child['slug'] ? 'menu-open' : '' }}">
                              <a href="{{ URL::to('/') . '/' . $menu['slug'] . '/' . $child['slug'] . '/' . $grand_child['slug'] }}" class="nav-link {{ request()->segment(3) == $grand_child['slug'] ? 'active nav-change-template' : '' }}">
                                <div class="row">
                                  <div class="col-sm-3">
                                    <i class="nav-icon {{ $grand_child['icon'] }}" style="color : {{ request()->segment(3) == $grand_child['slug'] ? '#FFFFFF' : 'grey' }}; margin-left: 30px;"></i>
                                  </div>
                                  <div class="col-sm-9">
                                    <p style="color: {{ request()->segment(3) == $grand_child['slug'] ? '#FFFFFF' : 'grey' }}">
                                      {{ $grand_child['name'] }}
                                    </p>
                                  </div>
                                </div>
                              </a>
                            </li>
                          @endforeach
                        </ul>
                      </li>
                    @else
                      <li class="nav-item {{ request()->segment(2) == $child['slug'] ? 'menu-open' : '' }}">
                        <a href="{{ URL::to('/') . '/' . $menu['slug'] . '/' . $child['slug'] }}" class="nav-link {{ request()->segment(2) == $child['slug'] ? 'active nav-change-template' : '' }}">
                          <div class="row">
                            <div class="col-sm-3">
                              <i class="nav-icon {{ $child['icon'] }}" style="color : {{ request()->segment(2) == $child['slug'] ? '#FFFFFF' : 'grey' }}; margin-left: 15px;"></i>
                            </div>
                            <div class="col-sm-9">
                              <p style="color: {{ request()->segment(2) == $child['slug'] ? '#FFFFFF' : 'grey' }}">
                                {{ $child['name'] }}
                              </p>
                            </div>
                          </div>
                        </a>
                      </li>
                    @endif
                  @endforeach
                </ul>
              </li>
            @else
              <li class="nav-item {{ request()->segment(1) == $menu['slug'] ? 'menu-open' : '' }}">
                <a href="{{ url($menu['slug']) }}" class="nav-link {{ request()->segment(1) == $menu['slug'] ? 'active nav-change-template' : '' }} ">
                  <div class="row">
                    <div class="col-sm-2">
                      <i class="nav-icon {{ $menu['icon'] }}" style="color : {{ request()->segment(1) == $menu['slug'] ? '#FFFFFF' : 'grey' }}"></i>
                    </div>
                    <div class="col-sm-10">
                      <div class="row">
                        <div class="col-sm-9">
                          <p style="color: {{ request()->segment(1) == $menu['slug'] ? '#FFFFFF' : 'grey' }}">
                            {{ $menu['name'] }}
                          </p>
                        </div>
                        <div class="col-sm-3">
                          @if(count($menu['childs']) > 0)
                            <i class="right fas fa-angle-left" style="color: {{ request()->segment(1) == $menu['slug'] ? '#FFFFFF' : 'grey' }}"></i>
                          @endif
                        </div>
                      </div>
                    </div>
                  </div>
                </a>
              </li>
            @endif
          @endif
        @endforeach
      </ul>
    </nav>

  </div>
  <!-- /.sidebar -->
</aside>