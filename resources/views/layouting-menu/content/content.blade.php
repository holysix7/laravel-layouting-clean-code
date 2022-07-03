<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="card col-sm-12">
        @foreach(session('menus') as $menu)
          @foreach(session('permissions') as $permission)
            @if(request()->segment(1) == $menu['slug'])
              @if($permission['permission_id'] == 2)
                @if($permission['application_id'] == $menu['id'])
                  @if($permission['isactive'] == true)
                    <div class="row mt-2">
                      <div class="col-sm-12 d-flex justify-content-end">
                        <a href="{{ route('layouting.new') }}" class="btn btn-success btn-template-tambah">
                          <i class="mdi mdi-plus"></i>
                          New {{$menu['description']}}
                        </a>
                      </div>
                    </div>
                  @endif
                @endif
              @endif
            @endif
          @endforeach
        @endforeach
        <div class="card-body">
          <table id="fikri-request" class="table table-bordered table-striped"></table>
        </div>
        <!-- /.card-body -->
      </div>
    </div>
  </div>
</section>

@push('script')
<script type="text/javascript">
  $(document).ready(function() {
    var table = $('#fikri-request').DataTable({
      serverSide: true,
      ajax: {
        url: "{{ route('layouting') }}",
        type: 'POST',
        data: {
          _token: $('meta[name="csrf-token"]').attr('content')
        }
      },
      paging: true,
      lengthChange: true,
      searching: false,
      ordering: false,
      info: true,
      autoWidth: false,
      responsive: true,
      dom: '<"top"fB>rt<"bottom"lip><"clear">',
      processing: true,
      buttons: [],
      columns: [{
          title: "No",
          width: "5%",
          data: 'rownum',
          mRender: function(data, type, row) {
            return row.rownum;
          }
        },
        {
          title: "Layouts Name",
          data: 'name',
          width: "15%"
        },
        {
          title: "Created By",
          data: 'created_by_name',
          width: "15%"
        },
        {
          title: "Created At",
          data: 'created_at',
          width: "10%"
        },
        {
          title: "Updated By",
          data: 'updated_by_name',
          width: "15%"
        },
        {
          title: "Updated At",
          data: 'updated_at',
          width: "10%"
        },
        {
          title: "Status",
          data: 'isactive',
          width: "10%",
          mRender: function(data, type, row){
            if(row.isactive == true){
              var alert   = 'badge-success'
              var status  = 'Active'
            }else{
              var alert   = 'badge-danger'
              var status  = 'Suspend'
            }
            return `<span class="badge ${alert} status_span" style="padding-left: 5px;">${status}</span>`
          }
        },
        {
          class: "text-center details-control",
          data: "id",
          orderable: false,
          width: "7%",
          title: "Action",
          mRender: function(data, type, row) {
            return `<a href="${row.routeshow}" class="button-action" style='font-size: 28px;'><i class="mdi mdi-eye"></i></a>`
          }
        },
      ],
    })
  })
</script>
@endpush