<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Informasi Manajemen Koperasi Desa Merah Putih') - Simkopdes</title>
    <meta name="description" content="@yield('meta_description', 'Simkopdes - Sistem Informasi Manajemen Koperasi Desa/Kelurahan Merah Putih. Pertanian berkelanjutan dan pemberdayaan ekonomi masyarakat desa.')">
    <link rel="shortcut icon" href="{{ asset('favicon.png') }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    @yield('styles')
</head>
<body>

    @include('partials.navbar')

    @yield('content')

    @include('partials.footer')

    <!-- Registration Modal -->
    <div class="modal-wrapper" id="registerModal">
        <div class="modal-box">
            <button class="modal-close" id="closeRegisterModal">&times;</button>
            <div class="modal-header">
                <h3>Form Pendaftaran Anggota</h3>
                <p>Isi formulir pendaftaran anggota Koperasi Merah Putih di bawah ini.</p>
            </div>
            <div style="text-align:center;padding:10px;">
                <a href="{{ route('daftar') }}" class="btn btn-primary" style="border-radius:10px;">Ke Halaman Daftar</a>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/script.js') }}"></script>
    @yield('scripts')

    <!--Start of Tawk.to Script-->
    <script type="text/javascript">
    var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
    (function(){
    var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
    s1.async=true;
    s1.src='https://embed.tawk.to/6a4e1877751e041d46cbe0a2/1jt0gv55g';
    s1.charset='UTF-8';
    s1.setAttribute('crossorigin','*');
    s0.parentNode.insertBefore(s1,s0);
    })();
    </script>
    <!--End of Tawk.to Script-->
</body>
</html>
