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
                <h3 class="text-center registerh3" style="font-weight: bold">Ubah Password</h3>
              </div>
              <div class="d-flex justify-content-center align-items-center">
                {{-- <p class="text-center text-muted">Registrasi dan terintegrasi dengan sistem payment mudah dalam satu platform  </p> --}}
                @if($errors->any())
                @foreach($errors->all() as $error)
                <p class="text-center" style="color: red">{{$error}}</p>
                @endforeach
                @endif
              </div>

              <form action="{{route('update.password')}}" method="post">
                @csrf
                <input type="hidden" class="form-control" name="user_id" value="{{$user->user_id}}" readonly required />
                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label class="form-label-bold">Nama</label>
                      <input type="text" class="form-control" value="{{$user->name}}" readonly required />
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label class="form-label-bold">Email</label>
                      <input type="text" class="form-control" value="{{$user->email}}" readonly required />
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <label class="form-label-bold">Password</label>
                    <div class="form-group input-group">
                      <input type="password" class="form-control" name="password" id="password" required onkeyup="CheckPassword(this)" />
                      <div class="input-group-append">
                        <span class="input-group-text" onclick="password_show_hide();">
                          <i class="fas fa-eye" id="show_eye"></i>
                          <i class="fas fa-eye-slash d-none" id="hide_eye"></i>
                        </span>
                      </div>
                    </div>
                  </div>
                  <div id="passwordValidation" style="color:red"></div>
                </div>
                <div class="text-center justify-content-center">
                  <button type="submit" class="btn btn-primary login-btn" id="submit" disabled>Ubah Password </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>

  </section>
  <script type="text/javascript">
    // $(document).ready(function() {

    // })

    function CheckPassword(inputtxt) {
      p = document.getElementById("password").value;
      errors = [];
      if (p.length < 6) {
        errors.push("- Panjang password minimal 6 karakter");
        // errors.push("- Your password must be at least 6 characters");
      }
      // if (p.length > 32) {
      //   errors.push("- Your password must be at max 32 characters");
      // }
      if (p.search(/[a-z]/) < 0) {
        errors.push("- Password harus mengandung minimal satu huruf kecil");
        // errors.push("- Your password must contain at least one lower case letter.");
      }
      if (p.search(/[A-Z]/) < 0) {
        errors.push("- Password harus mengandung minimal satu huruf kapital");
        // errors.push("- Your password must contain at least one upper case letter.");
      }

      if (p.search(/[0-9]/) < 0) {
        errors.push("- Password harus mengandung minimal satu angka");
        // errors.push("- Your password must contain at least one digit.");
      }
      // if (p.search(/[!@#\$%\^&\*_]/) < 0) {
      //   errors.push("Your password must contain at least special char from -[ ! @ # $ % ^ & * _ ]");
      // }
      if (errors.length > 0) {
        document.getElementById("password").focus();
        $("#passwordValidation").html(errors.join("<br>"));
        $("#submit").attr("disabled", "disabled");
        return false;
      } else {
        $("#passwordValidation").html("");
        $("#submit").removeAttr("disabled");
        return true;
      }
    }

    function password_show_hide() {
      var x = document.getElementById("password");
      var show_eye = document.getElementById("show_eye");
      var hide_eye = document.getElementById("hide_eye");
      hide_eye.classList.remove("d-none");
      if (x.type === "password") {
        x.type = "text";
        show_eye.style.display = "none";
        hide_eye.style.display = "block";
      } else {
        x.type = "password";
        show_eye.style.display = "block";
        hide_eye.style.display = "none";
      }
    }
  </script>
</body>

</html>