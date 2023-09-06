<table border="1">
    <thead>
        <tr>
            <th rowspan="3">NO</th>
            <th rowspan="3">Nama</th>
            <th rowspan="3">Jabatan</th>
            <th rowspan="3">Divisi</th>
            @foreach ($header as $item)
                <th colspan="2">{{$item->sorting}}.{{$item->pertanyaan}}</th>
            @endforeach
        </tr>
        <tr>
            @foreach ($header as $item)
                <th colspan="2">{{$item->nama}}</th>
            @endforeach
        </tr>
        <tr>
            @foreach ($header as $item)
                <th>jawaban</th>
                <th>bobot</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($hasil as $key=>$item)
            @php
                $jawaban=json_decode($item->jawaban_quesioner);
            @endphp
            <tr>
                <td>{{++$key}}</td>
                <td>{{$item->nama}}</td>
                <td>{{$item->jabatan->nama}}</td>
                <td>{{$item->divisi->nama}}</td>
                @foreach ($jawaban as $j)
                    @if ($j->jawaban->jenis=='pilgan')
                        <td>{{$j->jawaban->jawaban}}</td>
                        <td>{{$j->jawaban->bobot}}</td>
                    @else
                        <td colspan="2">{{$j->jawaban->bobot}}</td>
                    @endif
                @endforeach
                {{-- <td>{{$item->jawaban_quesioner}}</td> --}}
            </tr>
        @endforeach
    </tbody>
</table>
