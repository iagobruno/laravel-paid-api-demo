@extends('layouts.main')

@section('page_title', 'Dashboard')

@section('content')
    <div class="m-auto" style="max-width: 700px">
        <section class="current-subscription py-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="m-0">Chaves de acesso</h4>
                    <p class="text-muted m-0" style="font-size:0.9em">Tokens que você gerou para acessar a API.</p>
                </div>
                <form action="{{ route('new-token.form') }}" method="get">
                    <button type="submit" class="btn btn-primary btn-sm">Criar novo token</button>
                </form>
            </div>

            @if (session('newToken'))
                <div class="alert alert-info px-3 py-2" style="font-size:0.9em">
                    Seu novo token:
                    <div>
                        <input type="text" readonly value="{{ session('newToken') }}" class="border-0 bg-transparent"
                            style="width: min(356px, 100%);" id="new-token-el">
                        <button data-clipboard-target="#new-token-el"
                            class="btn btn-sm btn-outline-secondary">Copiar</button>
                    </div>
                    <hr style="margin: 0.6em 0">
                    <p class="mb-0">Certifique-se de copiar seu token de acesso agora. Você não será capaz
                        de vê-lo novamente!</p>
                </div>
            @endif

            <ul class="list-group">
                @foreach ($tokens as $token)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            {{ $token->name }}
                            <span class="text-muted" style="font-size:0.8em">• Criado em
                                {{ $token->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center gap-2">
                            @if ($token->last_used_at)
                                <span class="text-muted" style="font-size:0.8em">Usado
                                    {{ $token->last_used_at?->diffForHumans() }}</span>
                            @endif
                            <span class="btn btn-sm btn-danger">Revoke</span>
                        </div>
                    </li>
                @endforeach
            </ul>
        </section>

        <section class="current-subscription py-3">
            <h4 class="mb-3">Plano atual</h4>

            <div class="card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <strong class="h4 mb-3">{{ $currentSubscription ? 'Pro' : 'Starter' }}</strong>
                    </div>
                    <div>
                        {{-- <form action="{{ route('subscribe.show') }}" method="POST"> --}}
                        {{-- @csrf --}}
                        <button type="submit" class="btn btn-success">Mudar plano</button>
                        {{-- </form> --}}
                    </div>
                </div>
            </div>
        </section>

        <section class="py-3">
            <h4 class="mb-3">Uso atual deste mês</h4>
            <div class="card">
                @if ($upcomingInvoice)
                    <div class="card-header bg-white">
                        <strong class="fs-5">{{ money($upcomingInvoice->total) }}</strong>
                        <div>{{ number_format($totalUsage, 0, '', '.') }} chamada{{ $totalUsage === 1 ? '' : 's' }} no
                            total
                        </div>
                    </div>
                @endif
                <div class="card-body">
                    <div class="">{{ $usedQuota }} de {{ $freeQuota }} chamadas grátis</div>
                    <div class="progress my-1" style="height:6px">
                        <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0"
                            aria-valuemax="100" style="width: {{ $usedQuotaPercentage }}%"></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="invoices py-3">
            <h4 class="mb-3">Recibos</h4>

            <div class="rounded-2 border px-2">
                @if (count($invoices) >= 1)
                    <table class="table-borderless m-0 table">
                        <thead>
                            <tr>
                                <th scope="col">Data</th>
                                <th scope="col">Preço</th>
                                <th scope="col">Referência</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoices as $invoice)
                                <tr>
                                    <td>{{ $invoice->date()->format('d/m/Y') }}</td>
                                    <td>{{ $invoice->total() }}</td>
                                    <td>{{ $invoice->id }}</td>
                                    <td><a href="{{ $invoice->hosted_invoice_url }}" target="_blank"
                                            rel="noopener noreferrer">Ver recibo</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-muted p-3 text-center">Nenhum recido ainda.</div>
                @endif
            </div>
        </section>
    </div>
@endsection

@push('extra_body')
    <script src="https://unpkg.com/clipboard@2.0.11/dist/clipboard.min.js"></script>
    <script>
        new ClipboardJS('[data-clipboard-target]');
    </script>
@endpush
