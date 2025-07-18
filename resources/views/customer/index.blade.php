<x-master-layout>

    <head>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    </head>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3 flex-wrap gap-3">
                            <h5 class="fw-bold">{{ $pageTitle ?? trans('messages.list') }}</h5>
                            @if ($list_status !== 'unverified')
                            @if ($auth_user->can('user add'))
                            <a href="{{ route('user.create') }}" class=" float-end me-1 btn btn-sm btn-primary"><i
                                    class="fa fa-plus-circle"></i>
                                {{ __('messages.add_form_title', ['form' => __('messages.user')]) }}</a>
                            @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="row justify-content-between gy-3">
                    <div class="col-md-3">
                        <div class="col-md-12">
                            <form action="{{ route('user.bulk-action') }}" id="quick-action-form"
                                class="form-disabled d-flex gap-3 align-items-center">
                                @csrf
                                <select name="action_type" class="form-control select2" id="quick-action-type"
                                    style="width:100%" disabled>
                                    <option value="">{{ __('messages.no_action') }}</option>
                                    <option value="change-status">{{ __('messages.status') }}</option>
                                    <option value="delete">{{ __('messages.delete') }}</option>
                                    <option value="restore">{{ __('messages.restore') }}</option>
                                    <option value="permanently-delete">{{ __('messages.permanent_dlt') }}</option>
                                </select>

                                <div class="select-status d-none quick-action-field" id="change-status-action"
                                    style="width:100%">
                                    <select name="status" class="form-control select2" id="status"
                                        style="width:100%">
                                        <option value="1">{{ __('messages.active') }}</option>
                                        <option value="0">{{ __('messages.inactive') }}</option>
                                    </select>
                                </div>
                                <button id="quick-action-apply" class="btn btn-primary" data-ajax="true"
                                    data--submit="{{ route('user.bulk-action') }}" data-datatable="reload"
                                    data-confirmation='true' data-title="{{ __('user', ['form' => __('user')]) }}"
                                    title="{{ __('user', ['form' => __('user')]) }}"
                                    data-message='{{ __('Do you want to perform this action?') }}'
                                    disabled>{{ __('messages.apply') }}</button>
                        </div>

                        </form>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group datatable-filter">
                            <!-- <label class="form-label" for="date_range">Date Range</label> -->
                            <input type="text" id="datepicker1" class="form-control flatpickr flatpickr-input active" placeholder="Select Date Range" readonly="readonly">
                        </div>
                    </div>
                    <div class="col-md-6">

                        <div class="d-flex align-items-center gap-3 justify-content-end">
                            <div class="d-flex justify-content-end gap-3">

                                <div class="datatable-filter ml-auto">
                                    <select name="column_status" id="column_status" class="select2 form-control"
                                        data-filter="select" style="width: 100%">
                                        <option value="">{{ __('messages.all') }}</option>
                                        <option value="0" {{ $filter['status'] == '0' ? 'selected' : '' }}>
                                            {{ __('messages.inactive') }}
                                        </option>
                                        <option value="1" {{ $filter['status'] == '1' ? 'selected' : '' }}>
                                            {{ __('messages.active') }}
                                        </option>
                                    </select>
                                </div>
                                <div class="input-group input-group-search ms-2">
                                    <span class="input-group-text" id="addon-wrapping"><i
                                            class="fas fa-search"></i></span>
                                    <input type="text" class="form-control dt-search" placeholder="Search..."
                                        aria-label="Search" aria-describedby="addon-wrapping"
                                        aria-controls="dataTableBuilder">
                                </div>



                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="datatable" class="table table-striped border">
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        let selectedFilters = {
            booking_status: [],
            payment_status: [],
            payment_type: [],
            advance_paid: [],
            date_range: ''
        };
        document.addEventListener('DOMContentLoaded', (event) => {

            window.renderedDataTable = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                responsive: true,
                dom: '<"row align-items-center"><"table-responsive my-3 mt-3 mb-2 pb-1" rt><"row align-items-center data_table_widgets" <"col-md-6" <"d-flex align-items-center flex-wrap gap-3" l i>><"col-md-6" p>><"clear">',
                ajax: {
                    "type": "GET",
                    "url": '{{ route('user.index_data',['list_status ' => $list_status]) }}',
                    "data": function(d) {
                        d.search = {
                            value: $('.dt-search').val()
                        };
                        d.filter = {
                            column_status: $('#column_status').val(),
                            date_range: selectedFilters.date_range || ''
                        }
                    },
                },
                columns: [{
                        name: 'check',
                        data: 'check',
                        title: '<input type="checkbox" class="form-check-input" name="select_all_table" id="select-all-table" data-type="user" onclick="selectAllTable(this)">',
                        exportable: false,
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at',
                        title: "{{ __('product.lbl_update_at') }}",
                        orderable: true,
                        visible: false,
                    },
                    {
                        data: 'display_name',
                        name: 'display_name',
                        title: "{{ __('messages.name') }}"
                    },
                    {
                        data: 'sso_login',
                        name: 'sso_login',
                        title: "{{ __('messages.sso_login') }}",
                        orderable: false,
                        searchable: false

                    },
                    @if($list_status == 'all') {
                        data: 'user_type',
                        name: 'user_type',
                        title: "{{ __('messages.user_type') }}"
                    },
                    @endif {
                        data: 'created_at',
                        name: 'created_at',
                        title: "{{ __('messages.joining_date') }}"
                    },
                    {
                        data: 'contact_number',
                        name: 'contact_number',
                        title: "{{ __('messages.contact_number') }}",
                        orderable: false,
                    },

                    {
                        data: 'address',
                        name: 'address',
                        title: "{{ __('messages.address') }}",
                        orderable: false,
                    },
                    @if($list_status !== 'all' && $list_status !== 'unverified') {
                        data: 'wallet',
                        name: 'wallet',
                        title: "{{ __('messages.wallet_amt') }}",
                        orderable: false,
                    },
                    @endif {
                        data: 'status',
                        name: 'status',
                        title: "{{ __('messages.status') }}"
                    },
                    @if($list_status == 'unverified') {
                        data: 'is_email_verified',
                        name: 'is_email_verified',
                        title: "{{ __('messages.is_email_verified') }}",
                        orderable: false,
                    }
                    @endif
                    @if($list_status !== 'unverified') {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        title: "{{ __('messages.action') }}"
                    }
                    @endif

                ],
                order: [
                    [1, 'desc']
                ],
                language: {
                    processing: "{{ __('messages.processing') }}" // Set your custom processing text
                }
            });
        });

        function resetQuickAction() {
            const actionValue = $('#quick-action-type').val();
            console.log(actionValue)
            if (actionValue != '') {
                $('#quick-action-apply').removeAttr('disabled');

                if (actionValue == 'change-status') {
                    $('.quick-action-field').addClass('d-none');
                    $('#change-status-action').removeClass('d-none');
                } else {
                    $('.quick-action-field').addClass('d-none');
                }
            } else {
                $('#quick-action-apply').attr('disabled', true);
                $('.quick-action-field').addClass('d-none');
            }
        }

        $('#quick-action-type').change(function() {
            resetQuickAction()
        });

        $(document).on('update_quick_action', function() {

        })

        $(document).on('click', '[data-ajax="true"]', function(e) {
            e.preventDefault();
            const button = $(this);
            const confirmation = button.data('confirmation');

            if (confirmation === 'true') {
                const message = button.data('message');
                if (confirm(message)) {
                    const submitUrl = button.data('submit');
                    const form = button.closest('form');
                    form.attr('action', submitUrl);
                    form.submit();
                }
            } else {
                const submitUrl = button.data('submit');
                const form = button.closest('form');
                form.attr('action', submitUrl);
                form.submit();
            }
        });



        document.addEventListener('DOMContentLoaded', function() {
            $("#datepicker1").flatpickr({
                mode: "range",
                dateFormat: "Y-m-d",
                onChange: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length === 2 || selectedDates.length === 1) {
                        selectedFilters.date_range = dateStr;
                        $('#datatable').DataTable().ajax.reload();
                        // updateTotalEarnings();
                    }
                }
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
</x-master-layout>