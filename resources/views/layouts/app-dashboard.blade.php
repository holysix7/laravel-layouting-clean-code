@extends('layouts.app')

@section('style')
<link rel="stylesheet" href="{{ url('adminlte/plugins/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ url('adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">

<link rel="stylesheet" href="{{ url('adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ url('adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ url('adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endsection

@section('content')

<section class="content">
  <div class="container-fluid">
    @include('content-header')
    <div class="row">
      <div class="card-body col-12">
        <div class="row">
          <div class="col-md-9">
            <div class="card">
              <div class="card-body row">
                <div class="col-md-6">
                  <div class="row mb-3">
                    <div class="col-md-6">
                      <h4>Summary </h4>
                      <h5 class="text-muted">Ringkasan transaksi </h5>
                    </div>
                  </div>
                  <div class="row child-item d-flex align-items-center" style="margin-left: auto;">
                    <h5 class="text-muted mr-2"><i class="mdi mdi-checkbox-blank-circle ringkasan-transaksi-blue pr-2"></i>Total Transaksi </h5>
                    <h4 id="total_transaction_amount_value">0</h4>
                  </div>
                  <div class="row child-item d-flex align-items-center" style="margin-left: auto;">
                    <h5 class="text-muted mr-2"><i class="mdi mdi-checkbox-blank-circle ringkasan-transaksi-yellow pr-2"></i>Transaksi </h5>
                    <h4 id="total_transaction_count_value">0</h4>
                  </div>
                  <div class="row child-item" style="margin-left: auto;">
                    <button class="btn btn-secondary force-accent-blue d-print-none" onclick="javascript:print()">Report <i class="mdi mdi-download"></i></button>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="row">
                    <div class="col-md-12 mb-4">
                      <input type="month" id="dashboard-date" class="form-control" placeholder="Periode" value="{{ $date ?? '' }}">
                    </div>
                    <div class="col-md-12">
                      <div class="card p-2 py-3">
                        <h4 class="text-center text-md">Trend Transaksi</h4>
                        <canvas id="lineChart"></canvas>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row mt-4 col-md-12">
                  <div class="col-md-3">
                    <div class="row">
                      <div class="col-md-4">
                        <i class="mdi mdi-credit-card icon-merchant-dashboard-blue"></i>
                      </div>
                      <div class="col-md-8" style="margin-top: 6px;">
                        <div class="row ml-1">
                          <h5 class="text-muted">
                            Pendapatan
                          </h5>
                        </div>
                        <div class="row ml-1">
                          <h5 id="balance_value" class="value-merchant-dashboard">
                            0
                          </h5>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="row">
                      <div class="col-md-4">
                        <i class="mdi mdi-alert icon-merchant-dashboard-yellow"></i>
                      </div>
                      <div class="col-md-8" style="margin-top: 6px;">
                        <div class="row ml-1">
                          <h5 class="text-muted">
                            Suspect FDS
                          </h5>
                        </div>
                        <div class="row ml-1">
                          <h5 id="suspected_fds_value" class="value-merchant-dashboard">
                            0 Detected
                          </h5>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3 h-100">
                    <div class="row">
                      <div class="col-md-4">
                        <i class="mdi mdi-store icon-merchant-dashboard-purple"></i>
                      </div>
                      <div class="col-md-8" style="margin-top: 6px;">
                        <div class="row ml-1">
                          <h5 class="text-muted">
                            Merchant
                          </h5>
                        </div>
                        <div class="row ml-1">
                          <h5 id="merchant_value" class="value-merchant-dashboard">
                            0 Total
                          </h5>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="row">
                      <div class="col-md-4">
                        <i class="mdi mdi-cash icon-merchant-dashboard-lime"></i>
                      </div>
                      <div class="col-md-8" style="margin-top: 6px;">
                        <div class="row ml-1">
                          <h5 class="text-muted">
                            Penarikan Dana
                          </h5>
                        </div>
                        <div class="row ml-1">
                          <h5 id="withdrawl_value" class="value-merchant-dashboard">
                            Rp 0
                          </h5>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3 pl-0 pb-3">
            <div class="card h-100">
              <div class="card-body">
                <div class="row">
                  <div class="col-md-10">
                    <h5 style="font-size: 18px">Payment Channel</h5>
                    <h5 class="text-muted" id="top_payment_channel_date"></h5>
                  </div>
                  <div class="col-md-2 d-flex justify-content-end">
      
                  </div>
                </div>
                <canvas id="pieChart"></canvas>
                <div class="d-block mt-3" id="top_payment_channel_value"></div>
              </div>
            </div>
          </div>
        </div>
      
        <div class="row mt-2">
          <div class="col-md-6">
            <div class="card col-sm-12 card-400">
              <div class="card-body">
                <div class="row">
                  <div class="col-md-10">
                    <h5 style="font-size: 18px">Top 5 Merchant Transactions</h5>
                    <h5 class="text-muted" id="top_transaction_date"></h5>
                  </div>
                  <div class="col-md-2 d-flex justify-content-end">
                    <!-- <a style="font-size: 20px;"><i class="mdi mdi-dots-horizontal"></i></a> -->
                  </div>
                </div>
                <div id="top_transaction_value"></div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card col-sm-12 card-400">
              <div class="card-body">
                <div class="row">
                  <div class="col-md-10">
                    <h5 style="font-size: 18px">Top 10 Transaction by Customers</h5>
                    <h5 class="text-muted" id="top_customer_transaction_date"></h5>
                  </div>
                  <div class="col-md-2 d-flex justify-content-end">
                    <!-- <a style="font-size: 20px;"><i class="mdi mdi-dots-horizontal"></i></a> -->
                  </div>
                </div>
                <div class="row mt-4">
                  <div class="col-md-12 dashboard-merchant">
                    <table class="table table-responsive table-striped">
                      <thead style="background: #393F80; color: white;">
                        <tr>
                          <th style="width: 5%">No</th>
                          <th class="text-left" style="width: 50%">Name</th>
                          <th class="text-center">Amount</th>
                        </tr>
                      </thead>
                      <tbody id="top_customer_transaction_value"></tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

@section('script')
<script src="{{ url('adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ url('adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ url('adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ url('adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ url('adminlte/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ url('adminlte/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ url('adminlte/plugins/jszip/jszip.min.js') }}"></script>
<script src="{{ url('adminlte/plugins/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ url('adminlte/plugins/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ url('adminlte/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ url('adminlte/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ url('adminlte/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
<script src="{{ url('adminlte/plugins/select2/js/select2.full.min.js') }} "></script>
<script src="{{ url('js/utils.js') }} "></script>
@endsection