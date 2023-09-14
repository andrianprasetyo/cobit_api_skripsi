<table>
    <thead>
        <tr>
            <th>Domain/GAMO</th>
            <th>Target Organisasi</th>
            <th>Hasil Assesment</th>
            <th>GAP</th>
            <th>GAP Deskripsi</th>
            <th>Potensi Inisiatif</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
        <tr>
            <td>{{$item->kode}} - {{$item->ket}}</td>
            <td>{{$item->target_level}}</td>
            <td>{{$item->hasil_assesment}}</td>
            <td>{{$item->gap_minus}}</td>
            <td>{{$item->gap_deskripsi}}</td>
            <td>{{$item->potensi}}</td>
        </tr>
        @endforeach
    </tbody>
</table>
