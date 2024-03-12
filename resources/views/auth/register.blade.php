<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Sign in.</title>
    <link href="{{ asset('assets/tabler/dist/css/tabler.min.css?1684106062') }}" rel="stylesheet" />
    <link href="{{ asset('assets/tabler/dist/css/tabler-flags.min.css?1684106062') }}" rel="stylesheet" />
    <link href="{{ asset('assets/tabler/dist/css/tabler-payments.min.css?1684106062') }}" rel="stylesheet" />
    <link href="{{ asset('assets/tabler/dist/css/tabler-vendors.min.css?1684106062') }}" rel="stylesheet" />
    <link href="{{ asset('assets/tabler/dist/css/demo.min.css?1684106062') }}" rel="stylesheet" />
    <style>
        @import url('https://rsms.me/inter/inter.css');

        :root {
            --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
        }

        body {
            font-feature-settings: "cv03", "cv04", "cv11";
        }
    </style>
    <!-- Scripts -->
    {{-- @vite(['resources/sass/app.scss', 'resources/js/app.js']) --}}
</head>

<body class=" d-flex flex-column">
    <script src="./dist/js/demo-theme.min.js?1684106062"></script>
    <div class="page page-center">
        <div class="container container-normal py-4">
            <div class="row align-items-center g-4">
                <div class="col-lg">
                    <div class="container-tight">
                        <div class="text-center mb-4">
                            <a href="." class="navbar-brand navbar-brand-autodark"><img src="./static/logo.svg"
                                    height="36" alt=""></a>
                        </div>

                        <h2 class="h2 text-center mb-4">Register</h2>
                        <form class="card card-md" action="{{ route('register') }}" method="get" autocomplete="off"
                            novalidate>
                            <div class="card-body">
                                <h2 class="card-title text-center mb-4">Create new account</h2>
                                <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <input id="name" type="text"
                                        class="form-control @error('name') is-invalid @enderror" name="name"
                                        value="{{ old('name') }}" required autocomplete="name" autofocus>

                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email address</label>
                                    <input id="email" type="email"
                                        class="form-control @error('email') is-invalid @enderror" name="email"
                                        value="{{ old('email') }}" required autocomplete="email">

                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <div class="input-group input-group-flat">
                                        <input id="password" type="password"
                                            class="form-control @error('password') is-invalid @enderror" name="password"
                                            required autocomplete="new-password">

                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        <span class="input-group-text">
                                            <a href="#" class="link-secondary" title="Show password"
                                                data-bs-toggle="tooltip"><!-- Download SVG icon from http://tabler-icons.io/i/eye -->
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                    height="24" viewBox="0 0 24 24" stroke-width="2"
                                                    stroke="currentColor" fill="none" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                                    <path
                                                        d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                                </svg>
                                            </a>
                                        </span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-check">
                                        <input type="checkbox" class="form-check-input" />
                                        <span class="form-check-label">Agree the <a href="./terms-of-service.html"
                                                tabindex="-1">terms and
                                                policy</a>.</span>
                                    </label>
                                </div>
                                <div class="form-footer">
                                    <button type="submit" class="btn btn-primary w-100">Create new
                                        account</button>
                                </div>
                            </div>
                        </form>

                        <div class="text-center text-muted mt-3">
                            have account ? <a href="/login" tabindex="-1">Sign in</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg d-none d-lg-block">
                    <img src="{{ asset('assets/tabler/static/illustrations/undraw_secure_login_pdn4.svg') }}"
                        height="300" class="d-block mx-auto" alt="">
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('assets/tabler/dist/js/tabler.min.js?1684106062') }}" defer></script>
    <script src="{{ asset('assets/tabler/dist/js/demo.min.js?1684106062') }}" defer></script>
</body>

</html>
