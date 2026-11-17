<!--**********************************
            Footer start
        ***********************************-->
<div class="footer">
    <div class="copyright">
        <p>Copyright © Limpopay {{ date('Y') }}</p>
    </div>
</div>
<!--**********************************
            Footer end
        ***********************************-->

<!--**********************************
           Support ticket button start
        ***********************************-->

<!--**********************************
           Support ticket button end
        ***********************************-->


</div>
<!--**********************************
        Main wrapper end
    ***********************************-->
<form action="{{route('make.loan')}}" method="POST">
    @csrf
    <div class="modal fade" id="reqLoan">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Request Loan</h5>
                    <button type="button" class="close" data-bs-dismiss="modal"><span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="loan_response"></div>
                    <div class="mb-3">
                        <div class="input-group mb-3">

                            <input type="number" step="0.01" min="1" name="amount" class="form-control" placeholder="Amount ({{ Auth::user()->currency }})" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="input-group mb-3">

                            <input type="text" name="ssn" class="form-control" placeholder="SSN" autocomplete="off" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Request Now</button>
                </div>
            </div>
        </div>
    </div>
</form>
<!--**********************************
        Scripts
    ***********************************-->
<!-- Required vendors -->
<script src="{{asset('vendor/global/global.min.js')}}"></script>
<script src="{{asset('vendor/bootstrap-select/dist/js/bootstrap-select.min.js')}}"></script>
<script src="{{asset('vendor/chart.js/Chart.bundle.min.js')}}"></script>

<!-- Datatable -->
<script src="{{asset('vendor/datatables/js/jquery.dataTables.min.js')}}"></script>

<script src="{{asset('js/custom.min.js')}}"></script>
<script src="{{asset('js/deznav-init.js')}}"></script>
<script src="{{asset('js/demo.js')}}"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<!--<script src="js/styleSwitcher.js"></script>-->

<script>
    (function($) {
        var $table = $('#example5');
        // Skip DataTables when the table only holds the "no records" placeholder row.
        if ($table.length && $table.find('tbody td[colspan]').length === 0) {
            $table.DataTable({
                searching: false,
                paging: true,
                select: false,
                ordering: false,
                lengthChange: false
            });
        }
    })(jQuery);
</script>


</body>

</html>