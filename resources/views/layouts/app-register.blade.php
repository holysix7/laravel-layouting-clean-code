<!DOCTYPE html>
<html lang="en">

@include('layouts.app-heading')

<body>
  <style>
    /* Chrome, Safari, Edge, Opera */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
      -webkit-appearance: none;
      margin: 0;
    }

    /* Firefox */
    input[type=number] {
      -moz-appearance: textfield;
    }
  </style>
  @include('layouts.app-header')

  <section class="d-flex align-items-center">
    <div class="container">
      <div class="row d-flex justify-content-center align-items-center">
        <div class="col-lg-8 order-1 order-lg-2 hero-img" data-aos="zoom-in" data-aos-delay="200">
          <div class="card">
            <div class="card-body">
              @include('layouts.flash-message')
              <div class="d-flex justify-content-center align-items-center">
                <h3 class="text-center registerh3" style="font-weight: bold">Registrasi Merchant</h3>
              </div>
              <div class="d-flex justify-content-center align-items-center">
                <p class="text-center text-muted">Registrasi dan terintegrasi dengan sistem payment mudah dalam satu platform </p>
              </div>

              @if (count($errors) > 0)
              @foreach($errors->all() as $error)
              <div class="alert alert-danger">{{ $error }} </div>
              @endforeach
              @endif
              @if ($message = Session::get('success'))
              <div class="alert alert-success  alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
              </div>
              @endif

              <form action="{{asset('/processing-register')}}" method="post">
                @csrf
                <!-- <div class="row">
                  <h5 style="font-weight: bold">Isi data merchant</h5>
                  <div class="col-sm-4">
                    <label class="form-label-bold">Daftar Sebagai</label>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-6">
                    <select class="form-control" name="">
                      <option>Pilih</option>
                      <option>Perusahaan</option>
                    </select>
                  </div>
                </div> -->
                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label class="form-label-bold">Nama Merchant</label>
                      <input type="text" class="form-control" name="merchant_name" required />
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label class="form-label-bold">Nama Pemilik</label>
                      <input type="text" class="form-control" name="owner_name" required />
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label class="form-label-bold">Email</label>
                      <input type="email" class="form-control" name="email" required />
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label class="form-label-bold">Nama Pengguna</label>
                      <input type="text" class="form-control" name="username" required />
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label class="form-label-bold">No Telepon</label>
                      <input type="text" class="form-control phone-number" name="phone" maxlength="15" onkeypress="return event.charCode>=48 && event.charCode<=57" required />
                    </div>
                  </div>
                  <div class="col-sm-12">
                    <div class="form-group">
                      <label class="form-label-bold">Alamat Lengkap</label>
                      <input type="text" class="form-control" name="address" required />
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="form-label-bold">Kelurahan</label>
                      <select class="form-control" id="subdistrict_id" name="subdistrict_id">
                        <option value=""></option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="form-label-bold">Kecamatan</label>
                      <select class="form-control select2" id="district_id" name="district_id">
                        <option value=""></option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="form-label-bold">Kabupaten/Kota</label>
                      <select class="form-control select2" id="city_id" name="city_id">
                        <option value=""></option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="form-label-bold">Provinsi</label>
                      <select class="form-control select2" id="province_id" name="province_id">
                        <option value=""></option>
                      </select>
                    </div>
                  </div>
                </div>
                <!-- <div class="row" style="margin-top: 10px;">
                  <div class="col-sm-6">
                    <span id="captcha-img">
                      {!!captcha_img('custom')!!}
                    </span>
                    <button class="btn btn-light" id="reload"><i class="fa fa-refresh"></i></button>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-6">
                    <div class="input-group mb-3">
                      <input type="text" class="form-control" id="captcha_form" name="captcha" placeholder="Jawaban" />
                    </div>
                  </div>
                </div> -->
                <div class="row" style="padding-top: 10px;">
                  <div class="col">
                    <input type="checkbox" name="terms" id="terms" required disabled /> Saya telah menyetujui <a href="javascript:void(0)" data-toggle="modal" data-target="#tncModal">syarat dan ketentuan</a>&nbsp; yang berlaku
                  </div>
                </div>
                <div class="text-center justify-content-center">
                  <button type="submit" class="btn btn-primary login-btn" id="btn_reg" disabled>Registrasi Sekarang </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="tncModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Syarat dan Ketentuan</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="terms_body" style="overflow-y: auto; height: 500px">
            Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.
            <br>
            Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.
            <br>
            Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.
            <br>
            Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.
            <br>
            Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.

            <!-- <p>Pertanyaan ini untuk membuktikan apakah Anda adalah pengunjung manusia atau bukan dan untuk mencegah pengiriman spam otomatis.<br> Pertanyaan Matematika</p>
              <span id="captcha-img">
              {!!captcha_img('custom')!!}
            </span>
            <button class="btn btn-light" id="reload"><i class="fa fa-refresh"></i></button>
            <div class="input-group mb-3">
              <input type="text" class="form-control" id="captcha_form_tnc" name="captcha_tnc" placeholder="Jawaban" />
            </div>
            <hr>
            <div class="text-right">
              <button class="btn btn-primary login-btn" id="captcha_save">Save Captcha</button>
            </div> -->
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
          </div>
        </div>
      </div>
    </div>
  </section>

  <script src="{{ asset('adminlte/plugins/select2/js/select2.full.min.js') }} "></script>
  <script type="text/javascript">
    $("#terms_body").scroll(function() {
      buffer = 40 // # of pixels from bottom of scroll to fire your function. Can be 0
      if ($("#terms_body").prop('scrollHeight') - $("#terms_body").scrollTop() <= $("#terms_body").height() + buffer) {
        document.getElementById("terms").checked = true;
        $('#terms').removeAttr('disabled')
        $('#btn_reg').removeAttr('disabled')
      }
    });

    $(document).ready(function() {

      // $("#captcha_save").on('click', function() {
      // $("#captcha_id").val($("#captcha_form").val())
      // $("#captcha_id").val($("#captcha_form_tnc").val())
      // $("#captcha_img_subm").append($('#captcha-img'))
      // $('#tncModal').modal('hide')
      // })
      // $('#reload').on('click', function(e) {
      //   e.preventDefault();
      //   $.ajax({
      //     url: "/reload",
      //     type: 'GET',
      //     success: function(response) {
      //       $('#captcha-img').html(response.captcha)
      //     }
      //   })
      // })

      $('.select2').select2({});

      $("#subdistrict_id").select2({
        ajax: {
          url: 'https://jmto.onbilling.id/api/wilayah',
          dataType: 'json',
          type: 'POST',
          delay: 250,
          data: function(params) {
            return {
              "provinsi": "",
              "kab_kota": "",
              "kecamatan": "",
              "kelurahan": "",
              "wilayah": params.term,
            };
          },
          processResults: function(res, params) {
            if (res.success) {
              data = res.data;
            } else {
              data = [];
            }
            let mappedData = []
            if (data.length > 0) {
              mappedData = data.map((item) => {
                return {
                  id: item.subdistrict_id,
                  text: item.subdistrict_name,
                  res: item
                }
              })
            }
            return {
              results: mappedData,
            };
          },
          cache: true
        },
        minimumInputLength: 3,
        templateResult: formatRepo,
        templateSelection: formatRepoSelection,
      }).on('select2:select', function(e) {
        const data = e.params.data.res;
        $("#district_id").append('<option value="' + data.district_id + '" selected="selected">' + data.district_name + '</option>');
        $("#city_id").append('<option value="' + data.city_id + '" selected="selected">' + data.city_name + '</option>');
        $("#province_id").append('<option value="' + data.province_id + '" selected="selected">' + data.province_name + '</option>');
      });

      $('#subdistrict_id').data().select2.on("focus", function(e) {
        $('#subdistrict_id').select2("open");
        $('.select2-search__field')[0].focus();
      });

      // provinces()
      // $('#cities').prop('disabled', true)
      // $('#districts').prop('disabled', true)
      // $('#subdistricts').prop('disabled', true)
      // $('#pronvices').on('change', function() {
      //   if ($(this).val() != "0") {
      //     $('#cities').prop('disabled', false)
      //     $('#cities').html("")
      //     var id = parseInt($(this).val())
      //     $('#cities').append(`<option value="0">Pilih</option>`)
      //     // console.log(id)
      //     cities(id)
      //   } else {
      //     $('#cities').prop('disabled', true)
      //   }
      // })
      // $('#cities').on('change', function() {
      //   if ($(this).val() != "0") {
      //     $('#districts').prop('disabled', false)
      //     $('#districts').html("")
      //     var id = parseInt($(this).val())
      //     $('#districts').append(`<option value="0">Pilih</option>`)
      //     // console.log(id)
      //     districts(id)
      //   } else {
      //     $('#districts').prop('disabled', true)
      //   }
      // })
      // $('#districts').on('change', function() {
      //   if ($(this).val() != "0") {
      //     $('#subdistricts').prop('disabled', false)
      //     $('#subdistricts').html("")
      //     var id = parseInt($(this).val())
      //     $('#subdistricts').append(`<option value="0">Pilih</option>`)
      //     // console.log(id)
      //     subdistricts(id)
      //   } else {
      //     $('#subdistricts').prop('disabled', true)
      //   }
      // })
    })

    // function provinces() {
    //   $.ajax({
    //     url: "/province",
    //     type: 'GET',
    //     success: function(response) {
    //       var html = []
    //       $.map(response.records, function(v) {
    //         raw = `<option value="${v.province_id}">${v.name}</option>`
    //         html.push(raw)
    //       })
    //       $('#pronvices').append(html)
    //     },
    //     error: function(e) {
    //       console.log(e)
    //     }
    //   })
    // }

    // function cities(id) {
    //   $.ajax({
    //     url: "/city",
    //     type: 'POST',
    //     data: {
    //       _token: $('meta[name="csrf-token"]').attr('content'),
    //       province_id: id
    //     },
    //     success: function(response) {
    //       var html = []
    //       $.map(response.records, function(v) {
    //         raw = `<option value="${v.city_id}">${v.name}</option>`
    //         html.push(raw)
    //       })
    //       $('#cities').append(html)
    //     },
    //     error: function(e) {
    //       console.log(e)
    //     }
    //   })
    // }

    // function districts(id) {
    //   $.ajax({
    //     url: "/district",
    //     type: 'POST',
    //     data: {
    //       _token: $('meta[name="csrf-token"]').attr('content'),
    //       city_id: id
    //     },
    //     success: function(response) {
    //       var html = []
    //       $.map(response.records, function(v) {
    //         raw = `<option value="${v.district_id}">${v.name}</option>`
    //         html.push(raw)
    //       })
    //       $('#districts').append(html)
    //     },
    //     error: function(e) {
    //       console.log(e)
    //     }
    //   })
    // }

    // function subdistricts(id) {
    //   $.ajax({
    //     url: "/subdistrict",
    //     type: 'POST',
    //     data: {
    //       _token: $('meta[name="csrf-token"]').attr('content'),
    //       city_id: id
    //     },
    //     success: function(response) {
    //       var html = []
    //       $.map(response.records, function(v) {
    //         raw = `<option value="${v.subdistrict_id}">${v.name}</option>`
    //         html.push(raw)
    //       })
    //       $('#subdistricts').append(html)
    //     },
    //     error: function(e) {
    //       console.log(e)
    //     }
    //   })
    // }

    function formatRepo(repo) {
      if (repo.loading) {
        return repo.text;
      }
      var $container = $(`
			<div>
			<p class="m-0">${repo.res.subdistrict_name}, ${repo.res.district_name}, ${repo.res.city_name}, ${repo.res.province_name}</p>
			</div>
			`);
      return $container;

    }

    function formatRepoSelection(repo) {
      return repo.subdistrict_name || repo.text;
    }
  </script>
</body>

</html>