<form class="container" wire:submit.prevent="register">
    <h2 class="title">@lang('auth.register.header')</h2>

    <div class="box">
        <div class="field">
            <label class="label" for="username">@lang('auth.register.username')</label>
            <div class="control">
                <input class="input @error('user.username') is-danger @enderror" type="text" id="username"
                    wire:model.defer="user.username" autofocus required>
            </div>
            @error('user.username') <p class="help is-danger">{{ $message }}</p> @enderror
        </div>

        <div class="field">
            <label class="label" for="email">@lang('auth.register.email')</label>
            <div class="control">
                <input class="input @error('user.email') is-danger @enderror" type="email" id="email"
                    wire:model.defer="user.email" autofocus required>
            </div>
            @error('user.email') <p class="help is-danger">{{ $message }}</p> @enderror
        </div>

        <div class="field">
            <label class="label" for="password">@lang('auth.register.password')</label>
            <div class="control">
                <input class="input @error('user._password') is-danger @enderror" type="password" id="password"
                    wire:model.defer="user._password" required>
            </div>
            @error('user._password') @if ($message != 'null') <p class="help is-danger">{{ $message }}</p> @endif @enderror
        </div>

        <div class="field">
            <label class="label" for="password_confirmation">@lang('auth.register.password_confirmation')</label>
            <div class="control">
                <input class="input @error('user.password_confirmation') is-danger @enderror" type="password" id="password_confirmation"
                    wire:model.defer="user.password_confirmation" required>
            </div>
            @error('user.password_confirmation') @if ($message != 'null') <p class="help is-danger">{{ $message }}</p> @endif @enderror
        </div>

        <div class="field">
            <div class="control">
                <button class="button is-link" type="submit">@lang('auth.register.register')</button>
            </div>
        </div>
    </div>
</form>
