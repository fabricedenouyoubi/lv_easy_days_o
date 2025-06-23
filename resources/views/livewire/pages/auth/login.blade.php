<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.auth-layout')]
class extends Component {
    public LoginForm $form;

    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: false);
    }
};
?>
<div class="auth-page">
    <div class="container-fluid p-0">
        <div class="row g-0 align-items-center">
            <div class="col-xxl-4 col-lg-4 col-md-6">
                <div class="row justify-content-center g-0">
                    <div class="col-xl-9">
                        <div class="p-4">
                            <div class="card mb-0">
                                <div class="card-body">
                                    <div class="auth-full-page-content rounded d-flex p-3 my-2">
                                        <div class="w-100">
                                            <div class="d-flex flex-column h-100">
                                                <div class="mb-4 mb-md-3">
                                                    <a href="{{ route('login') }}" class="d-block auth-logo">
                                                        <img src="{{ asset('assets/borex/images/logo-gcs.png') }}"
                                                            alt="" height="22"
                                                            class="auth-logo-dark me-start">
                                                        <img src="{{ asset('assets/borex/images/logo-gcs.png') }}"
                                                            alt="" height="22"
                                                            class="auth-logo-light me-start">
                                                    </a>
                                                </div>
                                                <div class="auth-content my-auto">
                                                    <div class="text-center">
                                                        <h5 class="mb-0">TCRI - GCS ChronoTemps</h5>
                                                        <p class="text-muted mt-2">Maîtrisez votre temps,
                                                            libérez votre potentiel
                                                        </p>
                                                    </div>

                                                    @error('form.error')
                                                        <div class="alert alert-danger fade show text-justify"
                                                            role="alert">
                                                            <span>{{ $message }}</span>
                                                        </div>
                                                    @enderror

                                                    <form class="mt-4 pt-2" wire:submit.prevent="login">
                                                        <div id="div_id_login" class="mb-3"> <label for="id_login"
                                                                class="form-label requiredField">
                                                                Email<span class="asteriskField text-danger">*</span>
                                                            </label> <input type="email" name="login"
                                                                wire:model="form.email" autocomplete="email"
                                                                class="form-control @error('form.email') is-invalid @enderror">

                                                            @error('form.email')
                                                                <div class="invalid-feedback">
                                                                    {{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>

                                                        <div id="div_id_password" class="mb-2"> <label
                                                                for="id_password" class="form-label requiredField">
                                                                Mot de Passe<span class="asteriskField text-danger">*</span>
                                                            </label> <input type="password" name="password"
                                                                wire:model="form.password" autocomplete="password"
                                                                class="form-control @error('form.password') is-invalid @enderror">
                                                            @error('form.password')
                                                                <div class="invalid-feedback">
                                                                    {{ $message }}
                                                                </div>
                                                            @enderror

                                                        </div>

                                                        <div class="row mb-3" role="button">
                                                            <div class="col">
                                                                <div class="form-text">
                                                                    <a class=""
                                                                        href="{{ route('password.request') }}"
                                                                        {{-- wire:navigate --}}>
                                                                        Mot de passe Oublié ?
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row mb-3">
                                                            <div class="col">
                                                                <div class="form-check font-size-15">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        id="remember-check" wire:model="form.remember">
                                                                    <label class="form-check-label font-size-13"
                                                                        for="remember-check">
                                                                        Se souvenir de moi
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <button
                                                                class="btn btn-primary w-100 waves-effect waves-light"
                                                                type="submit">Se connecter</button>
                                                        </div>
                                                    </form>
                                                </div>
                                                <div class="mt-2 text-center">
                                                    <p class="mb-0">© 2025 ChronoTemps - par GCS
                                                        Technologie
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end auth full page content -->
            </div>
            <!-- end col -->
            <div class="col-xxl-8 col-lg-8 col-md-6">
                <div class="auth-bg bg-white py-md-5 p-4 d-flex">
                    <div class="bg-overlay bg-white"></div>
                    <!-- end bubble effect -->
                    <div class="row justify-content-center align-items-center">
                        <div class="col-xl-8">
                            <div class="mt-4">
                                <img src="{{ asset('assets/borex/images/login-img.png') }}" class="img-fluid"
                                    alt="">
                            </div>
                            <div class="p-0 p-sm-4 px-xl-0 py-5">
                                <div id="reviewcarouselIndicators" class="carousel slide auth-carousel"
                                    data-bs-ride="carousel">
                                    <div class="carousel-indicators carousel-indicators-rounded">
                                        <button type="button" data-bs-target="#reviewcarouselIndicators"
                                            data-bs-slide-to="0" class="active" aria-current="true"
                                            aria-label="Slide 1"></button>
                                        <button type="button" data-bs-target="#reviewcarouselIndicators"
                                            data-bs-slide-to="1" aria-label="Slide 2"></button>
                                        <button type="button" data-bs-target="#reviewcarouselIndicators"
                                            data-bs-slide-to="2" aria-label="Slide 3"></button>
                                    </div>

                                    <!-- end carouselIndicators -->
                                    <div class="carousel-inner w-75 mx-auto">
                                        <div class="carousel-item active">
                                            <div class="testi-contain text-center">
                                                <h5 class="font-size-20 mt-4">“Libérez votre temps, libérez
                                                    votre potentiel”
                                                </h5>
                                                <p class="font-size-15 text-muted mt-3 mb-0">Découvrez une
                                                    nouvelle façon de gérer votre temps et de maximiser votre
                                                    productivité. Notre application vous aide à organiser vos
                                                    journées, à suivre vos progrès et à atteindre vos objectifs,
                                                    sans stress.</p>
                                            </div>
                                        </div>

                                        <div class="carousel-item">
                                            <div class="testi-contain text-center">
                                                <h5 class="font-size-20 mt-4">“Votre temps, votre tableau de
                                                    bord, votre réussite”</h5>
                                                <p class="font-size-15 text-muted mt-3 mb-0">
                                                    Transformez le chaos en clarté. Avec des fonctionnalités
                                                    intuitives, notre application vous permet de planifier vos
                                                    tâches, de définir des priorités et de visualiser votre
                                                    emploi du temps en un clin d'œil.
                                                </p>
                                            </div>
                                        </div>

                                        <div class="carousel-item">
                                            <div class="testi-contain text-center">
                                                <h5 class="font-size-20 mt-4">“Chaque minute compte, chaque
                                                    objectif atteint”</h5>
                                                <p class="font-size-15 text-muted mt-3 mb-0">
                                                    Visualisez vos progrès, célébrez vos succès et restez motivé
                                                    tout au long de votre parcours. Notre application vous aide
                                                    à suivre vos réalisations, à identifier vos forces et à
                                                    continuer à progresser.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end carousel-inner -->
                                </div>
                                <!-- end review carousel -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end col -->
        </div>
        <!-- end row -->
    </div>
    <!-- end container fluid -->
</div>
