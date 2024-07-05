<table border="1">
    <thead>
        <tr>
            <th rowspan="3">NO</th>
            <th rowspan="3">Nama</th>
            <th rowspan="3">Jabatan</th>
            <th rowspan="3">Divisi</th>
            @foreach ($header as $item)
                <th colspan="2">{{$item->sorting}}.{{strip_tags($item->pertanyaan)}}</th>
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
                <td>{{$item->nama_responden}}</td>
                <td>{{$item->nama_jabatan}}</td>
                <td>{{$item->nama_divisi}}</td>
                @foreach ($jawaban as $j)
                    @if ($j->jawaban->jenis=='pilgan')
                        <td>{{$j->jawaban->jawaban}}</td>
                        <td>{{$j->jawaban->bobot}}</td>
                    @else
                        @if ($j->jawaban->bobot)
                        <td></td>
                        <td>{{$j->jawaban->bobot}}</td>
                        @else
                        <td></td>
                        <td></td>
                        @endif
                    @endif
                @endforeach
                {{-- <td>{{$item->jawaban_quesioner}}</td> --}}
            </tr>
        @endforeach
    </tbody>
</table>
