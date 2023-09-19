<table>
    <thead>
        <tr>
            <th rowspan="2">Domain/GAMO</th>
            <th rowspan="2">Hasil Assesment</th>
            <th rowspan="1" colspan="4">Level</th>
        </tr>
        <tr>
            @foreach ($data['level'] as $level)
                <th>{{$level}}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($data['list'] as $item_list)
        <tr>
            <td>{{$item_list['kode']}} - {{$item_list['ket']}}</td>
            <td>{{$item_list['total']}}</td>

            @foreach ($item_list['level'] as $item_level)
                <td>{{$item_level['label']}}</td>
            @endforeach
        </tr>
        @endforeach
    </tbody>
</table>
