@extends('layouts.main')

@section('page_title', 'Assinar')

@section('content')
    <form method="POST" action="{{ route('subscribe.handle') }}" id="subscribe-form">
        @csrf

        @if ($errors->any())
            <div class="m-auto mt-5" style="max-width: 620px">
                @foreach ($errors->all() as $error)
                    <x-alert-flash message="{{ $error }}" type="danger" dismissible="true" class="mb-2" />
                @endforeach
            </div>
        @endif

        <header class="pt-5 pb-4 text-center">
            <h2 class="h1">Assinar plano Pro por {{ money(4999) }}</h2>
            <p class="text-muted">Informe os dados do seu cartão de crédito.</p>
        </header>

        <section class="m-auto" style="width: min(500px, 100%);">
            <div id="setup-element">
                Carregando...
                <!--Stripe.js injects the Payment Element-->
            </div>

            <button id="submit-button" class="btn btn-primary btn-lg w-100 mt-4">ASSINAR</button>
            <small class="d-block text-muted mt-3 text-center">Ao assinar você concorda com nossos termos de serviço.</small>
        </section>
    </form>
@endsection

@push('extra_body')
    <script src="https://js.stripe.com/v3/"></script>
    <script type="application/json" id="stripe-keys">
        {
            "setup_intent_secret": "{{ $setupIntent?->client_secret }}",
            "stripe_pub_key": "{{ env('STRIPE_KEY') }}"
        }
    </script>
    <script>
        const stripeKeys = JSON.parse(document.getElementById("stripe-keys").innerText);
        const stripe = Stripe(stripeKeys.stripe_pub_key);

        const stripeElements = stripe.elements({
            clientSecret: stripeKeys.setup_intent_secret,
        });

        const paymentElement = stripeElements.create("payment");
        paymentElement.mount("#setup-element");

        const form = document.querySelector("#subscribe-form");
        const submitButton = form.querySelector("#submit-button");
        submitButton.addEventListener("click", handleSubmit);

        async function handleSubmit(e) {
            e.preventDefault();
            e.stopPropagation();
            submitButton.setAttribute("disabled", true);

            const {
                setupIntent,
                error
            } = await stripe
                .confirmSetup({
                    elements: stripeElements,
                    redirect: "if_required",
                    confirmParams: {
                        return_url: "https://example.com/account/payments/setup-complete",
                    },
                })
                .finally(() => {
                    submitButton.removeAttribute("disabled");
                });

            if (error) {
                // This point will only be reached if there is an immediate error when
                // confirming the payment. Show error to your customer (for example, payment
                // details incomplete)
                return alert(error.message);
            }
            console.log("confirmSetup return:", setupIntent);

            const pmInput = document.createElement("input");
            pmInput.type = "hidden";
            pmInput.name = "payment_method";
            pmInput.value = setupIntent.payment_method;
            form.append(pmInput);
            form.submit();
        }
    </script>
@endpush
