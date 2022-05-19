@extends('layouts.main')

@section('page_title', 'Criar novo token de acesso')

@section('content')
    <div class="m-auto py-3" style="max-width: 700px">
        <h4 class="mb-3">Criar novo token de acesso</h4>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('new-token') }}" method="post">
                    @csrf
                    <div class="mb-3">
                        <label for="token-name" class="form-label">Nome</label>
                        <input type="text" name="token-name" class="form-control @error('token-name') is-invalid @enderror"
                            id="token-name" maxlength="255" autofocus>
                        @error('token-name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Criar token</button>
                </form>
            </div>
        </div>
    </div>
@endsection
