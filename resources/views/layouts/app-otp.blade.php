<!DOCTYPE html>
<html lang="en">

@include('layouts.app-heading')

<body>
    {{-- @include('layouts.app-header') --}}

    <section class="d-flex align-items-center">
        <div class="container">
            <div class="row d-flex justify-content-center align-items-center">
                <div class="col-lg-8 order-1 order-lg-2 hero-img" data-aos="zoom-in" data-aos-delay="200">
                    <div class="card">
                        <div class="card-body">
                            @include('layouts.flash-message')
                            <div class="d-flex justify-content-center align-items-center">
                                <h4 class="registerh3" style="font-weight: bold; text-align: center;">Kami telah mengirimkan otp pada email anda, silahkan menginput otp yang diterima</h4>
                            </div>
                            <!-- <div class="d-flex justify-content-center align-items-center">
                                <h3 class="text-center registerh3" style="font-weight: bold">Validasi OTP</h3>
                            </div> -->
                            <div class="d-flex justify-content-center align-items-center">
                                {{-- <p class="text-center text-muted">Registrasi dan terintegrasi dengan sistem payment mudah dalam satu platform  </p> --}}
                                @if($errors->any())
                                @foreach($errors->all() as $error)
                                <p class="text-center" style="color: red">{{$error}}</p>
                                @endforeach
                                @endif
                            </div>

                            <form action="{{route('otp.validate')}}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-sm-12 input-group">
                                        <input type="text" class="form-control" id="role_id" name="role_id" value="{{ $user->role_id }}" required readonly hidden />
                                        <input type="text" class="form-control" id="refnum" name="refnum" value="{{ $data['REFNUM'] }}" required readonly hidden />
                                        <input type="text" class="form-control text-center" id="otp" name="otp" maxlength="6" placeholder="Ketikan OTP disini.." required />
                                    </div>
                                    <div class="text-center justify-content-center">
                                        <button type="submit" class="btn btn-primary login-btn">Simpan</button>
                                        <a class="btn btn-primary login-btn disabled" id="ulang" onclick="ctrl.countdown()">Kirim Ulang OTP</a>
                                    </div>
                                    <div class="text-center justify-content-center" id="timer" style="font-size:20px;"></div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script type="text/javascript">
        var ctrl = {
            interval: '',
            counter: 0,
            username: '',
            startTimer: function(duration, display) {
                var timer = duration,
                    minutes, seconds;
                ctrl.interval = setInterval(function() {
                    minutes = parseInt(timer / 60, 10)
                    seconds = parseInt(timer % 60, 10);

                    minutes = minutes < 10 ? "0" + minutes : minutes;
                    seconds = seconds < 10 ? "0" + seconds : seconds;

                    display.textContent = minutes + ":" + seconds;

                    if (--timer < 0) {
                        $('#ulang').removeAttr('class');
                        $('#ulang').attr('class', 'btn btn-primary login-btn');
                        timer = duration;
                        clearInterval(ctrl.interval);
                    } else {
                        $('#ulang').removeAttr('class');
                        $('#ulang').attr('class', 'btn btn-primary login-btn disabled');
                    }
                }, 1000);
            },
            countdown: function() {
                if (ctrl.counter == 0) {
                    // ctrl.cekemail();
                    var oneMinute = 90,
                        display = document.querySelector('#timer');
                    ctrl.startTimer(oneMinute, display);
                    ctrl.counter++;
                } else if (ctrl.counter == 1 || ctrl.counter == 2) {
                    ctrl.cekemail();
                    alert('OTP dikirim ulang');
                    var oneMinute = 120,
                        display = document.querySelector('#timer');
                    ctrl.startTimer(oneMinute, display);
                    ctrl.counter++;
                } else {
                    ctrl.cekemail();
                    alert('OTP sudah mencapai maksimal kirim!');
                    location.reload();
                    ctrl.counter++;
                }
            },
            cekemail: function() {
                $.ajax({
                    url: 'http://127.0.0.1:8000/api/gen-otp',
                    data: {
                        "email": "{{ $email }}",
                    },
                    type: 'POST',
                    cache: false,
                    success: function(msg) {
                        if (msg.success) {
                            $('#refnum').val(msg.data.resotp.REFNUM);
                        } else {
                            alert(msg.message);
                        }
                    }
                });
            },
        }

        $(function() {
            ctrl.countdown();
        });
    </script>
</body>

</html>