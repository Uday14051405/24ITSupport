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
            <th>Login Time</th>
            <th>Logout Time</th>
            <th>Duration</th>
            <th>Device</th>
            <th>Platform</th>
            <th>Browser</th>
            <th>App Name</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $key => $row)
        <tr>
            <td>{{ $row->login_time }}</td>
            <td>{{ $row->logout_time }}</td>
            <td>{{ $row->login_duration }}</td>
            <td>
                {{ json_decode($row->data)->device ?? 'N/A' }}
            </td>
            <td>
                {{ json_decode($row->data)->platform ?? 'N/A' }}
            </td>
            <td>
                {{ json_decode($row->data)->browser ?? 'N/A' }}
            </td>
            <td>
                {{ json_decode($row->data)->app_name ?? 'N/A' }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
