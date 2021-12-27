<table class="table is-fullwidth">
    <tbody>
        @for ($y = 0; $y < 3; $y++)
            <tr>
                @for ($x = 0; $x < 10; $x++)
                    <td>
                        @php
                            $userItem = $user->items->first(fn ($userItem) => $userItem->pivot->position_x == $x && $userItem->pivot->position_y == $y + 1);
                        @endphp
                        @if ($userItem != null)
                            <div class="image is-medium" title="{{ $userItem->name }}" style="background-image: url(/storage/textures/{{ $userItem->texture != null && $userItem->texture->image != null ? $userItem->texture->image : 'default.png' }});">
                                <div style="position: absolute; right: 0; bottom: 0; color: #fff; line-height: 1;">{{ $userItem->pivot->amount }}</div>
                            </div>
                        @else
                            <div class="image is-medium has-background-grey-lighter"></div>
                        @endif
                    </td>
                @endfor
            </tr>
        @endfor
    </tbody>
    <tfoot>
        <tr>
            @for ($x = 0; $x < 10; $x++)
                <td>
                    @php
                        $userItem = $user->items->first(fn ($userItem) => $userItem->pivot->position_x == $x && $userItem->pivot->position_y == 0);
                    @endphp
                    @if ($userItem != null)
                        <div class="image is-medium" title="{{ $userItem->name }}" style="background-image: url(/storage/textures/{{ $userItem->texture != null && $userItem->texture->image != null ? $userItem->texture->image : 'default.png' }});">
                            <div style="position: absolute; right: 0; bottom: 0; color: #fff; line-height: 1;">{{ $userItem->pivot->amount }}</div>
                        </div>
                    @else
                        <div class="image is-medium has-background-grey-lighter"></div>
                    @endif
                </td>
            @endfor
        </tr>
    </tfoot>
</table>
