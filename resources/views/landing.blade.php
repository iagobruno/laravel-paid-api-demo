@extends('layouts.main')

@section('content')
    <h1 class="mb-0 mt-3 py-5 text-center">Comece a usar sem custos financeiros <br> e depois pague de acordo com seu uso
    </h1>

    <section class="features d-flex align-items-start w-75 m-auto mt-4 justify-evenly gap-3">
        <div class="col">
            <h5 class="mb-3">Pague conforme o uso</h5>
            <p>Comece de graça. Cada produto tem um nível gratuito significativo para que você possa se inscrever e criar
                sem atrito.</p>
        </div>
        <div class="col">
            <h5 class="mb-3">Descontos por volume</h5>
            <p>À medida que seu uso aumenta, você obtém automaticamente o preço por volume. Sem necessidade de negociação.
            </p>
        </div>
        <div class="col">
            <h5 class="mb-3">Economize ao se comprometer</h5>
            <p>Receba descontos adicionais para assinaturas anuais.</p>
        </div>
    </section>

    <section class="pricing d-flex w-75 flex-wrap-no m-auto my-5 gap-4 text-center">
        <div class="card box-shadow" style="flex: 1 1 0;">
            <div class="card-header">
                <h4 class="font-weight-normal my-0">Starter</h4>
            </div>
            <div class="card-body">
                <h1 class="card-title pricing-card-title">{{ money(0) }}</h1>
                <ul class="text-start mt-3 mb-4">
                    <li>Até 10 chamadas/mês sem custos financeiros.</li>
                </ul>
                <div class="text-muted mb-2" style="font-size: 0.86em;line-height: normal;">Não é necessário informar dados
                    financeiros.</div>

                <a href="{{ route('auth.github') }}" type="button"
                    class="btn btn-lg btn-block btn-outline-primary">Começar agora</a>
            </div>
        </div>
        <div class="card box-shadow" style="flex: 1 1 0;">
            <div class="card-header">
                <h4 class="font-weight-normal my-0">Pro</h4>
            </div>
            <div class="card-body">
                <h1 class="card-title pricing-card-title">{{ money(4999) }}<small class="text-muted fs-6">/mês</small>
                </h1>
                <ul class="text-start mt-3 mb-4">
                    <li>Até 10 chamadas/mês sem custos financeiros. Depois disso, são cobrados {{ money(25) }}/chamada.
                    </li>
                    <li>Suporte técnico.</li>
                </ul>
                <a href="{{ route('subscribe.show') }}" type="button" class="btn btn-lg btn-block btn-primary">Começar
                    agora</a>
            </div>
        </div>
        <div class="card box-shadow" style="flex: 1 1 0;">
            <div class="card-header">
                <h4 class="font-weight-normal my-0">Enterprise</h4>
            </div>
            <div class="card-body">
                <h1 class="card-title pricing-card-title">Custom</h1>
                <ul class="text-start mt-3 mb-4">
                    <li>Até 100 chamadas/mês sem custos financeiros. Depois disso, são cobrados {{ money(25) }}/chamada.
                    </li>
                    <li>Suporte técnico.</li>
                </ul>
                <button type="button" class="btn btn-lg btn-block btn-outline-primary">Entrar em contato</button>
            </div>
        </div>
    </section>
@endsection
