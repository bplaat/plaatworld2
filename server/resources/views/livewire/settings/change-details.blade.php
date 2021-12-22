<div class="my-5">
    @if ($isChanged)
        <div class="notification is-success">
            <button class="delete" wire:click="$set('isChanged', false)"></button>
            <p>@lang('settings.change_details.success_message')</p>
        </div>
    @endif

    <form class="box" wire:submit.prevent="changeDetails">
        <h2 class="title is-4">@lang('settings.change_details.header')</h2>

        <div class="columns">
            <div class="column">
                <div class="field">
                    <label class="label" for="username">@lang('settings.change_details.username')</label>
                    <div class="control">
                        <input class="input @error('user.username') is-danger @enderror" type="text" id="username"
                            wire:model.defer="user.username" required>
                    </div>
                    @error('user.username') <p class="help is-danger">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="column">
                <div class="field">
                    <label class="label" for="email">@lang('settings.change_details.email')</label>
                    <div class="control">
                        <input class="input @error('user.email') is-danger @enderror" type="email" id="email" wire:model.defer="user.email" required>
                    </div>
                    @error('user.email') <p class="help is-danger">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="columns">
            <div class="column">
                <div class="field">
                    <label class="label" for="language">@lang('settings.change_details.language')</label>
                    <div class="control">
                        <div class="select is-fullwidth @error('user.language') is-danger @enderror">
                            <select id="language" wire:model.defer="user.language">
                                <option value="{{ App\Models\User::LANGUAGE_ENGLISH }}">English</option>
                            </select>
                        </div>
                    </div>
                    @error('user.language') <p class="help is-danger">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="column">
                <div class="field">
                    <label class="label" for="theme">@lang('settings.change_details.theme')</label>
                    <div class="control">
                        <div class="select is-fullwidth @error('user.theme') is-danger @enderror">
                            <select id="theme" wire:model.defer="user.theme">
                                <option value="{{ App\Models\User::THEME_LIGHT }}">@lang('settings.change_details.theme_light')</option>
                                <option value="{{ App\Models\User::THEME_DARK }}">@lang('settings.change_details.theme_dark')</option>
                            </select>
                        </div>
                    </div>
                    @error('user.theme') <p class="help is-danger">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="field">
            <div class="control">
                <button class="button is-link" type="submit">@lang('settings.change_details.button')</button>
            </div>
        </div>
    </form>
</div>
