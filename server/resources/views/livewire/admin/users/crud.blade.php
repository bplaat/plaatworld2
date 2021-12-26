<div class="container">
    <h2 class="title">@lang('admin/users.crud.header')</h2>

    <x-search-header :itemName="__('admin/users.crud.users')">
        <div class="buttons">
            <button class="button is-link" wire:click="$set('isCreating', true)" wire:loading.attr="disabled">@lang('admin/users.crud.create_user')</button>
        </div>

        <x-slot name="sorters">
            <option value="">@lang('admin/users.crud.username_asc')</option>
            <option value="username_desc">@lang('admin/users.crud.username_desc')</option>
            <option value="created_at_desc">@lang('admin/users.crud.created_at_desc')</option>
            <option value="created_at">@lang('admin/users.crud.created_at_asc')</option>
        </x-slot>
    </x-search-header>

    @if ($users->count() > 0)
        {{ $users->links() }}

        <div class="columns is-multiline">
            @foreach ($users as $user)
                <livewire:admin.users.item :user="$user" :wire:key="$user->id" />
            @endforeach
        </div>

        {{ $users->links() }}
    @else
        <p><i>@lang('admin/users.crud.empty')</i></p>
    @endif

    @if ($isCreating)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isCreating', false)"></div>

            <form wire:submit.prevent="createUser" class="modal-card">
                <div class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/users.crud.create_user')</p>
                    <button type="button" class="delete" wire:click="$set('isCreating', false)"></button>
                </div>

                <div class="modal-card-body">
                    <div class="columns">
                        <div class="column">
                            <div class="field">
                                <label class="label" for="username">@lang('admin/users.crud.username')</label>
                                <div class="control">
                                    <input class="input @error('user.username') is-danger @enderror" type="text" id="username"
                                        wire:model.defer="user.username" required>
                                </div>
                                @error('user.username') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="column">
                            <div class="field">
                                <label class="label" for="email">@lang('admin/users.crud.email')</label>
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
                                <label class="label" for="password">@lang('admin/users.crud.password')</label>
                                <div class="control">
                                    <input class="input @error('user._password') is-danger @enderror" type="password" id="password"
                                        wire:model.defer="user._password" required>
                                </div>
                                @error('user._password')
                                    <p class="help is-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="column">
                            <div class="field">
                                <label class="label" for="password_confirmation">@lang('admin/users.crud.password_confirmation')</label>
                                <div class="control">
                                    <input class="input @error('user.password_confirmation') is-danger @enderror" type="password" id="password_confirmation"
                                        wire:model.defer="user.password_confirmation" required>
                                </div>
                                @error('user.password_confirmation') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="field">
                        <label class="label" for="avatar">@lang('admin/users.crud.avatar')</label>
                        <div class="control">
                            <input class="input @error('avatar') is-danger @enderror" type="file" accept=".jpg,.jpeg,.png" id="avatar" wire:model="avatar">
                        </div>
                        @error('avatar')
                            <p class="help is-danger">{{ $message }}</p>
                        @else
                            <p class="help">@lang('admin/users.crud.avatar_help')</p>
                        @enderror
                    </div>

                    <div class="columns">
                        <div class="column">
                            <div class="field">
                                <label class="label" for="role">@lang('admin/users.crud.role')</label>
                                <div class="control">
                                    <div class="select is-fullwidth @error('user.role') is-danger @enderror">
                                        <select id="role" wire:model.defer="user.role">
                                            <option value="{{ App\Models\User::ROLE_NORMAL }}">@lang('admin/users.crud.role_normal')</option>
                                            <option value="{{ App\Models\User::ROLE_ADMIN }}">@lang('admin/users.crud.role_admin')</option>
                                        </select>
                                    </div>
                                </div>
                                @error('user.role') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="column">
                            <div class="field">
                                <label class="label" for="language">@lang('admin/users.crud.language')</label>
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
                                <label class="label" for="theme">@lang('admin/users.crud.theme')</label>
                                <div class="control">
                                    <div class="select is-fullwidth @error('user.theme') is-danger @enderror">
                                        <select id="theme" wire:model.defer="user.theme">
                                            <option value="{{ App\Models\User::THEME_LIGHT }}">@lang('admin/users.crud.theme_light')</option>
                                            <option value="{{ App\Models\User::THEME_DARK }}">@lang('admin/users.crud.theme_dark')</option>
                                        </select>
                                    </div>
                                </div>
                                @error('user.theme') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="field">
                        <label class="label" for="active">@lang('admin/users.crud.active')</label>
                        <label class="checkbox" for="active">
                            <input type="checkbox" id="active" wire:model.defer="user.active">
                            @lang('admin/users.crud.active_user')
                        </label>
                    </div>
                </div>

                <div class="modal-card-foot">
                    <button type="submit" class="button is-link">@lang('admin/users.crud.create_user')</button>
                    <button type="button" class="button" wire:click="$set('isCreating', false)" wire:loading.attr="disabled">@lang('admin/users.crud.cancel')</button>
                </div>
            </form>
        </div>
    @endif
</div>
