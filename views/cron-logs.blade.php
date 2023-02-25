<table class="table is-bordered is-striped is-narrow is-hoverable">
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

        <th style="white-space: pre-wrap">
            Вывод
        </th>

        <th style="white-space: pre-wrap">
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
                {{ $log->id }}
            </td>
            <td>
                {{ $log->command }}
            </td>

            <td>
                {{ $log->description }}
            </td>

            <td>
                {{ $log->output }}
            </td>

            <td>
                {{ $log->errors }}
            </td>

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