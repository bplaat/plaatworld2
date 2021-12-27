<div class="column is-one-quarter">
    <div class="card">
        <div class="card-image">
            <div class="image is-square" style="background-image: url(/storage/avatars/{{ $user->avatar ?? 'default.png' }});"></div>

            <div class="card-image-tags">
                @if ($user->role == App\Models\User::ROLE_NORMAL)
                    <span class="tag is-success">{{ Str::upper(__('admin/users.item.role_normal')) }}</span>
                @endif

                @if ($user->role == App\Models\User::ROLE_ADMIN)
                    <span class="tag is-danger">{{ Str::upper(__('admin/users.item.role_admin')) }}</span>
                @endif

                @if (!$user->active)
                    <span class="tag is-warning">{{ Str::upper(__('admin/users.item.inactive')) }}</span>
                @endif
            </div>
        </div>

        <div class="card-content content">
            <h4 class="mb-0">{{ $user->username }}</h4>
        </div>

        <div class="card-footer">
            <a class="card-footer-item" wire:click.prevent="$set('isShowing', true)">@lang('admin/users.item.show')</a>
            <a class="card-footer-item" wire:click.prevent="$set('isEditing', true)">@lang('admin/users.item.edit')</a>
            @if ($user->id != Auth::id())
                <a class="card-footer-item has-text-danger" wire:click.prevent="hijackUser">@lang('admin/users.item.hijack')</a>
            @endif
            <a class="card-footer-item has-text-danger" wire:click.prevent="$set('isDeleting', true)">@lang('admin/users.item.delete')</a>
        </div>
    </div>

    @if ($isShowing)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isShowing', false)"></div>

            <div class="modal-card">
                <div class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/users.item.show_user')</p>
                    <button type="button" class="delete" wire:click="$set('isShowing', false)"></button>
                </div>

                <div class="modal-card-body content">
                    <h1 class="title is-spaced is-4">
                        {{ $user->username }}

                        <span class="is-pulled-right is-hidden-mobile">
                            @if ($user->role == App\Models\User::ROLE_NORMAL)
                                <span class="tag is-success">{{ Str::upper(__('admin/users.item.role_normal')) }}</span>
                            @endif

                            @if ($user->role == App\Models\User::ROLE_ADMIN)
                                <span class="tag is-danger">{{ Str::upper(__('admin/users.item.role_admin')) }}</span>
                            @endif

                            @if (!$user->active)
                                <span class="tag is-warning">{{ Str::upper(__('admin/users.item.inactive')) }}</span>
                            @endif
                        </span>
                    </h1>

                    <div class="box">
                        <h2 class="title is-spaced is-5">@lang('admin/users.item.inventory')</h2>

                        <x-user-inventory :user="$user" />

                        <form wire:submit.prevent="addItem">
                            <div class="field has-addons is-block-mobile">
                                <livewire:components.item-chooser name="add_item" inline="true" includeInactive="true" />

                                <div class="control" style="width: 100%;">
                                    <input class="input @error('addItemAmount') is-danger @enderror" type="number" step="1" placeholder="@lang('admin/users.item.add_amount')"
                                        wire:model.defer="addItemAmount" required>
                                </div>

                                <div class="control">
                                    <button class="button is-link" type="submit" style="width: 100%;">@lang('admin/users.item.add_item')</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($isEditing)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isEditing', false)"></div>

            <form wire:submit.prevent="editUser" class="modal-card">
                <div class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/users.item.edit_user')</p>
                    <button type="button" class="delete" wire:click="$set('isEditing', false)"></button>
                </div>

                <div class="modal-card-body">
                    <div class="columns">
                        <div class="column">
                            <div class="field">
                                <label class="label" for="username">@lang('admin/users.item.username')</label>
                                <div class="control">
                                    <input class="input @error('user.username') is-danger @enderror" type="text" id="username"
                                        wire:model.defer="user.username" required>
                                </div>
                                @error('user.username') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="column">
                            <div class="field">
                                <label class="label" for="email">@lang('admin/users.item.email')</label>
                                <div class="control">
                                    <input class="input @error('user.email') is-danger @enderror" type="email" id="email"
                                        wire:model.defer="user.email">
                                </div>
                                @error('user.email') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="columns">
                        <div class="column">
                            <div class="field">
                                <label class="label" for="password">@lang('admin/users.item.password')</label>
                                <div class="control">
                                    <input class="input @error('newPassword') is-danger @enderror" type="password" id="password" wire:model.defer="newPassword">
                                </div>
                                @error('newPassword')
                                    <p class="help is-danger">{{ $message }}</p>
                                @else
                                    <p class="help">@lang('admin/users.item.password_hint')</p>
                                @enderror
                            </div>
                        </div>

                        <div class="column">
                            <div class="field">
                                <label class="label" for="password_confirmation">@lang('admin/users.item.password_confirmation')</label>
                                <div class="control">
                                    <input class="input @error('newPasswordConfirmation') is-danger @enderror" type="password" id="password_confirmation" wire:model.defer="newPasswordConfirmation">
                                </div>
                                @error('newPasswordConfirmation') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="field">
                        <label class="label" for="avatar">@lang('admin/users.item.avatar')</label>
                        @if ($user->avatar != null)
                            <div class="box" style="width: 50%;">
                                <div class="image is-square is-rounded" style="background-image: url(/storage/avatars/{{ $user->avatar }});"></div>
                            </div>
                        @endif
                    </div>

                    <div class="field">
                        <div class="control">
                            <input class="input @error('avatar') is-danger @enderror" type="file" accept=".jpg,.jpeg,.png" id="avatar" wire:model="avatar">
                        </div>
                        @error('avatar')
                            <p class="help is-danger">{{ $message }}</p>
                        @else
                            <p class="help">@lang('admin/users.item.avatar_help')</p>
                        @enderror
                    </div>

                    @if ($user->avatar != null)
                        <div class="field">
                            <div class="control">
                                <button type="button" class="button is-danger" wire:click="deleteAvatar" wire:loading.attr="disabled">@lang('admin/users.item.delete_avatar')</button>
                            </div>
                        </div>
                    @endif

                    <div class="columns">
                        <div class="column">
                            <div class="field">
                                <label class="label" for="role">@lang('admin/users.item.role')</label>
                                <div class="control">
                                    <div class="select is-fullwidth @error('user.role') is-danger @enderror">
                                        <select id="role" wire:model.defer="user.role">
                                            <option value="{{ App\Models\User::ROLE_NORMAL }}">@lang('admin/users.item.role_normal')</option>
                                            <option value="{{ App\Models\User::ROLE_ADMIN }}">@lang('admin/users.item.role_admin')</option>
                                        </select>
                                    </div>
                                </div>
                                @error('user.role') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="column">
                            <div class="field">
                                <label class="label" for="language">@lang('admin/users.item.language')</label>
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
                                <label class="label" for="theme">@lang('admin/users.item.theme')</label>
                                <div class="control">
                                    <div class="select is-fullwidth @error('user.theme') is-danger @enderror">
                                        <select id="theme" wire:model.defer="user.theme">
                                            <option value="{{ App\Models\User::THEME_LIGHT }}">@lang('admin/users.item.theme_light')</option>
                                            <option value="{{ App\Models\User::THEME_DARK }}">@lang('admin/users.item.theme_dark')</option>
                                        </select>
                                    </div>
                                </div>
                                @error('user.theme') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="field">
                        <label class="label" for="active">@lang('admin/users.item.active')</label>
                        <label class="checkbox" for="active">
                            <input type="checkbox" id="active" wire:model.defer="user.active">
                            @lang('admin/users.item.active_user')
                        </label>
                    </div>
                </div>

                <div class="modal-card-foot">
                    <button type="submit" class="button is-link">@lang('admin/users.item.edit_user')</button>
                    <button type="button" class="button" wire:click="$set('isEditing', false)">@lang('admin/users.item.cancel')</button>
                </div>
            </form>
        </div>
    @endif

    @if ($isDeleting)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isDeleting', false)"></div>

            <div class="modal-card">
                <div class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/users.item.delete_user')</p>
                    <button type="button" class="delete" wire:click="$set('isDeleting', false)"></button>
                </div>

                <div class="modal-card-body">
                    <p>@lang('admin/users.item.delete_description')</p>
                </div>

                <div class="modal-card-foot">
                    <button class="button is-danger" wire:click="deleteUser()" wire:loading.attr="disabled">@lang('admin/users.item.delete_user')</button>
                    <button class="button" wire:click="$set('isDeleting', false)" wire:loading.attr="disabled">@lang('admin/users.item.cancel')</button>
                </div>
            </div>
        </div>
    @endif
</div>
