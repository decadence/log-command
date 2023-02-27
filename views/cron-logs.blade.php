<table>
    <thead>
    <tr>
        <th>
            ID
        </th>
        <th>
            Имя команды
        </th>

        <th>
            Описание
        </th>

        <th>
            Вывод
        </th>

        <th>
            Ошибки
        </th>

        <th>
            Время выполнения (секунды)
        </th>

        <th>
            Время первого сообщения
        </th>

        <th>
            Время последнего сообщения
        </th>

    </tr>

    </thead>
    <tbody>
    @foreach($logs as $log)
        <tr>
            <td>
                <a href="{{ route("settings.logs", $log) }}">
                    {{ $log->id }}
                </a>
            </td>
            <td>
                {{ $log->command }}
            </td>

            <td>
                {{ $log->description }}
            </td>

            {{-- отображаем лог только на деталке и без лишних пробелов --}}
            <td style="white-space: pre-wrap">@if($logs->count() === 1){{ $log->output }}@endif</td>

            <td style="white-space: pre-wrap">@if($logs->count() === 1){{ $log->errors }}@endif</td>

            <td>
                {{ $log->run_seconds }}
            </td>

            <td>
                {{ $log->created_at }}
            </td>

            <td>
                {{ $log->updated_at }}
            </td>
        </tr>

    @endforeach
    </tbody>
</table>
