<section class="content">
  <div class="container-fluid">
    <div class="row" style="margin-top: 10px; margin-right: 5px;">
      <div class="col-sm-6">
        <label>Layout Name</label>
        <input type="hidden" value="{{ request()->segment(3) }}" id="layoutId">
        <input type="hidden" value="{{ route('layouting.show', request()->segment(3)) }}" id="routingName">
        <input type="text" class="form-control" name="name" id="layout-name">
      </div>
      <div class="col-sm-6 d-flex justify-content-start align-items-end">
        <button class="btn btn-primary" id="saveId" style="width: 50% !important;">Save</button>
      </div>
    </div>
    <hr>
    <div class="row d-flex justify-content-center">
      <div class="col-sm-6 d-flex justify-content-end">
        <canvas id="canvasId" width="400" height="800" style="border-style: solid; border-width: thin; border-radius: 15px"></canvas>
      </div>
      <div class="col-sm-6 justify-content-start">
        <div class="row mt-2">
          <div class="col-sm-4 d-flex justify-content-start">
            <button class="btn btn-danger" id="clearId">Clear</button>
          </div>
        </div>
        <div class="row mt-2">
          <div class="col-sm-2 d-flex justify-content-center">
            <button class="d-flex align-items-center justify-content-center" id="squareId" style="background: #FFF; border-width: thin; height: 100px; width: 100px;">
              <div id="squareContent" style="background: #FFF; border-style: solid; border-width: thin; height: 50px; width: 50px;">
              </div>
            </button>
          </div>
          <div class="col-sm-2 d-flex justify-content-center">
            <button class="d-flex align-items-start justify-content-center" id="rectangularId" style="background: #FFF; border-width: thin; height: 100px; width: 100px;">
              <div id="rectangularContent" style="background: #FFF; border-style: solid; border-width: thin; height: 50px; width: 88px;">
              </div>
            </button>
          </div>
          <div class="col-sm-2 d-flex justify-content-center">
            <button class="d-flex align-items-start justify-content-center" id="rectangularRadiusId" style="background: #FFF; border-width: thin; height: 100px; width: 100px;">
              <div id="rectangularContent" style="background: #FFF; border-style: solid; border-width: thin; height: 50px; width: 88px; border-radius: 5px;">
              </div>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

@push('script')
<script type="text/javascript">
  $(document).ready(function() {
    $.ajax({
      url: $('#routingName').val(),
      type: 'POST',
      data: {
        _token: $('meta[name="csrf-token"]').attr('content'),
        id: $('#layoutId').val()
      },
      success: function(res) {
        var canvas = new fabric.Canvas('canvasId')
        if(res.status == 200){
          res.data.app_layout_bodies.map(function(v, k){
            var object = new fabric.Rect({
              left: v.left,
              top: v.top,
              fill: '',
              stroke: v.stroke,
              strokeWidth: v.stroke_width,
              width: v.width,
              height: v.height
            })
            canvas.add(object)
            canvas.renderAll()
          })
        }
      }
    })

    var canvas = new fabric.Canvas('canvasId')
    $('#squareId').on('click', function(){
      var square = new fabric.Rect({
        left: 10,
        top: 10,
        fill: '',
        stroke: 'black',
        strokeWidth: 2,
        width: 100,
        height: 100
      })
      canvas.add(square)
      canvas.renderAll()
    })
    $('#rectangularId').on('click', function(){
      var rectangular = new fabric.Rect({
        left: 10,
        top: 10,
        fill: '',
        stroke: 'black',
        strokeWidth: 2,
        width: 380,
        height: 100
      })
      canvas.add(rectangular)
      canvas.renderAll()
    })
    $('#rectangularRadiusId').on('click', function(){
      var rectangularRadius = new fabric.Rect({
        left: 10,
        top: 10,
        fill: '',
        stroke: 'black',
        strokeWidth: 2,
        width: 380,
        height: 100,
        rx: 15,
        ry: 15
      })
      canvas.add(rectangularRadius)
      canvas.renderAll()
    })
    $('#clearId').on('click', function(){
      canvas._objects.forEach(function(v, k){
        canvas.remove(v)
      })
    })
    $('#saveId').on('click', function(){
      var bodyCanvas = canvas._objects
      var array = []
      bodyCanvas.map(function(v, k){
        array.push({
          left: v.left,
          top: v.top,
          stroke: v.stroke,
          stroke_width: v.strokeWidth,
          width: v.width,
          height: v.height
        })
      })
      console.log(array)
      $.ajax({
        url: "{{ route('layouting.new') }}",
        type: 'POST',
        data: {
          _token: $('meta[name="csrf-token"]').attr('content'),
          bodies: array,
          name: $('#layout-name').val()
        },
        success: function(response){
          console.log(response)
          alert(response.message)
          window.location.replace("{{ route('layouting') }}");
        },
        error: function(error){
          alert(error)
        }
      })
    })
  })
</script>
@endpush