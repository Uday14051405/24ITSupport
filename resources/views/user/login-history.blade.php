<x-master-layout>

    <head>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>

        <style>
            @media (min-width: 992px) {
                .modal-lg{
                    --bs-modal-width: 860px;
                }
            }
        </style>
    </head>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3 flex-wrap gap-3">
                            <h5 class="fw-bold">Login History</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row align-items-center justify-content-between gy-3">
                    <div class="col-md-6 col-lg-4 col-xl-5">
                        <div class="d-flex flex-wrap align-items-center gap-3">
                            <button type="button" 
                                class="btn btn-sm btn-primary ms-2" data-toggle="modal"
                                data-target="#Export">Export</button>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <div class="d-flex flex-column">
                            <div class="d-flex justify-content-end gap-2">
                                <div>
                                    <label for="datePicker" class="form-label">From Date</label>
                                    <input type="date" id="datePicker" class="form-control" style="width: 150px;" value="{{ date('Y-m-d') }}">
                                </div>
                                <div>
                                    <label for="datePicker2" class="form-label">To Date</label>
                                    <input type="date" id="datePicker2" class="form-control" style="width: 150px;" value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <div class="d-flex justify-content-end gap-2">
                            <div class="input-group input-group-search ms-2">
                                <span class="input-group-text" id="addon-wrapping"><i class="fas fa-search"></i></span>
                                <input type="text" id="searchBox" class="form-control dt-search"
                                    placeholder="Search by Name or Email..." aria-label="Search"
                                    aria-describedby="addon-wrapping">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table id="datatable" class="table table-striped border">
                                <thead>
                                    <tr>
                                        <th>Serial No</th>
                                        <th>User</th>
                                        <th>User Status</th>
                                        <th>Login Date</th>
                                        <th>Total Duration</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Login Details Modal -->
        <div class="modal fade" id="loginDetailsModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">User Login Details</h5>
                        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Data will be loaded dynamically -->
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="user_id" id="user_id" value="">
                        <input type="hidden" name="login_date" id="login_date" value="">
                        <button type="button" class="btn btn-primary export-details" data-toggle="modal"
                            data-target="#Export-Details">Export Details</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Export Modal -->
        <div class="modal fade" id="Export" tabindex="-1" role="dialog" aria-labelledby="exportModalTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exportModalTitle">Export Data</h5>
                        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"></span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <label>Select File Type</label>
                        <div class="btn-group btn-group-toggle d-flex flex-wrap export-type" data-toggle="buttons">
                            <label class="btn btn-outline-primary active">
                                <input type="radio" name="fileType" value="xlsx" /> XLSX
                            </label>
                            <label class="btn btn-outline-primary">
                                <input type="radio" name="fileType" value="csv" /> CSV
                            </label>
                            <label class="btn btn-outline-primary">
                                <input type="radio" name="fileType" value="pdf" checked /> PDF
                            </label>
                            <label class="btn btn-outline-primary active">
                                <input type="radio" name="fileType" value="html" /> HTML
                            </label>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="downloadButton">Export</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="Export-Details" tabindex="-1" role="dialog" aria-labelledby="exportModalTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exportModalTitle">Export Data</h5>
                        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"></span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <label>Select File Type</label>
                        <div class="btn-group btn-group-toggle d-flex flex-wrap export-type" data-toggle="buttons">
                            <label class="btn btn-outline-primary active">
                                <input type="radio" name="detailType" value="xlsx" /> XLSX
                            </label>
                            <label class="btn btn-outline-primary">
                                <input type="radio" name="detailType" value="csv" /> CSV
                            </label>
                            <label class="btn btn-outline-primary">
                                <input type="radio" name="detailType" value="pdf" checked /> PDF
                            </label>
                            <label class="btn btn-outline-primary active">
                                <input type="radio" name="detailType" value="html" /> HTML
                            </label>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="downloadDetails">Export</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            let table = $('#datatable').DataTable({
                serverSide: true,
                responsive: true,
                searching: false,
                dom: '<"row align-items-center"><"table-responsive my-3 mt-3 mb-2 pb-1" rt><"row align-items-center data_table_widgets" <"col-md-6" <"d-flex align-items-center flex-wrap gap-3" l i>><"col-md-6" p>><"clear">',
                ajax: {
                    "url": '{{ route('user.login-history.data') }}',
                    "type": "GET",
                    "data": function (d) {
                        d.search = $('#searchBox').val();  // Pass search term
                        d.date = $('#datePicker').val();   // Pass selected date
                        d.date2 = $('#datePicker2').val();
                    }
                },
                columns: [
                    {
                        data: null, name: 'id', render: function (data, type, row, meta) {
                            var pageInfo = $('#datatable').DataTable().page.info(); 
                            return pageInfo.start + meta.row + 1; // Maintain numbering across pages
                        }
                    },
                    { data: 'user', name: 'user' },
                    { data: 'user_status', name: 'user_status' },
                    { data: 'log_datetime', name: 'log_datetime' },
                    { data: 'total_duration', name: 'total_duration' }
                ],
                order: [[1, 'desc']]
            });

            // Trigger search on input change
            $('#searchBox').on('keyup', function () {
                table.draw();
            });

            // Refresh table when date is changed
            $('#datePicker').on('change', function () {
                table.draw();
            });
            $('#datePicker2').on('change', function () {
                table.draw();
            });
        });

        $(document).on('click', '.view-details', function (e) {
            e.preventDefault();
            let userId = $(this).data('userid');
            let date = $(this).data('date');

            document.getElementById("user_id").value = userId;
            document.getElementById("login_date").value = date;

            $.ajax({
                url: '{{ route("user.login-history.details") }}',
                type: 'GET',
                data: { user_id: userId, date: date },
                success: function (response) {
                    if (response.success) {
                        let tableHtml = `
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="table-dark">
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
                                    <tbody>`;

                        let today = new Date().toISOString().split('T')[0]; // Get today's date in YYYY-MM-DD format

                        response.data.forEach((activity, index) => {
                            let jsonData = activity.data ? JSON.parse(activity.data) : {};
                            let logoutTime = activity.logout_time;
                            let duration = activity.login_duration
                                ? new Date(activity.login_duration * 1000).toISOString().substr(11, 8)
                                : '-';

                            // If the date is today and it's the latest record (first in the list) with null logout_time
                            if (date === today && logoutTime === null) {
                                logoutTime = 'running';
                                duration = 'running';
                            }

                            tableHtml += `
                                <tr>
                                    <td>${activity.login_time}</td>
                                    <td>${logoutTime}</td>
                                    <td>${duration}</td>
                                    <td>${jsonData.device ?? '-'}</td>
                                    <td>${jsonData.platform ?? '-'}</td>
                                    <td>${jsonData.browser ?? '-'}</td>
                                    <td>${jsonData.app_name ?? '-'}</td>
                                </tr>`;
                        });

                        tableHtml += `</tbody></table></div>`;

                        $('#loginDetailsModal .modal-body').html(tableHtml);
                        $('#loginDetailsModal').modal('show');
                    }
                }
            });
        });


        $(document).ready(function () {
            $('#downloadButton').on('click', function () {
                let fileType = $('input[name="fileType"]:checked').val();
                let selectedDate = $('#datePicker').val(); // Get selected date
                let selectedDate2 = $('#datePicker2').val(); // Get selected date
                window.location.href = "{{ route('user.login-history.export') }}?fileType=" + fileType + "&date=" + selectedDate + "&date2=" + selectedDate2;
            });
        });


        $(document).ready(function () {
            $('#downloadDetails').on('click', function () {
                let fileType = $('input[name="detailType"]:checked').val();
                let user_id = $('input[name="user_id"]').val();
                let login_date = $('input[name="login_date"]').val();
                window.location.href = "{{ route('user.login-history-details.export') }}?fileType=" + fileType + "&user_id=" + user_id + "&login_date=" + login_date;
            });
        });
    </script>
</x-master-layout>