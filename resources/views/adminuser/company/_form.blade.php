<div id="modal-add-company" class="modal fade" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="titleModal">
                	Create Company
                </h4>
            </div>
            <div class="modal-body">
            	<form action="{{ route('company.save-company') }}" method="POST">
            		@csrf
            		<input type="hidden" name="id" id="id">
            		<div class="form-group">
            			<label>Company Name <span class="text-danger">*</span></label>
            			<input required type="text" name="company_name" id="company_name" class="form-control" placeholder="Enter company name">
            		</div>
            		<div class="form-group">
            			<label>Phone Number</label>
                		<div class="input-group">
						    <span class="input-group-addon"><i class="fa fa-phone"></i></span>
						    <input type="text" id="company_phone" name="company_phone" class="form-control" placeholder="8123456789">
						</div>
					</div>
            		<div class="form-group">
            			<label>Company Webiste</label>
            			<input type="text" name="company_website" id="company_website" class="form-control" placeholder="https:://">
            		</div>
            		<div class="form-group">
            			<label>Address</label>
            			<textarea class="form-control" name="company_address" id="company_address" height="50"></textarea>
            		</div>
            		<div class="form-group">
            			<label>City</label>
            			<input type="text" name="company_city" id="company_city" class="form-control">
            		</div>
            		<div class="form-group">
            			<label>Province</label>
            			<input type="text" name="company_province" id="company_province" class="form-control">
            		</div>
            		<div class="form-group">
						<label>Country</label>
						<select name="company_country" id="company_country" class="form-control">
							<option value="">- select country -</option>
							<option value="indonesia">Indonesia</option>
						</select>
					</div>
            		<div class="pull-right">
            			<button type="button" data-dismiss="modal" class="btn btn-default" style="border-radius: 5px;">
            				Close
            			</button>
            			<button type="submit" class="btn btn-primary" style="border-radius: 5px;">
            				Create
            			</button>
            		</div>
            	</form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#tableCompany').dataTable();
        });

        function getDetailCompanies(element) {
            var title = $(element).data('title');
            var query = $(element).data('query');
            
            $("#titleModal").html(title);

            $("#id").val(query.id);
            $("#company_name").val(query.company_name);
            $("#company_phone").val(query.company_phone);
            $("#company_website").val(query.company_website);
            $("#company_address").val(query.company_address);
            $("#company_city").val(query.company_city);
            $("#company_province").val(query.company_province);
            $("#company_country").val(query.company_country).trigger('change');
        }

        function hideNotification() {
        setTimeout(function() {
            $('#notification').fadeOut();
            }, 2000);
        };

        hideNotification();
    </script>
@endpush