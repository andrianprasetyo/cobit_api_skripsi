<table>
    <head>
        <tr>
            <th>Pertanyaan</th>
            <th>Urutan</th>
        </tr>
    </head>
    <tbody>
        @foreach($data as $item)
        <tr>
            <td>{{$item->pertanyaan}}</td>
            <td>{{$item->sorting}}</td>
        </tr>
        @endforeach
    </tbody>
</table>
