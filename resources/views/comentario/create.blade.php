
<form action="{{ route('comentario.store') }}" method="post">
    @csrf
    <input type="hidden" name="idvacation" value="{{ $vacation->id }}">
    
    <div class="upper-space mb-3" style="padding-top: 16px;">
        @error('content')
        <div class="alert alert-danger">{{ $message }}</div>
        @enderror
        <label for="content">Observación:</label>
        <textarea cols="60" rows="4" class="form-control" minlength="1" required id="text" name="content" placeholder="Que quieres comentar en {{ $vacation->titulo }}...">{{ old('content') }}</textarea>
    </div>

    <div class="upper-space" style="padding-top: 16px;">
        <input class="btn btn-primary" type="submit" value="Añadir observación">
    </div>
    
</form>