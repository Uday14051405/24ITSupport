<style>
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        border: 1px solid black; /* Adds border to all cells */
        padding: 8px;
        text-align: left;
    }
    th {
        background-color: #f2f2f2; /* Light grey background for headers */
    }
</style>

<table>
    <thead>
        <tr>
            <th>Serial No</th>
            <th>User</th>
            <th>Email</th>
            <th>Status</th>
            <th>Login Date</th>
            <th>Total Duration</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $key => $row)
        <tr>
            <td>{{ $loop->index + 1 }}</td>
            <td>{{ $row->first_name }} {{ $row->last_name }}</td>
            <td>{{ $row->email }}</td>
            <td>{{ strtoupper($row->user_status) }}</td>
            <td>{{ date('d M Y', strtotime($row->log_datetime)) }}</td>
            <td>{{ $row->total_duration }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
